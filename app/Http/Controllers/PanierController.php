<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Panier;
use App\Models\Product;
use App\Models\Stack;
use App\Models\PanierProduct;
use Illuminate\Support\Facades\Auth;


class PanierController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $panier = Panier::with(['panierProducts.product', 'user'])
            ->where('user_id', Auth::id())
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->where('status', 'pending')
            ->first();
        $recommendation = Product::where('top_produit', 1)->take(3)->get();
        $categories = Product::select('categorie')->distinct()->orderBy('categorie')->pluck('categorie');

        return Inertia::render('Catalogue/Panier', [
            'title'             =>  'Panier',
            'panier'            =>  $panier,
            'recommendation'    =>  $recommendation,
            'categories'        =>  $categories
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function addToPanier(Request $request)
    {
        $panier = Panier::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => 'pending', 'entreprise_id' => Auth::user()->entreprise_id]
        );

        $panierProduct = $panier->panierProducts()->firstOrCreate(
            [
                'product_id' => $request->product_id,
                'type_contrat' => $request->type_contrat
            ],
            ['quantity' => 0]
        );

        $panierProduct->increment('quantity', $request->quantity ?? 1);

        return Panier::with(['panierProducts.product', 'user'])
            ->where('user_id', Auth::id())
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->where('status', 'pending')
            ->first();
    }
    public function addStackToPanier(Request $request)
    {
        $stack = Stack::with('products')->where('id', $request->stack_id)->first();

        $panier = Panier::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => 'pending', 'entreprise_id' => Auth::user()->entreprise_id]
        );

        foreach ($stack->products as $product) {
            $panierProduct = $panier->panierProducts()->firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0]
            );

            $panierProduct->increment('quantity', 1);
        }

        return Panier::with(['panierProducts.product', 'user'])
            ->where('user_id', Auth::id())
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->first();
    }
    public function panierLength()
    {
        $panier = Panier::where('user_id', Auth::id())->where('status', 'pending')->where('entreprise_id', Auth::user()->entreprise_id)->first();

        if ($panier) {
            $totalItems = PanierProduct::where('panier_id', $panier->id)->sum('quantity');
            return response()->json($totalItems);
        }

        return response()->json(0);
    }
    public function updateQuantity(Request $request)
    {
        $panierId = $request->panierId;
        $productId = $request->productId;
        $quantity = $request->quantity;

        $panierProduct = PanierProduct::where('panier_id', $panierId)
            ->where('product_id', $productId)
            ->first();

        if ($panierProduct) {
            $panierProduct->quantity = $quantity;
            $panierProduct->save();

            return response()->json(['message' => 'Quantité mise à jour avec succès']);
        }

        return response()->json(['error' => 'Article non trouvé'], 404);
    }

    public function removeItem(Request $request)
    {
        $productId = $request->productId;
        $panierId = $request->panierId;

        $panierProduct = PanierProduct::where('panier_id', $panierId)
            ->where('product_id', $productId)
            ->first();

        if ($panierProduct) {
            $panierProduct->delete();

            return response()->json(['message' => 'Article supprimé avec succès']);
        }

        return response()->json(['error' => 'Article non trouvé'], 404);
    }

    public function sendPanier(Request $request)
    {
        $panier = Panier::where('user_id', Auth::id())->where('status', 'pending')->where('entreprise_id', Auth::user()->entreprise_id)->first();

        if ($panier) {
            $panier->status = 'sent';
            $panier->save();

            return response()->json(['message' => 'Panier envoyé avec succès']);
        }

        return response()->json(['error' => 'Panier non trouvé'], 404);
    }
}
