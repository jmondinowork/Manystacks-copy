<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Favoris;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavorisController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index($categorie = null, $souscategorie = null)
    {
        $userId = Auth::id();

        // Construction de base de la requête pour les produits favoris.
        $productQuery = Product::whereHas('favoris', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });


        if (!is_null($categorie)) {
            $productQuery->where('categorie', $categorie);
        }

        // Détermination des catégories à afficher.
        $field = is_null($categorie) ? 'categorie' : 'sous_categorie';
        $categories = $productQuery->distinct()->pluck($field);

        if (!is_null($souscategorie)) {
            $productQuery->where('sous_categorie', $souscategorie);
        }

        $products = $productQuery->get();

        // Redirection si une seule catégorie ou sous-categorie est trouvée.
        if ($categories->count() == 1 && is_null($souscategorie)) {
            $redirectCategorie = $categorie ?? $categories[0];
            $redirectSousCategorie = $categorie ? $categories[0] : '';
            return redirect()->route('mon-catalogue', [
                'categorie' => $redirectCategorie,
                'souscategorie' => $redirectSousCategorie
            ]);
        }

        return Inertia::render('MesFavoris', [
            'products'              => $products,
            'categories'            => $categories,
            'souscategorieplucked'  => $categories->count() == 1,
            'currentcategorie'      => $categorie
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function toggleFavoris(Request $request)
    {
        if ($request->favoris) {
            Favoris::firstOrCreate([
                'user_id' => Auth::id(),
                'product_slug' => $request->product_slug,
            ]);
        } else {
            Favoris::where('user_id', Auth::id())
                ->where('product_slug', $request->product_slug)
                ->delete();
        }

        return response()->json(['message' => "Favoris mis à jour avec succès."]);
    }

    public function searchFavoris(Request $request)
    {
        $searchResults = Product::whereHas('favoris', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->where('name', 'like', '%' . $request->searchInput . '%')
            ->get();

        return response()->json([
            'searchResults' => $searchResults
        ]);
    }
}
