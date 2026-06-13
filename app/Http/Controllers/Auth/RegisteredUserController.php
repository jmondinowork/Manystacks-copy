<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdresseLivraison;
use App\Models\EntrepriseInformation;
use App\Models\Signataire;
use App\Models\User;
use App\Models\Tag;
use App\Models\Commande;
use App\Models\CommandeProduct;
use App\Models\IonLicence;
use App\Models\OauthToken;
use App\Models\Product;

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function loginFirstTime(Request $request)
    {
        $token = $request->input('token');

        if ($token) {
            $user = User::where('remember_token', $token)->first();

            if ($user) {
                return Inertia::render('Auth/LoginFirstTime', [
                    'user' => $user,
                ]);
            }
        }

        return redirect()->route('login');
    }

    public function syncItProFirstTime(): Response
    {
        return Inertia::render('Auth/SyncItProFirstTime', [
            'integrations' => config('integration.tenant'),
        ]);
    }

    public function syncSirhFirstTime(): Response
    {
        return Inertia::render('Auth/SyncSirhFirstTime', [
            'integrations' => config('integration.sirh'),
        ]);
    }

    public function storeFirstTime(Request $request)
    {
        $request->validate([
            'password' => ['required', Rules\Password::defaults()],
            'token' => ['required', 'string'],
        ]);

        $user = User::where('remember_token', $request->token)->whereNotNull('remember_token')->first();
        if (!$user) {
            return redirect()->route('login');
        }

        $user->update([
            'password' => Hash::make($request->password),
            'remember_token' => null,
        ]);

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Auth::login($user);

        return redirect(route('syncItProFirstTime'));
    }

    private function getLicenses($entreprise, $user)
    {
        if ($client_id = $this->ionService->getClientId($entreprise->siret)) {
            $entreprise->update(['ion_id' => $client_id]);

            if ($subscriptions = $this->ionService->getActiveSubscriptions($client_id)) {
                foreach ($subscriptions as $subscription) {
                    for ($i = 0; $i < intval($subscription['subscriptionTotalLicenses']); $i++) {
                        $productRef = Product::with('images')->where('sku_id', $subscription['ccpSkuId'])->first() ?? IonLicence::where('plan_id', $subscription['ccpPlanId'])->first();

                        if ($productRef) {
                            CommandeProduct::create([
                                'commande_id' => null,
                                'entreprise_id' => $entreprise->id,
                                'quantity' => 1,
                                'sku_id' => $subscription['ccpSkuId'],
                                'plan_id' => $subscription['ccpPlanId'],
                                'reference_id' => $subscription['ccpProductId'],
                                'commande_status' => 'COMPLETED',
                                // 'licence_id' => $subscription['subscriptionId'],
                                // 'licence_item_id' => $subscription['subscriptionId'],
                                'licence_resource_id' => $subscription['subscriptionId'],
                                'status' => 'active',
                                'name' => $productRef->name,
                                'slug' => Str::slug($productRef->name),
                                'categorie' => 'licences',
                                'sous_categorie' => 'licences',
                                'ref_fournisseur' => $productRef->ref_fournisseur ?? null,
                                'image_principale' => $productRef->image_principale,
                                'prix' => $subscription['price'],
                                'type_licence' => $productRef->type_licence ?? $productRef->facturation_period,
                                'guid' => $productRef->guid,
                                'fournisseur' => $productRef->fournisseur,
                                'date_debut_licence' => $subscription['subscriptionStartDate'],
                                'date_fin_licence' => $subscription['subscriptionEndDate'],
                                'auto_renew' => $subscription['subscriptionRenewStatus'] === 'ENABLED' ? 1 : 0,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'siret' => 'required',
            'email' => 'required|string|email|max:255|unique:' . User::class,
        ]);

        if (!$entreprise = $this->createEntreprise($request->siret)) {
            $entreprise = EntrepriseInformation::create([
                'siret' => $request->siret,
                'profile_img' => '/images/entreprise-icon.png',
                'pays' => 'France',
            ]);
        }

        Signataire::create([
            'entreprise_id' => $entreprise->id,
            'prenom' => $request->fname,
            'nom' => $request->lname,
            'telephone' => $request->tel,
            'mail' => $request->email,
            'representant_legal' => 1,
        ]);

        $uniqueId = Str::uuid();
        $user = User::create([
            'name' => $request->fname . ' ' . $request->lname,
            'email' => $request->email,
            'tel' => $request->tel,
            'role' => 'admin',
            'type' => 'Personne',
            'remember_token' => $uniqueId,
            'entreprise_id' => $entreprise->id,
        ]);

        $tag = Tag::create([
            'name' => 'Admin',
            'entreprise_id' => $entreprise->id,
            'color' => 'main',
        ]);
        $user->tags()->attach($tag->id);

        if (OauthToken::where('service_name', 'ion')->exists())
            $this->getLicenses($entreprise, $user);

        $this->emailService->sendEmail([
            'templateId' => 7,
            'to' => [['email' => $request->email]],
            'params' => [
                'destinataire' => $request->email,
                'cta' => env('APP_URL') . '/loginFirstTime?token=' . $uniqueId,
            ]
        ]);

        // event(new Registered($user));

        // Auth::login($user);

        return response()->json(['id' => $user->id]);
    }
}
