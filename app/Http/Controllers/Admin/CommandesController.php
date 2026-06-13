<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeProduct;
use App\Models\Historique;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommandesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $commandes = Commande::with(['commandeProducts', 'entreprise'])
            ->where(function ($query) {
                $query->where('statut', '!=', 'Terminée')
                    ->orWhere('statut', '!=', 'Erreur');
            })->get();

        return Inertia::render('Admin/Commandes/Index', [
            'commandes' =>  $commandes
        ]);
    }
    public function commande($reference_commande = null)
    {
        if (!$commande = Commande::with(['entreprise', 'signataire'])->where('reference_commande', $reference_commande)->first())
            return redirect('/commandes');

        $products = CommandeProduct::with(['adresseLivraison', 'userLivraison'])
            ->where('commande_id', $commande->id)
            ->where('categorie', '!=', 'licences')
            ->get();

        $commande->products = $products->toArray();
        $commande->totalQuantity = $products->sum('quantity');

        return Inertia::render('Admin/Commandes/Commande', [
            'commande'  =>  $commande,
            'allProducts' => $commande->products
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function filterCommande(Request $request)
    {
        return Commande::with(['commandeProducts', 'entreprise'])->whereIn('statut', $request->input('statuts', []))->get();
    }
    public function searchCommande(Request $request)
    {
        $searchInput = $request->searchInput ?? '';
        $selectedStatuses = $request->statuts ?? [];

        $query = Commande::query();

        if (!empty($searchInput)) {
            $query->where(function ($subQuery) use ($searchInput) {
                $subQuery->where('reference_commande', 'like', '%' . $searchInput . '%')
                    ->orWhereHas('entreprise', function ($userQuery) use ($searchInput) {
                        $userQuery->where('raison_sociale', 'like', '%' . $searchInput . '%');
                    });
            });
        }

        if (!empty($selectedStatuses)) {
            $query->whereIn('statut', $selectedStatuses);
        }

        $query->with(['entreprise', 'commandeProducts']);

        return $query->get();
    }
    public function financeur(Request $request)
    {
        $validatedData = $request->validate([
            'financeur' => 'required',
            'lien_contrat' => 'required',
            'id' => 'required|integer'
        ]);

        $commandeId = $validatedData['id'];
        Commande::where('id', $commandeId)->update([
            'financeur' => $validatedData['financeur'],
            'lien_contrat' => $validatedData['lien_contrat'],
            'statut' => "En attente de signature",
            'date_financement' => now()->toIso8601String()
        ]);

        $commande = Commande::find($commandeId);
        $user = User::find($commande->user_id);

        $this->emailService->sendEmail([
            'templateId' => 4,
            'to' => [['email' => $user->email]],
            'params' => [
                'reference_commande' => $commande->reference_commande,
                'cta' => env('APP_URL') . "/mes-commandes/" . $commande->reference_commande,
                'destinataire' => $user->email
            ]
        ]);
    }
    public function validation(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer'
        ]);

        $commandeId = $validatedData['id'];
        Commande::where('id', $commandeId)
            ->update([
                'statut' => "Livraison en cours",
                'date_validation' => now()->toIso8601String()
            ]);
        $commande = Commande::with('commandeProducts')->find($commandeId);
        $user = User::find($commande->user_id);

        $this->emailService->sendEmail([
            'templateId' => 6,
            'to' => [['email' => $user->email]],
            'params' => [
                'reference_commande' => $commande->reference_commande,
                'cta' => env('APP_URL') . "/mes-commandes/" . $commande->reference_commande,
                'destinataire' => $user->email
            ]
        ]);

        $commandeProducts = $commande->commandeProducts;
        foreach ($commandeProducts as $commandeProduct) {
            $userLivraison = User::find($commandeProduct->user_livraison_id);
            if ($userLivraison->email) {
                $this->emailService->sendEmail([
                    'templateId' => 9,
                    'to' => [['email' => $userLivraison->email]],
                    'params' => [
                        'name' => $userLivraison->name,
                        'destinataire' => $userLivraison->email,
                    ]
                ]);
            }

            $commandeProduct->update(['status' => 'En service']);
            Historique::create([
                'title' => "Changement d'état",
                'description' => "Mise en service de l'équipement",
                'equipement_id' => $commandeProduct->id,
            ]);
        }
    }
    public function confirmationAchat(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer'
        ]);

        $commandeId = $validatedData['id'];
        Commande::where('id', $commandeId)
            ->update([
                'statut' => "Livraison en cours",
            ]);
        $commande = Commande::find($commandeId);
        $user = User::find($commande->user_id);
    }
    public function sign_again(Request $request)
    {
        $validatedData = $request->validate([
            'lien_contrat' => '',
            'id' => 'required|integer'
        ]);

        $commandeId = $validatedData['id'];
        Commande::where('id', $commandeId)
            ->update([
                'statut' => "En attente de signature",
                'sign_again' => 1,
                'date_validation' => null,
                'date_signature' => now()->toIso8601String()
            ]);
        $commande = Commande::find($commandeId);
        $user = User::find($commande->user_id);

        $this->emailService->sendEmail([
            'templateId' => 5,
            'to' => [['email' => $user->email]],
            'params' => [
                'reference_commande' => $commande->reference_commande,
                'cta' => env('APP_URL') . "/mes-commandes/" . $commande->reference_commande,
                'destinataire' => $user->email
            ]
        ]);
    }
    public function livraison(Request $request)
    {
        $commande = Commande::find($request->commandeId);
        $user = User::find($commande->user_id);
        $liensLivraison = $request->input('liensLivraison', []);
        $numerosUnique = $request->input('numerosUnique', []);

        foreach ($numerosUnique as $item) {
            CommandeProduct::where('id', $item['id'])->update(['numero_unique' => $item['numeroUnique']]);
        }

        foreach ($liensLivraison as $item) {
            CommandeProduct::where('id', $item['id'])->update(['lien_livraison' => $item['lienLivraison']]);
        }

        $this->emailService->sendEmail([
            'templateId' => 11,
            'to' => [['email' => $user->email]],
            'params' => [
                'reference_commande' => $commande->reference_commande,
                'cta' => env('APP_URL') . "/mes-commandes/" . $commande->reference_commande,
                'destinataire' => $user->email
            ]
        ]);
    }
    public function contrat(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
            'contrat_signe' => 'required',
            'date_debut' => 'required',
            'date_fin' => 'required'
        ]);

        $commande = Commande::find($validatedData['id']);
        $user = User::find($commande->user_id);
        if ($request->hasFile('contrat_signe')) {
            $contrat_signe_name = $user->name . "/contrat_financement_" . $commande->reference_commande;
            Storage::disk('s3')->put($contrat_signe_name, file_get_contents($request->file("contrat_signe")), 'public');
            $contrat_signe_url = Storage::disk('s3')->url($contrat_signe_name);
        }

        Commande::where('id', $validatedData['id'])
            ->update([
                'statut' => "Terminée",
                'contrat_signe' => $contrat_signe_url,
                'date_debut_contrat' => Carbon::createFromFormat('d/m/Y', $validatedData['date_debut'])->toIso8601String(),
                'date_fin_contrat' => Carbon::createFromFormat('d/m/Y', $validatedData['date_fin'])->toIso8601String()
            ]);
    }
    public function termine(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer'
        ]);

        Commande::where('id', $validatedData['id'])
            ->update([
                'statut' => "Contrat à transmettre",
                'date_termine' => now()->toIso8601String()
            ]);
    }
}
