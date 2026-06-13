<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\CommandeProduct;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mockery\Undefined;

class MesContratsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $mes_contrats = Commande::with('commandeProducts')->where('entreprise_id', Auth::user()->entreprise_id)->where('date_debut_contrat', '!=', null)->get();

        return Inertia::render('MesContrats/Index', [
            "mes_contrats"  =>  $mes_contrats
        ]);
    }
    public function contrat($reference_commande = null)
    {
        if (!$contrat = Commande::with(['commandeProducts', 'commandeProducts.userAttributed'])->where('reference_commande', $reference_commande)->first()) {
            return redirect()->route('mes-contrats');
        }

        return Inertia::render('MesContrats/Contrat', [
            "contrat"  =>  $contrat,
            "equipement_available" => CommandeProduct::where('sous_categorie', '!=', 'licences')->where('entreprise_id', Auth::user()->entreprise_id)->whereNull('commande_id')->get(),
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function searchContrats(Request $request)
    {
        return response()->json([
            'searchResults' => Commande::with('commandeProducts')->where('entreprise_id', Auth::user()->entreprise_id)->where('date_debut_contrat', '!=', null)->where('reference_commande', 'like', '%' . $request->searchInput . '%')->get()
        ]);
    }

    public function createContrat(Request $request)
    {
        $request->validate([
            'numero_contrat' => 'required',
            'date_debut_contrat' => 'required',
            'date_fin_contrat' => 'required',
        ]);

        $user = Auth::user();

        $contrat = Commande::create([
            'reference_commande' => $request->numero_contrat,
            'date_debut_contrat' => $request->date_debut_contrat,
            'date_fin_contrat' => $request->date_fin_contrat,
            'entreprise_id' => $user->entreprise_id,
            'user_id' => $user->id,
            'statut' => 'Terminée',
            'contrat_signe' => null,
        ]);

        if ($request->hasFile('contrat_signe')) {
            $contrat_signe_name = $user->name . "/contrat_financement_" . $contrat->reference_commande;
            Storage::disk('s3')->put($contrat_signe_name, file_get_contents($request->file("contrat_signe")), 'public');
            $contrat_signe_url = Storage::disk('s3')->url($contrat_signe_name);

            $contrat->update([
                'contrat_signe' => $contrat_signe_url,
            ]);
        }

        return response()->json(Commande::with('commandeProducts')->where('entreprise_id', Auth::user()->entreprise_id)->where('date_debut_contrat', '!=', null)->get());
    }

    public function modifyContrat(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'numero_contrat' => '',
            'date_debut_contrat' => '',
            'date_fin_contrat' => '',
        ]);

        $user = Auth::user();

        $contrat = Commande::with(['commandeProducts', 'commandeProducts.userAttributed'])->find($request->id);

        if ($request->hasFile('contrat_signe')) {
            $contrat_signe_name = $user->name . "/contrat_financement_" . $contrat->reference_commande;
            Storage::disk('s3')->put($contrat_signe_name, file_get_contents($request->file("contrat_signe")), 'public');
            $contrat_signe_url = Storage::disk('s3')->url($contrat_signe_name);
            $contrat->contrat_signe = $contrat_signe_url;
        }

        $fields = [
            'numero_contrat',
            'date_debut_contrat',
            'date_fin_contrat'
        ];
        foreach ($fields as $field) {
            if ($request->filled($field)) {
                $contrat->$field = $request->input($field);
            }
        }

        $contrat->save();

        return response()->json($contrat);
    }

    public function editEquipementToContrat(Request $request)
    {
        $request->validate([
            'contrat_id' => 'required|integer',
            'product_id' => 'required|integer',
            'action' => '',
        ]);

        $product = CommandeProduct::find($request->product_id);
        $product->update([
            'commande_id' => $request->action == 'retirer' ? null : $request->contrat_id,
            'type_contrat' => $request->type_contrat ?? $product->type_contrat,
            'prix' => $request->prix ?? $product->prix,
        ]);

        $contrat = Commande::with(['commandeProducts', 'commandeProducts.userAttributed'])->find($request->contrat_id);

        return response()->json([
            'contrat' => $contrat,
            'equipement_available' => CommandeProduct::where('sous_categorie', '!=', 'licences')->where('entreprise_id', Auth::user()->entreprise_id)->whereNull('commande_id')->get(),
        ]);
    }
}
