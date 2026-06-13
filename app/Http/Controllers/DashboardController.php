<?php

namespace App\Http\Controllers;

use App\Models\CommandeProduct;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Commande;
use App\Models\EntrepriseInformation;
use App\Models\Support;
use App\Models\Product;
use Aws\Command;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = User::with('entreprise')->find(Auth::id());

        $collaborateurs_nb = User::where('entreprise_id', $user->entreprise_id)->where('type', 'Personne')->count();
        $licencesMonth = CommandeProduct::where('entreprise_id', $user->entreprise_id)->where('categorie', 'licences')->where('type_licence', 'Mensuel')->whereIn('status', ['active', 'provisioning'])->get();
        $licencesYear = CommandeProduct::where('entreprise_id', $user->entreprise_id)->where('categorie', 'licences')->where('type_licence', 'Annuel')->whereIn('status', ['active', 'provisioning'])->get();
        $equipements_location = CommandeProduct::with('commande')->where('entreprise_id', $user->entreprise_id)->where('sous_categorie', '!=', 'licences')->where('status', '!=', 'pending')->where('type_contrat', 'location')->whereHas('commande', function ($query) {
            $query->where('date_fin_contrat', '>=', Carbon::today());
        })->get();
        $equipements_achat = CommandeProduct::with('commande')->where('entreprise_id', $user->entreprise_id)->where('sous_categorie', '!=', 'licences')->where('status', '!=', 'pending')->where('type_contrat', 'achat')->whereHas('commande', function ($query) {
            $query->whereYear('date_debut_contrat', Carbon::now()->year)->whereMonth('date_debut_contrat', Carbon::now()->month);
        })->get();
        $commandes = Commande::with('commandeProducts')->where('entreprise_id', $user->entreprise_id)->whereNotNull('financeur')->get();
        $supports = Support::with('user')->where('entreprise_id', $user->entreprise_id)->where('status', 'En cours')->get();

        $entreprise = EntrepriseInformation::find($user->entreprise_id);
        $siblings = $entreprise->siblings();
        $entreprise->siblings = $siblings;

        $prixLicencesMonthly = $licencesMonth->sum('prix') + $licencesYear->filter(function ($licence) {
            return $licence->created_at->month == Carbon::now()->month;
        })->sum('prix');

        return Inertia::render('Dashboard/Index', [
            'count' => [
                'collaborateurs' => $collaborateurs_nb,
                'licences' => $licencesMonth->count() + $licencesYear->count(),
                'equipements' => CommandeProduct::where('entreprise_id', $user->entreprise_id)->where('sous_categorie', '!=', 'licences')->where('status', '!=', 'pending')->count(),
                'prix_licences_month' => $licencesMonth->sum('prix'),
                'prix_licences_year' => $licencesYear->sum('prix'),
                'prix_equipements_location' => $equipements_location->sum('prix'),
                'prix_equipements_achat' => $equipements_achat->sum('prix'),
                'prix_location_total' => $equipements_location->sum('prix') + $prixLicencesMonthly,
                'prix_achat_total' => $equipements_achat->sum('prix'),
            ],
            'licencesMonth' => $licencesMonth->groupBy('name')->map(function ($items, $key) {
                return [
                    'name' => $key,
                    'total' => $items->count(),
                    'prix_u' => $items->first()->prix,
                    'image_principale' => $items->first()->image_principale,
                    'slug' => $items->first()->slug,
                ];
            })->values(),
            'licencesYear' => $licencesYear->groupBy('name')->map(function ($items, $key) {
                $firstItem = $items->first();
                $purchaseDate = $firstItem->created_at;
                $now = Carbon::now();
                $nextRenewal = Carbon::create($now->year, $purchaseDate->month, $purchaseDate->day, 0, 0, 0);
                if ($nextRenewal->lessThanOrEqualTo($now)) {
                    $nextRenewal->addYear();
                }
                $echeance = $now->diffInMonths($nextRenewal);

                return [
                    'name' => $key,
                    'total' => $items->count(),
                    'prix_u' => $items->first()->prix,
                    'echeance' => $echeance,
                    'image_principale' => $items->first()->image_principale,
                    'slug' => $items->first()->slug,
                ];
            })->values(),
            'equipements_location' => $equipements_location->groupBy('name')->map(function ($items, $key) {
                return [
                    'name' => $key,
                    'total' => $items->count(),
                    'prix_u' => $items->first()->prix,
                    'image_principale' => $items->first()->image_principale,
                    'id' => $items->first()->id,
                ];
            })->values(),
            'equipements_achat' => $equipements_achat->groupBy('name')->map(function ($items, $key) {
                return [
                    'name' => $key,
                    'total' => $items->count(),
                    'prix_u' => $items->first()->prix,
                    'image_principale' => $items->first()->image_principale,
                    'id' => $items->first()->id,
                ];
            })->values(),
            'commandes' => $commandes,
            'supports' => $supports,
            'entreprise' => $entreprise,
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function switchEntreprise(Request $request)
    {
        $request->validate([
            'entreprise_id' => 'required',
        ]);

        $user = User::find(Auth::id());
        $user->entreprise_id = $request->entreprise_id;
        $user->save();
    }
    public function searchDashboard(Request $request)
    {
        $searchInput = $request->input('searchInput');

        $mesEquipements = CommandeProduct::where('name', 'like', '%' . $searchInput . '%')
            ->orWhere('numero_unique', 'like', '%' . $searchInput . '%')
            ->get()
            ->groupBy(function ($commandeProduct) {
                return $commandeProduct->categorie == 'licences' ? $commandeProduct->name : $commandeProduct->id;
            })
            ->map(function ($groupedProducts) {
                $commandeProduct = $groupedProducts->first();
                if ($commandeProduct->categorie == 'licences') {
                    return [
                        'image' => $commandeProduct->image_principale,
                        'link' => '/mes-licences/' . $commandeProduct->slug,
                        'name' => $commandeProduct->name,
                        'path' => 'Licences >'
                    ];
                } else {
                    return [
                        'image' => $commandeProduct->image_principale,
                        'link' => '/mes-equipements/' . $commandeProduct->id,
                        'description' => $commandeProduct->numero_unique,
                        'name' => $commandeProduct->name,
                        'path' => 'Équipements >'
                    ];
                }
            })
            ->values();

        $mesCommandes = Commande::where('reference_commande', 'like', '%' . $searchInput . '%')
            ->get()
            ->map(function ($commande) {
                if ($commande->contrat_signe) {
                    return [
                        'image' => $commande->commandeProducts->first()->image_principale,
                        'link' => '/mes-contrats/' . $commande->reference_commande,
                        'name' => $commande->reference_commande,
                        'path' => 'Contrats >'
                    ];
                } else {
                    return [
                        'image' => $commande->commandeProducts->first()->image_principale,
                        'link' => '/mes-commandes/' . $commande->reference_commande,
                        'name' => $commande->reference_commande,
                        'path' => 'Commandes >'
                    ];
                }
            });

        $mesCommandeProducts = Commande::with('commandeProducts')
            ->whereHas('commandeProducts', function ($query) use ($searchInput) {
                $query->where('name', 'like', '%' . $searchInput . '%');
                $query->orWhere('numero_unique', 'like', '%' . $searchInput . '%');
            })
            ->get()
            ->flatMap(function ($commande) use ($searchInput) {
                return $commande->commandeProducts
                    ->filter(function ($product) use ($searchInput) {
                        return stripos($product->name, $searchInput) !== false;
                    })
                    ->groupBy('name')
                    ->map(function ($product) use ($commande) {
                        $product = $product->first();

                        if ($commande->contrat_signe) {
                            return [
                                'image' => $product->image_principale,
                                'link' => '/mes-contrats/' . $commande->reference_commande,
                                'description' => $product->numero_unique,
                                'name' => $product->name,
                                'path' => 'Contrats >'
                            ];
                        } else {
                            return [
                                'image' => $product->image_principale,
                                'link' => '/mes-commandes/' . $commande->id,
                                'description' => $product->numero_unique,
                                'name' => $product->name,
                                'path' => 'Commandes >'
                            ];
                        }
                    });
            })
            ->values();

        $mesCollaborateurs = User::where('entreprise_id', Auth::user()->entreprise_id)
            ->where('type', 'Personne')
            ->where('name', 'like', '%' . $searchInput . '%')
            ->orWhere('email', 'like', '%' . $searchInput . '%')
            ->get()
            ->map(function ($collaborateur) {
                return [
                    'image' => $collaborateur->profile_img,
                    'link' => '/mon-equipe/' . $collaborateur->id,
                    'description' => $collaborateur->email,
                    'name' => $collaborateur->name,
                    'path' => 'Équipe >'
                ];
            });

        $mesSalles = User::with('adresse')
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->where('type', 'Salle')
            ->where('name', 'like', '%' . $searchInput . '%')
            ->orWhereHas('adresse', function ($query) use ($searchInput) {
                $query->where('titre', 'like', '%' . $searchInput . '%');
            })
            ->get()
            ->map(function ($salle) {
                return [
                    'image' => $salle->profile_img,
                    'link' => '/mes-salles/' . $salle->id,
                    'description' => $salle->adresse->titre,
                    'name' => $salle->name,
                    'path' => 'Salles >'
                ];
            });


        $catalogue = Product::where('name', 'like', '%' . $searchInput . '%')
            ->where('deleted', 0)
            ->get()
            ->map(function ($product) {
                return [
                    'image' => $product->image_principale,
                    'link' => '/catalogue/' . $product->categorie . '/' . $product->sous_categorie . '/' . $product->slug . '?id=' . $product->id,
                    'description' => $product->proprietes,
                    'name' => $product->name,
                    'path' => 'Catalogue >'
                ];
            });

        $mesEquipements = collect($mesEquipements);
        $mesCommandes = collect($mesCommandes);
        $mesCommandeProducts = collect($mesCommandeProducts);
        $mesCollaborateurs = collect($mesCollaborateurs);
        $mesSalles = collect($mesSalles);
        $catalogue = collect($catalogue);

        $allResults = $mesEquipements->merge($mesCommandes)->merge($mesCommandeProducts)->merge($mesCollaborateurs)->merge($mesSalles)->merge($catalogue);
        return response()->json(['searchResults' => $allResults]);
    }
}
