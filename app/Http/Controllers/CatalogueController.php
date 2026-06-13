<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Favoris;
use App\Models\Licence;
use App\Models\Stack;
use Illuminate\Support\Facades\Auth;

class CatalogueController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $products = Product::where('top_produit', 1)->distinct()->get();
        $categories = Product::select('categorie')->distinct()->orderBy('categorie')->pluck('categorie');
        $stacksPublic = Stack::with('products')->where('public', 1)->get();

        return Inertia::render('Catalogue/Index', [
            'products'      =>  $products,
            'categories'    =>  $categories,
            'stacksPublic'  =>  $stacksPublic
        ]);
    }

    public function categorie(Request $request)
    {
        $specialCategories = ['ordinateurs', 'telephones', 'tablettes', 'licences'];
        if (in_array($request->categorie, $specialCategories)) {
            return redirect()->route('souscategorie', ['categorie' => $request->categorie, 'souscategorie' => $request->categorie]);
        }

        $products = Product::with('images')->where('categorie', $request->categorie)->where('deleted', 0)->get();
        $categories = Product::select('categorie')->distinct()->orderBy('categorie')->where('deleted', 0)->pluck('categorie');
        $sous_categories = Product::select('sous_categorie')->where('categorie', $request->categorie)->where('deleted', 0)->distinct()->orderBy('sous_categorie')->pluck('sous_categorie');

        return Inertia::render('Catalogue/Categorie', [
            'products'          =>  $products,
            'categories'        =>  $categories,
            'sous_categories'   =>  $sous_categories,
            'current_categorie' =>  $request->categorie
        ]);
    }

    public function souscategorie(Request $request)
    {
        $categories = Product::select('categorie')->where('deleted', 0)->distinct()->orderBy('categorie')->pluck('categorie');
        $products = Product::with('images')->where('sous_categorie', $request->souscategorie)->where('deleted', 0)->get();
        $sous_categories = Product::select('sous_categorie')->where('categorie', $request->categorie)->where('deleted', 0)->distinct()->orderBy('sous_categorie')->pluck('sous_categorie');


        return Inertia::render('Catalogue/Souscategorie', [
            'products'          =>  $products,
            'categories'        =>  $categories,
            'sous_categories'   =>  $sous_categories,
            'current_categorie' =>  $request->souscategorie
        ]);
    }

    public function product(Request $request)
    {
        if ($request->id) {
            $product = Product::with('images')->find($request->id);
            $products = Product::with('images')->where('slug', $product->slug)->get();
        }
        else {
            $products = Product::with('images')->where('slug', $request->slug)->get();
            $product = $products[0];
        }
        $categories = Product::select('categorie')->distinct()->orderBy('categorie')->pluck('categorie');
        $sous_categories = Product::select('sous_categorie')->where('categorie', $request->categorie)->distinct()->orderBy('sous_categorie')->pluck('sous_categorie');

        // Algo a faire pour récupérer les produits qui pourrait correspondre
        $otherProducts = Product::where('top_produit', true)->whereNot('slug', $products[0]->slug)->distinct('slug')->inRandomOrder()->take(4)->get();
        $mes_stacks = Stack::with('products')->where('entreprise_id', Auth::user()->entreprise_id)->get();

        return Inertia::render("Catalogue/Product", [
            'current_product'   =>  $product,
            'products'          =>  $products,
            'categories'        =>  $categories,
            'sous_categories'   =>  $sous_categories,
            'current_categorie' =>  $request->souscategorie,
            'otherProducts'     =>  $otherProducts,
            'mes_stacks'        =>  $mes_stacks
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function searchProducts(Request $request)
    {
        return response()->json([
            'searchResults' => Product::where('name', 'like', '%' . $request->searchInput . '%')->where('deleted', 0)->get()
        ]);
    }
}
