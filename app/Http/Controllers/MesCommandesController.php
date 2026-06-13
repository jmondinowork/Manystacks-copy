<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;
use App\Models\CommandeProduct;

class MesCommandesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $mes_commandes = Commande::with(['commandeProducts'])->where('entreprise_id', Auth::user()->entreprise_id)
            ->where('statut', '!=', 'Terminée')
            ->where('statut', '!=', 'Erreur')
            ->get();

        return Inertia::render('MesCommandes/Index', [
            'mes_commandes' =>  $mes_commandes
        ]);
    }
    public function commande($reference_commande = null)
    {
        if (!$commande = Commande::where('entreprise_id', Auth::user()->entreprise_id)->where('reference_commande', $reference_commande)->first())
            return redirect('/mes-commandes');

        $products_location = CommandeProduct::with(['adresseLivraison', 'userLivraison'])
            ->where('commande_id', $commande->id)
            ->where('categorie', '!=', 'licences')
            ->where('type_contrat', 'location')
            ->get();

        $products_achat = CommandeProduct::with(['adresseLivraison', 'userLivraison'])
            ->where('commande_id', $commande->id)
            ->where('categorie', '!=', 'licences')
            ->where('type_contrat', 'achat')
            ->get();

        $licences = CommandeProduct::where('commande_id', $commande->id)
            ->where('categorie', 'licences')
            ->get();

        $recapProducts_location = CommandeProduct::with(['adresseLivraison', 'userLivraison'])
            ->where('commande_id', $commande->id)
            ->where('categorie', '!=', 'licences')
            ->where('type_contrat', 'location')
            ->get()
            ->groupBy('name')
            ->map(function ($items) {
                // Merge quantities for each group
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->quantity += $item->quantity;
                    return $carry;
                });
            })->values();

        $recapProducts_achat = CommandeProduct::with(['adresseLivraison', 'userLivraison'])
            ->where('commande_id', $commande->id)
            ->where('categorie', '!=', 'licences')
            ->where('type_contrat', 'achat')
            ->get()
            ->groupBy('name')
            ->map(function ($items) {
                // Merge quantities for each group
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->quantity += $item->quantity;
                    return $carry;
                });
            })->values();

        $recapLicences = CommandeProduct::where('commande_id', $commande->id)
            ->where('categorie', 'licences')
            ->get()
            ->groupBy('name')
            ->map(function ($items) {
                // Merge quantities for each group
                return $items->reduce(function ($carry, $item) {
                    if (!$carry) {
                        return $item;
                    }
                    $carry->quantity += $item->quantity;
                    return $carry;
                });
            })->values();

        $products = $products_location->merge($products_achat);
        $commande->commande_products = $products->toArray();
        $commande->totalQuantity = $products->sum('quantity');

        return Inertia::render('MesCommandes/Commande', [
            'commande'  =>  $commande,
            'licences'  =>  $licences,
            'products_location'  =>  $products_location,
            'products_achat'  =>  $products_achat,
            'recapProducts_location' => $recapProducts_location,
            'recapProducts_achat' => $recapProducts_achat,
            'recapLicences' => $recapLicences
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function signature(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer'
        ]);

        $commandeId = $validatedData['id'];
        Commande::where('id', $commandeId)
            ->update([
                'statut' => "En validation du contrat",
                "sign_again" => 0,
                'date_signature' => now()->toIso8601String()
            ]);
        $commande = Commande::find($commandeId);

        $this->emailService->sendEmail([
            'templateId' => 3,
            'to' => [['email' => env('MANYSTACK_CONTACT')]],
            'params' => [
                'objet' => "Contrat signé",
                'title' => "Contrat signé (A vérifier)",
                'subtitle' => "Commande n°$commande->reference_commande",
                'cta_title' => "Voir la commande",
                'cta_url' => env('APP_URL') . "/commandesAdmin/" . $commande->reference_commande
            ]
        ]);
    }
    public function searchCommandes(Request $request)
    {
        return response()->json([
            'searchResults' => Commande::with(['commandeProducts'])->where('entreprise_id', Auth::user()->entreprise_id)->where('reference_commande', 'like', '%' . $request->searchInput . '%')->get()
        ]);
    }
}
