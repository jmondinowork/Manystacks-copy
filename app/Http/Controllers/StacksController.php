<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\Stack;


class StacksController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $mes_stacks = Stack::with('products')->where('entreprise_id', Auth::user()->entreprise_id)->get();

        return Inertia::render('Catalogue/MesStacks/Index', [
            'title'         =>  'Mes stacks',
            'mes_stacks'    =>  $mes_stacks
        ]);
    }
    public function stack($slug = null)
    {
        $stack = Stack::with('products')->where('slug', $slug)->first();
        $mes_stacks = Stack::with('products')->where('entreprise_id', Auth::user()->entreprise_id)->get();


        return Inertia::render('Catalogue/MesStacks/Stack', [
            'stack'         =>  $stack,
            'mes_stacks'    =>  $mes_stacks
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function addToStack(Request $request)
    {
        $product = Product::find($request->product_id);
        $stack = Stack::find($request->stack_id);

        if (!$product || !$stack)
            return response()->json(['error' => 'Produit ou Stack introuvable'], 404);

        if ($stack->products()->where('product_id', $product->id)->exists())
            return response()->json(['error' => 'Le produit est déjà dans la stack'], 409);

        $stack->products()->attach($product->id);

        return Stack::with('products')->where('entreprise_id', Auth::user()->entreprise_id)->get();
    }
    public function createStack(Request $request)
    {
        $validatedData = $request->validate([
            'stack_name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'id' => 'required|integer'
        ]);

        $stack = Stack::updateOrCreate(
            ['id' => $validatedData['id']],
            array_merge($validatedData, ['entreprise_id' => Auth::user()->entreprise_id])
        );

        $stack->slug = Str::slug($request->stack_name) . "-{$stack->id}";
        $stack->save();

        return Stack::with('products')->where('entreprise_id', Auth::user()->entreprise_id)->get();
    }

    public function removeProductFromStack(Request $request)
    {
        $stack = Stack::find($request->stack_id);
        $stack->products()->detach($request->product_id);

        return Stack::with('products')->where('id', $request->stack_id)->first();
    }
    public function deleteStack(Request $request)
    {
        Stack::find($request->stack_id)->delete();

        return redirect('/mes-stacks');
    }
}
