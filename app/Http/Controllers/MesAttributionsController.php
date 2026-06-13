<?php

namespace App\Http\Controllers;

use App\Models\Signataire;
use App\Models\PendingCommande;
use App\Models\CommandeProduct;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;
use App\Models\AdresseLivraison;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasserCommandeController;
use GuzzleHttp\Exception\RequestException;
use App\Models\EntrepriseDomain;
use App\Models\Support;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MesAttributionsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $userAuth = Auth::user();
        $routeName = Route::currentRouteName();

        $type = match ($routeName) {
            'mon-equipe' => 'Personne',
            'mes-salles' => 'salle',
            default => '',
        };

        $mes_attributions = User::with(['commandeProducts', 'tags'])
            ->where('entreprise_id', $userAuth->entreprise_id)
            ->where('type', $type)
            ->get();

        $licencesAvailable = CommandeProduct::select('id', 'name', 'image_principale', 'fournisseur', 'reference_id')
            ->where('entreprise_id', $userAuth->entreprise_id)
            ->where('categorie', 'licences')
            ->where('status', 'active')
            ->whereNull('user_attributed_id')
            ->get()
            ->groupBy('name')
            ->map(fn($items, $key) => [
                'id' => $items->first()->id,
                'name' => $key,
                'image_principale' => $items->first()->image_principale,
                'fournisseur' => $items->first()->fournisseur,
                'reference_id' => $items->first()->reference_id,
                'total' => $items->count(),
            ])->values();

        $equipementsAvailable = CommandeProduct::select('id', 'name', 'image_principale', 'numero_unique')
            ->where('entreprise_id', $userAuth->entreprise_id)
            ->where('categorie', '!=', 'licences')
            ->whereNull('user_attributed_id')
            ->get()
            ->groupBy('name')
            ->map(fn($items, $key) => [
                'id' => $items->first()->id,
                'name' => $key,
                'image_principale' => $items->first()->image_principale,
                'numero_unique' => $items->first()->numero_unique,
                'total' => $items->count(),
            ])->values();

        $collaborateurs = User::where('entreprise_id', $userAuth->entreprise_id)
            ->whereNotNull('role')
            ->get();

        $signataires = Signataire::where('entreprise_id', $userAuth->entreprise_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $domains = EntrepriseDomain::where('entreprise_id', $userAuth->entreprise_id)
            ->select('domain', 'tenant')
            ->get()
            ->groupBy('tenant')
            ->map(function ($items) {
                return $items->pluck('domain')->toArray();
            })
            ->toArray();

        $currentDomain = EntrepriseDomain::where('entreprise_id', $userAuth->entreprise_id)
            ->select('domain', 'tenant')
            ->where('domain', Str::after($userAuth->email, '@'))
            ->first();

        if (!$currentDomain) {
            $currentDomain = EntrepriseDomain::where('entreprise_id', $userAuth->entreprise_id)
                ->select('domain', 'tenant')
                ->first();
        }

        $licencesMarketPlace = Product::where('categorie', 'licences')->where('type_licence', 'Mensuel')->get();

        $licence_google = CommandeProduct::where('entreprise_id', $userAuth->entreprise_id)
            ->where('reference_id', 'like', '%GoogleWorkspace%')
            ->select('name', 'slug')
            ->first();
        if ($licence_google) {
            $licencesMarketPlace = $licencesMarketPlace->filter(function ($licence) use ($licence_google) {
                return !str_contains($licence->reference_id, 'GoogleWorkspace') || $licence->name === $licence_google;
            });
        }

        return Inertia::render('MesAttributions/Index', [
            'mes_attributions' =>  $mes_attributions,
            'adresses' =>  AdresseLivraison::where('entreprise_id', $userAuth->entreprise_id)->get(),
            'tags' => Tag::where('entreprise_id', $userAuth->entreprise_id)->get(),
            'licencesAvailable' => $licencesAvailable,
            'licencesMarketPlace' => $licencesMarketPlace,
            'licenceGoogleRef' => $licence_google,
            'equipementsAvailable' => $equipementsAvailable,
            'equipementsMarketPlace' => Product::where('categorie', '!=', 'licences')->get(),
            'entrepriseInfo' => User::with('entreprise')->find($userAuth->id),
            'domains' => $domains,
            'currentDomain' => $currentDomain,
            'collaborateurs' => $collaborateurs,
            'selectedSignataire' => $signataires->first(),
            'signataires' => $signataires
        ]);
    }
    public function attribution($attribution_id = null)
    {
        $attribution = User::with(['commandeProducts', 'tags'])->where('id', $attribution_id)->first();
        if ($attribution == null) {
            return redirect()->back();
        }

        $user = Auth::user();
        $licencesNames = $attribution->commandeProducts->where('sous_categorie', 'licences')->pluck('name')->unique()->toArray();

        $equipement_available = CommandeProduct::with(['userAttributed', 'userAttributed.tags'])
            ->where('entreprise_id', $user->entreprise_id)
            ->where('status', '!=', 'pending')
            ->where('sous_categorie', '!=', 'licences')
            ->whereNull('user_attributed_id')
            ->get();

        $licence_available = CommandeProduct::with(['userAttributed', 'userAttributed.tags'])
            ->where('entreprise_id', $user->entreprise_id)
            ->whereNotIn('name', $licencesNames)
            ->where('status', 'active')
            ->whereNull('user_attributed_id')
            ->where('fournisseur', $attribution->tenant_name)
            ->whereNotNull('guid')
            ->get();

        $licences_hold = CommandeProduct::where('entreprise_id', $user->entreprise_id)
            ->where('commande_status', 'ON_HOLD')
            ->where('reference_id', 'like', '%GoogleWorkspace%')
            ->first();

        $domains = EntrepriseDomain::where('entreprise_id', $user->entreprise_id)
            ->select('domain', 'tenant')
            ->get()
            ->groupBy('tenant')
            ->map(function ($items) {
                return $items->pluck('domain')->toArray();
            })
            ->toArray();

        $currentDomain = EntrepriseDomain::where('entreprise_id', $user->entreprise_id)
            ->select('domain', 'tenant')
            ->where('domain', Str::after($user->email, '@'))
            ->first();

        $userTagIds = $attribution->tags->pluck('id');
        $tags = Tag::whereNotIn('id', $userTagIds)->where('entreprise_id', $user->entreprise_id)->get();

        $equipements_location = CommandeProduct::with('commande')->where('user_attributed_id', $attribution_id)->where('sous_categorie', '!=', 'licences')->where('status', '!=', 'pending')->where('type_contrat', 'location')->whereHas('commande', function ($query) {
            $query->where('date_fin_contrat', '>=', Carbon::today());
        })->get();
        $equipements_achat = CommandeProduct::with('commande')->where('user_attributed_id', $attribution_id)->where('sous_categorie', '!=', 'licences')->where('status', '!=', 'pending')->where('type_contrat', 'achat')->whereHas('commande', function ($query) {
            $query->whereYear('date_debut_contrat', Carbon::now()->year)->whereMonth('date_debut_contrat', Carbon::now()->month);
        })->get();
        $licencesMonth = CommandeProduct::where('user_attributed_id', $attribution_id)->where('categorie', 'licences')->where('type_licence', 'Mensuel')->whereIn('status', ['active', 'provisioning'])->get();
        $licencesYear = CommandeProduct::where('user_attributed_id', $attribution_id)->where('categorie', 'licences')->where('type_licence', 'Annuel')->whereIn('status', ['active', 'provisioning'])->whereMonth('created_at', Carbon::now()->month)->get();
        $cout_total = $equipements_location->sum('prix') + $equipements_achat->sum('prix') + $licencesMonth->sum('prix') + $licencesYear->sum('prix');

        if ($attribution->type == 'Personne') {
            if ($attribution->tenant_user_id && $attribution->tenant_name == 'microsoft') {
                $token = $this->microsoftService->getAccessToken();
                $userApps = $this->microsoftService->getAppRoleAssignments($token, $attribution->tenant_user_id);
                $mfa = $this->microsoftService->getAutheticationMethods($token, $attribution->tenant_user_id);
            } else if ($attribution->tenant_user_id && $attribution->tenant_name == 'google') {
                $token = $this->googleService->getAccessToken();
                $userApps = $this->googleService->getUserApps($token, $attribution->tenant_user_id);
            }

            return Inertia::render('MonEquipe/Equipe', [
                'attribution' =>  $attribution,
                'equipement_available' =>  $equipement_available,
                'licence_available' =>  $licence_available,
                'equipements' => CommandeProduct::where('user_attributed_id', $attribution->id)->get(),
                'tags' => $tags,
                'userApps' => $userApps ?? [],
                'adresses' => AdresseLivraison::where('entreprise_id', $user->entreprise_id)->get(),
                'supports' => Support::where('user_id', $attribution->id)->get(),
                'domains' => $domains,
                'currentDomain' => $currentDomain,
                'cout_total' => $cout_total,
                'licences_hold' => $licences_hold,
                'mfa' => $mfa ?? [],
            ]);
        }

        return Inertia::render('MesSalles/Salle', [
            'attribution' =>  $attribution,
            'equipement_available' =>  $equipement_available,
            'equipements' => CommandeProduct::where('user_attributed_id', $attribution->id)->get(),
            'tags' => $tags,
            'adresses' => AdresseLivraison::where('entreprise_id', $user->entreprise_id)->get(),
            'cout_total' => $cout_total,
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function searchAttributions(Request $request)
    {
        return response()->json([
            'searchResults' => User::with(['commandeProducts', 'tags', 'entreprise'])
                ->where('entreprise_id', Auth::user()->entreprise_id)
                ->where('type', $request->type)
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->searchInput . '%')
                        ->orWhere('email', 'like', '%' . $request->searchInput . '%')
                        ->orWhereHas('tags', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->searchInput . '%');
                        });
                })->get()
        ]);
    }
    public function onboardCollaborateur(Request $request, TenantsController $tenantsController, PasserCommandeController $passerCommandeController)
    {
        $validated = $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email',
            'email_perso' => 'required|email'
        ]);

        $user = User::create([
            'name' => $validated['fname'] . ' ' . $validated['lname'],
            'email' => $validated['email'],
            'email_perso' => $validated['email_perso'],
            'type' => 'Personne',
            'role' => 'collaborateur',
            'poste' => $request->poste,
            'date_arrivee' => $request->date_arrivee,
            'tel' => $request->phone,
            'entreprise_id' => Auth::user()->entreprise_id
        ]);

        if ($request['tags']) {
            $user->tags()->sync($request['tags']);
        }

        $syncRequest = new Request([
            'userid' => $user->id,
            'email' => $user->email,
            'email_perso' => $user->email_perso,
            'tenant' => strtolower($request->tenant),
        ]);

        $response = $tenantsController->createTenantAccount($syncRequest);
        $user = $response->original['user'];

        if ($request->licences) {
            $licences = CommandeProduct::whereIn('id', $request->licences)->get();

            foreach ($licences as $licence) {
                $licence->update(['user_attributed_id' => $user->id]);

                if ($request->tenant === 'microsoft') {
                    $token = $this->microsoftService->getAccessToken();
                    $this->microsoftService->assignLicence($token, $user->tenant_user_id, $licence->guid);
                }
            }
        }

        if ($request->equipements) {
            $equipements = CommandeProduct::whereIn('id', $request->equipements)->get();
            foreach ($equipements as $equipement) {
                $equipement->update(['user_attributed_id' => $user->id]);
            }
        }

        if ($request->licencesMarketPlace || $request->equipementsMarketPlace) {
            PendingCommande::where('user_id', Auth::id())->delete();

            if ($request->licencesMarketPlace) {
                foreach ($request->licencesMarketPlace as $productId) {
                    PendingCommande::create([
                        'product_id' => $productId,
                        'quantity' => 1,
                        'user_id' => Auth::id(),
                        'adresse_livraison_id' => null,
                        'type_contrat' => 'location',
                        'user_livraison_id' => null,
                        'user_attributed_id' => $user->tenant_name === 'waiting' ? null : $user->id
                    ]);
                }
            }

            if ($request->equipementsMarketPlace) {
                foreach ($request->equipementsMarketPlace as $productId) {
                    PendingCommande::create([
                        'product_id' => $productId,
                        'quantity' => 1,
                        'user_id' => Auth::id(),
                        'type_contrat' => 'location',
                        'adresse_livraison_id' => $request->commande['livraison']['address']['id'],
                        'user_livraison_id' => $request->commande['livraison']['user']['id'],
                        'user_attributed_id' => $user->id
                    ]);
                }
            }

            if ($request->licencesMarketPlace && !$request->equipementsMarketPlace) {
                $passerCommandeController->store_full_licences();
            } else {
                $signataireData = $request->commande['signataire'];

                $syncRequest = Request::create('', 'POST', $signataireData);
                if (isset($signataireData['piece_identite_recto']) && !is_array($signataireData['piece_identite_recto']))
                    $syncRequest->files->set('piece_identite_recto', $signataireData['piece_identite_recto']);
                if (isset($signataireData['piece_identite_verso']) && !is_array($signataireData['piece_identite_verso']))
                    $syncRequest->files->set('piece_identite_verso', $signataireData['piece_identite_verso']);
                if (isset($signataireData['iban']) && !is_array($signataireData['iban']))
                    $syncRequest->files->set('iban', $signataireData['iban']);
                if (isset($signataireData['pouvoir']) && !is_array($signataireData['pouvoir']))
                    $syncRequest->files->set('pouvoir', $signataireData['pouvoir']);
                $passerCommandeController->store_signataire($syncRequest);
            }
        }
    }

    public function offboardCollaborateur(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer'
        ]);

        $user = User::find($validated['user_id']);

        $products = CommandeProduct::where('user_attributed_id', $user->id)->get();

        foreach ($products as $product) {
            if ($product->sous_categorie == 'licences') {
                if ($user->tenant_user_id && $user->tenant_name == 'microsoft') {
                    $token = $this->microsoftService->getAccessToken();
                    $this->microsoftService->unAssignLicence($token, $user->tenant_user_id, $product->guid);
                }
            }

            $product->update(['user_attributed_id' => null]);
        }
    }
}
