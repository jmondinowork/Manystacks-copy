<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Panier;
use App\Models\Commande;
use App\Models\CommandeProduct;
use App\Models\Signataire;
use Illuminate\Support\Facades\Storage;
use App\Models\PendingCommande;
use App\Models\EntrepriseInformation;
use App\Models\AdresseLivraison;
use App\Models\PanierProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PasserCommandeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function fullLicences()
    {
        $panier = Panier::with(['panierProducts.product', 'user'])
            ->where('user_id', Auth::id())
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->first();

        if (!$panier || $panier->panierProducts->isEmpty()) return redirect('/catalogue');

        PendingCommande::where('user_id', Auth::id())->delete();

        foreach ($panier->panierProducts as $item) {
            for ($i = 0; $i < $item->quantity; $i++) {
                PendingCommande::create([
                    'product_id' => $item->product_id,
                    'quantity' => 1,
                    'user_id' => Auth::id(),
                    'adresse_livraison_id' => null,
                    'user_livraison_id' => null,
                    'type_contrat' => 'location'
                ]);
            }
        }

        return Inertia::render('Catalogue/PasserCommande/FullLicences', [
            "panier"    =>  $panier->panierProducts
        ]);
    }
    public function entreprise()
    {
        $userInfo = User::with('entreprise')->find(Auth::id());
        $panier = Panier::with(['panierProducts.product', 'user'])
            ->where('user_id', Auth::id())
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->first();

        if (!$panier || $panier->panierProducts->isEmpty()) return redirect('/catalogue');
        else if ($panier->panierProducts->every(fn($product) => $product->product->categorie === 'licences')) {
            return redirect('/passer-commande/full-licences');
        }

        return Inertia::render('Catalogue/PasserCommande/Entreprise', [
            "userInfo"  =>  $userInfo,
            "panier"    =>  $panier
        ]);
    }
    public function livraison()
    {
        $panier = Panier::with(['panierProducts.product', 'user'])
            ->where('user_id', Auth::id())
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->first();
        $adresses = AdresseLivraison::where('entreprise_id', Auth::user()->entreprise_id)->get();
        $collaborateurs = User::where('entreprise_id', Auth::user()->entreprise_id)->where('role', '!=', null)->get();

        if (!$panier || $panier->panierProducts->isEmpty())
            return redirect('/catalogue');
        else if (!Auth::user()->entreprise_id)
            return redirect('/passer-commande/entreprise');


        return Inertia::render('Catalogue/PasserCommande/Livraison', [
            "panier"    =>  $panier,
            "adresses"  =>  $adresses,
            "collaborateurs"     =>  $collaborateurs
        ]);
    }
    public function signataire()
    {
        $panier = Panier::with(['panierProducts.product', 'user'])
            ->where('user_id', Auth::id())
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->first();

        if (!$panier || $panier->panierProducts->isEmpty())
            return redirect('/catalogue');
        else if (!Auth::user()->entreprise_id)
            return redirect('/passer-commande/entreprise');


        $panierProducts = PanierProduct::where('panier_id', $panier->id)
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')->orderBy('product_id', 'asc')->get()->toArray();

        $pendingCommandesProducts = PendingCommande::where('user_id', Auth::id())
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')->orderBy('product_id', 'asc')->get()->toArray();

        foreach ($pendingCommandesProducts as $key => $value) {
            if (!isset($panierProducts[$key]) || $panierProducts[$key] != $value) {
                PendingCommande::where('user_id', Auth::id())->delete();
                return redirect('/passer-commande/livraison');
            }
        }

        $pendingCommandes = PendingCommande::with('product')
            ->where('user_id', Auth::id())
            ->whereHas('product', function ($query) {
                $query->where('categorie', '!=', 'licences');
            })
            ->get()
            ->groupBy('adresse_livraison');

        foreach ($pendingCommandes as $adresse => $commandes) {
            $grouped = [];

            foreach ($commandes as $commande) {
                $key = $commande->product_id . '_' . $commande->type_contrat;

                if (!isset($grouped[$key])) {
                    $grouped[$key] = $commande;
                } else {
                    $grouped[$key]->quantity += $commande->quantity;
                }
            }

            $pendingCommandes[$adresse] = array_values($grouped);
        }

        $selectedSignataire = Signataire::where('entreprise_id', Auth::user()->entreprise_id)
            ->select(
                "id",
                'prenom',
                'nom',
                'telephone',
                'mail',
                'ville_naissance',
                'date_naissance',
                'piece_identite_recto',
                'piece_identite_verso',
                'pouvoir',
                'iban',
                'representant_legal'
            )
            ->latest()
            ->first() ?? null;

        if ($selectedSignataire) $selectedSignataire->rgpd = false;

        return Inertia::render('Catalogue/PasserCommande/Signataire', [
            'signataires'           =>  Signataire::where('entreprise_id', Auth::user()->entreprise_id)->get(),
            'selectedSignataire'    =>  $selectedSignataire,
            'pendingCommande'       =>  $pendingCommandes
        ]);
    }
    public function confirme()
    {
        return Inertia::render('Catalogue/PasserCommande/Confirme', [
            'reference_commande'    =>  Commande::where('entreprise_id', Auth::user()->entreprise_id)->latest()->first()->reference_commande
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function store_full_licences()
    {
        $reference_commande = $this->generateUniqueReference();
        $commande = Commande::create([
            'reference_commande' => $reference_commande,
            'entreprise_id' => Auth::user()->entreprise_id,
            'user_id' => Auth::id(),
            'signataire_id' => null,
            'statut' => 'En attente de provisionnement'
        ]);

        $this->createCommandeProducts($commande);
        $this->sendCommandeEmail($reference_commande);

        return redirect('/passer-commande/confirme');
    }
    public function store_entreprise_informations()
    {
        $entreprise_id = Auth::user()->entreprise_id;
        $validatedData = request()->validate([
            'siret' => 'required|string|max:255',
            'raison_sociale' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'complement_adresse' => 'nullable|string|max:255',
            'code_postal' => 'required|string|max:10',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255'
        ]);

        $adresseData = [
            'entreprise_id' => $entreprise_id,
            'adresse' => $validatedData['adresse'],
            'titre' => "Siège social",
            'primary' => 1,
            'code_postal' => $validatedData['code_postal'],
            'ville' => $validatedData['ville'],
            'pays' => $validatedData['pays']
        ];
        if (!AdresseLivraison::where('entreprise_id', $entreprise_id)->where('default', 1)->exists())
            $adresseData['default'] = 1;

        EntrepriseInformation::where('id', $entreprise_id)->update($validatedData);
        AdresseLivraison::updateOrCreate(
            ['primary' => 1, 'entreprise_id' => $entreprise_id],
            $adresseData
        );

        $userEntreprise = User::where('entreprise_id', $entreprise_id)->where('role', 'entreprise')->first();
        if ($userEntreprise) {
            $userEntreprise->update([
                'name' => $validatedData['raison_sociale'],
            ]);
        } else {
            User::create([
                'entreprise_id' => $entreprise_id,
                'name' => $validatedData['raison_sociale'],
                'profile_img' => "/images/entreprise-icon.png",
                'role'  => "entreprise"
            ]);
        }

        return redirect('/passer-commande/livraison');
    }
    public function store_livraison(Request $request)
    {
        PendingCommande::where('user_id', Auth::id())->delete();

        if ($request->adresseType === 'single') {
            foreach ($request->single['products'] as $item) {
                PendingCommande::create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'type_contrat' => $item['type_contrat'],
                    'user_id' => Auth::id(),
                    'adresse_livraison_id' => $request->single['address']['id'],
                    'user_livraison_id' => $request->single['user']['id']
                ]);
            }
        } else {
            foreach ($request->multiple as $item) {
                PendingCommande::create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'type_contrat' => $item['type_contrat'],
                    'user_id' => Auth::id(),
                    'adresse_livraison_id' => $item['address']['id'],
                    'user_livraison_id' => $item['user']['id']
                ]);
            }
        }

        return redirect('/passer-commande/signataire');
    }
    public function store_signataire(Request $request)
    {
        $validatedData = $request->validate([
            'id' => '',
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'telephone' => 'required|string|max:255',
            'mail' => 'required|email',
            'date_naissance' => 'required|string|max:255',
            'ville_naissance' => 'required|string|max:255',
            'representant_legal' => 'required|boolean',
            'piece_identite_recto' => 'required',
            'piece_identite_verso' => 'required',
            'pouvoir' => 'required_if:representant_legal,false',
            'iban' => 'required',
            'rgpd' => 'required|accepted'
        ]);
        unset($validatedData['rgpd']);

        $signataire = Signataire::findOrNew($validatedData['id']);
        $signataire->fill($validatedData);

        if ($request->hasFile('piece_identite_recto')) {
            $extension = $request->file('piece_identite_recto')->getClientOriginalExtension();
            $piece_identite_recto_name = Auth::user()->name . "/piece_identite_recto_signataire_" . $validatedData['nom'] . "_" . $validatedData['prenom'] . "." . $extension;
            Storage::disk('s3')->put($piece_identite_recto_name, file_get_contents($request->file("piece_identite_recto")), 'public');
            $piece_identite_recto_url = Storage::disk('s3')->url($piece_identite_recto_name);
        } else $piece_identite_recto_url = $validatedData['piece_identite_recto']['url'] ?? NULL;

        if ($request->hasFile('piece_identite_verso')) {
            $extension = $request->file('piece_identite_verso')->getClientOriginalExtension();
            $piece_identite_verso_name = Auth::user()->name . "/piece_identite_verso_signataire_" . $validatedData['nom'] . "_" . $validatedData['prenom'] . "." . $extension;
            Storage::disk('s3')->put($piece_identite_verso_name, file_get_contents($request->file("piece_identite_verso")), 'public');
            $piece_identite_verso_url = Storage::disk('s3')->url($piece_identite_verso_name);
        } else  $piece_identite_verso_url = $validatedData['piece_identite_verso']['url'] ?? NULL;

        if ($request->hasFile('pouvoir')) {
            $extension = $request->file('pouvoir')->getClientOriginalExtension();
            $pouvoir_name = Auth::user()->name . "/pouvoir_signataire_" . $validatedData['nom'] . "_" . $validatedData['prenom'] . "." . $extension;
            Storage::disk('s3')->put($pouvoir_name, file_get_contents($request->file("pouvoir")), 'public');
            $pouvoir_url = Storage::disk('s3')->url($pouvoir_name);
        } else $pouvoir_url = $validatedData['pouvoir']['url'] ?? NULL;

        if ($request->hasFile('iban')) {
            $extension = $request->file('iban')->getClientOriginalExtension();
            $iban_name = Auth::user()->name . "/iban_signataire_" . $validatedData['nom'] . "_" . $validatedData['prenom'] . "." . $extension;
            Storage::disk('s3')->put($iban_name, file_get_contents($request->file("iban")), 'public');
            $iban_url = Storage::disk('s3')->url($iban_name);
        } else  $iban_url = $validatedData['iban']['url'] ?? NULL;


        $signataire->piece_identite_recto = $piece_identite_recto_url;
        $signataire->piece_identite_verso = $piece_identite_verso_url;
        $signataire->pouvoir = $pouvoir_url;
        $signataire->iban = $iban_url;
        $signataire->entreprise_id = Auth::user()->entreprise_id;

        $signataire->save();

        $reference_commande = $this->generateUniqueReference();
        $pendingCommandes = PendingCommande::with('product')->where('user_id', Auth::id())->get();
        $count_licences = 0;
        $count_achat = 0;

        foreach ($pendingCommandes as $pendingCommande) {
            if ($pendingCommande->product->categorie === 'licences') {
                $count_licences++;
            }
            if ($pendingCommande->type_contrat === 'achat') {
                $count_achat++;
            }
        }

        if ($count_licences == count($pendingCommandes))
            $status = 'En attente de provisionnement';
        else if ($count_achat == count($pendingCommandes))
            $status = 'En confirmation d\'achat';
        else
            $status = 'En attente de financement';
        $commande = Commande::create([
            'reference_commande' => $reference_commande,
            'entreprise_id' => Auth::user()->entreprise_id,
            'user_id' => Auth::id(),
            'signataire_id' => $signataire->id,
            'statut' => $status
        ]);

        $this->createCommandeProducts($commande);
        $this->sendCommandeEmail($reference_commande);

        return redirect('/passer-commande/confirme');
    }
    public function store_adresse()
    {
        $validatedData = request()->validate([
            'id' => 'nullable',
            'titre' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'complement_adresse' => 'nullable|string|max:255',
            'code_postal' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255'
        ]);
        $validatedData['entreprise_id'] = Auth::user()->entreprise_id;

        AdresseLivraison::updateOrCreate(
            ['id' => $validatedData['id'], 'entreprise_id' => Auth::user()->entreprise_id],
            $validatedData
        );
        return response()->json(AdresseLivraison::where('entreprise_id', Auth::user()->entreprise_id)->get());
    }
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS MANAGEMENT
    |--------------------------------------------------------------------------
    */
    private function sendCommandeEmail($reference_commande)
    {
        $this->emailService->sendEmail([
            'templateId' => 3,
            'to' => [['email' => env('MANYSTACK_CONTACT')]],
            'params' => [
                'objet' => "Nouvelle commande",
                'title' => "Nouvelle commande",
                'subtitle' => "Commande n°$reference_commande",
                'cta_title' => "Voir la commande",
                'cta_url' => env('APP_URL') . "/commandesAdmin/" . $reference_commande
            ]
        ]);

        $commande = Commande::where('reference_commande', $reference_commande)->first();
        $commandeProducts = CommandeProduct::with(['userLivraison', 'adresseLivraison'])
            ->where('commande_id', $commande['id'])
            ->where('categorie', '!=', 'licences')
            ->get()
            ->groupBy('adresse_livraison_id')
            ->map(function ($items) {
                $uniqueItems = [];

                foreach ($items as $item) {
                    if (isset($uniqueItems[$item->ref_fournisseur])) {
                        $uniqueItems[$item->ref_fournisseur]->quantity += $item->quantity;
                        $uniqueItems[$item->ref_fournisseur]->prix += $item->prix;
                    } else {
                        $uniqueItems[$item->ref_fournisseur] = $item;
                    }
                }

                return array_values($uniqueItems);
            })
            ->values()
            ->map(function ($products) {
                $allProducts = [];

                foreach ($products as $product) {
                    $allProducts[] = [
                        'name' => $product->name,
                        'quantity' => 'x ' . $product->quantity,
                        'prix' => $product->prix . ' €',
                        'adresse' => $product->adresseLivraison->adresse,
                        'ville' => $product->adresseLivraison->code_postal . ', ' . $product->adresseLivraison->ville,
                        'user' => $product->userLivraison->name,
                    ];
                }

                return $allProducts;
            })->collapse()
            ->toArray();

        if ($commandeProducts) {
            $this->emailService->sendEmail([
                'templateId' => 8,
                'to' => [['email' => Auth::user()->email]],
                'params' => [
                    'cta' => env('APP_URL') . "/mes-commandes/" . $reference_commande,
                    'reference_commande' => $reference_commande,
                    'destinataire' => Auth::user()->email,
                    'date_commande' => now()->format('d/m/Y'),
                    'prix_commande' => $commande->commandeProducts->sum('prix') . ' €',
                    'articles' => $commandeProducts
                ]
            ]);
        }
    }

    private function createCommandeProducts($commande)
    {
        $user = User::with('entreprise')->find(Auth::id());

        $pendingCommandes = PendingCommande::with('product')
            ->where('user_id', Auth::id())
            ->get();
        $googleOrder = PendingCommande::with('product')
            ->where('user_id', Auth::id())
            ->whereHas('product', function ($query) {
                $query->where('fournisseur', 'google');
            })
            ->get();

        $ionOrderItems = [];

        $columnsToUnset = ['id', 'created_at', 'updated_at', 'co2', 'delais_livraison', 'deleted', 'images', 'slug', 'top_produit', 'appsincluses', 'appstype'];

        // Je prépare les produits à insérer dans la table commande_products
        foreach ($pendingCommandes as $pendingCommande) {
            $productAttributes = $pendingCommande->product->toArray();
            foreach ($columnsToUnset as $column) {
                unset($productAttributes[$column]);
            }

            $productAttributes += [
                'commande_id' => $commande->id,
                'entreprise_id' => $user->entreprise_id,
                'adresse_livraison_id' => $pendingCommande->adresse_livraison_id,
                'user_livraison_id' => $pendingCommande->user_livraison_id,
                'user_attributed_id' => $pendingCommande->user_attributed_id,
                'quantity' => $pendingCommande->quantity,
                'licence_resource_id' => null,
                'commande_status' => 'pending',
                'status' => 'pending',
                'slug' => Str::slug($pendingCommande->product->name),
                'type_contrat' => $pendingCommande->type_contrat,
                'prix' => $pendingCommande->type_contrat === 'location' ? $pendingCommande->product->prix_location : $pendingCommande->product->prix_achat,
                'licence_resource_id' => null,
                'date_debut_licence' => null,
                'date_fin_licence' => null,
                'auto_renew' => 0,
                'status' => 'pending',
                'commande_status' => 'ON_HOLD',
                'created_at' => now(),
                'updated_at' => now()
            ];

            unset($productAttributes['prix_achat']);
            unset($productAttributes['prix_location']);

            // Si le produit est une licence Microsoft, j'ajoute chaque licence une à une dans l'array ionOrderItems (quantity 1)
            if ($pendingCommande->product->categorie === 'licences') {
                $productAttributes['status'] = 'provisioning';

                if ($pendingCommande->product->fournisseur === 'microsoft') {
                    $ionOrderItems[] = [
                        "referenceId" => "item-" . $pendingCommande->product->reference_id,
                        "productId" => $pendingCommande->product->reference_id,
                        "skuId" => $pendingCommande->product->sku_id,
                        "planId" => $pendingCommande->product->plan_id,
                        "action" => "CREATE",
                        "quantity" => $pendingCommande->quantity,
                        "endCustomerPO" => $commande->reference_commande,
                        "resellerPO" => $commande->reference_commande,
                        "attributes" => [
                            "name" => "autoRenewFlag",
                            "value" => "true"
                        ]
                    ];
                }
                // Je fais ça parce que je suis degueu et que le product dans pending commande est un product ou je cherche la ref_fournisseur dans la table product,
                // alors que pour Google je n'ai pas de licence dans la table product, je devrais récupérer les info dans la table ion_licences mais vu que c'est que pour google je me permet de faire ça ici
                // donc je récupère la licence ref depuis la table commande_products de l'user et je l'ajoute dans les attributs du produit.
                else if ($pendingCommande->product->fournisseur === 'google') {
                    $googleProductRef = CommandeProduct::where('entreprise_id', Auth::user()->entreprise_id)
                        ->where('reference_id', $pendingCommande->product->reference_id)
                        ->where('status', 'active')
                        ->first();

                    $productAttributes['sku_id'] = $googleProductRef->sku_id;
                    $productAttributes['plan_id'] = $googleProductRef->plan_id;
                }
            }

            $productsToInsert[] = $productAttributes;
        }

        // J'insère les produits dans la table commande_products
        CommandeProduct::insert($productsToInsert);

        // Je supprime les produits du panier et les commandes en attente
        PendingCommande::where('user_id', Auth::id())->delete();
        Panier::where('user_id', Auth::id())->where('entreprise_id', Auth::user()->entreprise_id)->delete();


        // Si j'ai des licences google, j'update le nombre de licences sur le plan actuel de l'user (différent de Microsoft)
        if ($googleOrder->isNotEmpty()) {
            $gws_count = CommandeProduct::where('entreprise_id', Auth::user()->entreprise_id)
                ->where('reference_id', $googleProductRef->reference_id)
                ->where('status', 'active')
                ->where('sku_id', $googleProductRef->sku_id)
                ->where('plan_id', $googleProductRef->plan_id)
                ->count();

            $ionOrderItems[] = [
                "productId" => $googleProductRef->reference_id,
                "skuId" => $googleProductRef->sku_id,
                "planId" => $googleProductRef->plan_id,
                "action" => "UPDATE",
                "quantity" => $gws_count + $googleOrder->count(),
                "resourceId" => $googleProductRef->licence_resource_id,
                "attributes" => [
                    [
                        "name" => "operations",
                        "value" => "updateSubscription"
                    ]
                ]
            ];
        }

        // Je créé la commande sur ION (Google et Microsoft)
        if (!empty($ionOrderItems)) {
            $customerId = $user->entreprise->ion_id;
            $body = [
                "referenceId" => "Order-" . $customerId . $commande->reference_commande,
                "displayName" => "Order for customer " . $customerId . $commande->reference_commande,
                "orderItems" => $ionOrderItems
            ];

            $ionCommande = $this->ionService->createOrder($customerId, $body);
            $commande->commande_ion_id = basename($ionCommande['name']);
            $commande->save();

            // Je mets à jour les licences Google et Microsoft avec les resourceId
            foreach ($ionCommande['orderItems'] as $orderItem) {
                $product = CommandeProduct::where('entreprise_id', Auth::user()->entreprise_id)
                    ->where('reference_id', $orderItem['productId'])
                    ->where('sku_id', $orderItem['skuId'])
                    ->where('plan_id', $orderItem['planId'])
                    ->where('status', 'provisioning')
                    ->first();

                if ($product && $product->fournisseur === 'google')
                    $product->licence_resource_id = $googleProductRef->licence_resource_id;

                $product->save();
            }
        }
    }
}
