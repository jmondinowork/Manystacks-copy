<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Support;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Commande;
use App\Models\CommandeProduct;
use Illuminate\Support\Facades\Route;

class SupportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index(Request $request, $id = null)
    {
        if (Route::currentRouteName() == 'supportsAdmin') {
            $supports = Support::with(['messages', 'messages.user', 'commande', 'equipement', 'user'])->where('status', 'En cours')->get();

            $firstSupport = $supports->first();
            $currentSupportId = $id ? intval($id) : ($firstSupport ? $firstSupport->id : null);
            return Inertia::render('Support', [
                'supports' => $supports,
                'currentSupportId' => $currentSupportId
            ]);
        }

        $supports = Support::with(['messages', 'messages.user', 'commande', 'equipement', 'user'])->where('entreprise_id', Auth::user()->entreprise_id)->where('status', 'En cours')->get();
        $commandes = Commande::with('commandeProducts')->where('entreprise_id', Auth::user()->entreprise_id)->get()->map(function ($commande) {
            $commande->products_count = $commande->commandeProducts->count();
            return $commande;
        });
        $equipements = CommandeProduct::where('entreprise_id', Auth::user()->entreprise_id)->get();

        $firstSupport = $supports->first();
        $currentSupportId = $id ? intval($id) : ($firstSupport ? $firstSupport->id : null);
        return Inertia::render('Support', [
            'supports' => $supports,
            'commandes' => $commandes,
            'equipements' => $equipements,
            'currentSupportId' => $currentSupportId
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function createSupport(Request $request)
    {
        $request->validate([
            'user_id' => '',
            'object' => 'required',
            'message' => 'required',
            'from' => 'required',
            'commande_id' => 'sometimes',
            'equipement_id' => 'sometimes'
        ]);

        $support = new Support();
        $support->object = $request->object;
        $support->numero_support = 'SUP' . time();
        $support->commande_id = $request->commande_id;
        $support->equipement_id = $request->equipement_id;
        $support->entreprise_id = Auth::user()->entreprise_id;
        $support->user_id = $request->user_id ?? Auth::id();
        $support->save();

        $support->messages()->create([
            'message' => $request->message,
            'from' => $request->from,
            'user_id' => $request->user_id ?? Auth::id(),
            'support_id' => $support->id,
        ]);

        $this->emailService->sendEmail([
            'templateId' => 3,
            'to' => [['email' => env('MANYSTACK_CONTACT')]],
            'params' => [
                'objet' => "Nouveau support",
                'title' => $support->object,
                'subtitle' => $request->message,
                'cta_title' => "Voir le support",
                'cta_url' => env('APP_URL') . "/supportsAdmin/" . $support->id
            ]
        ]);

        $this->emailService->sendEmail([
            'templateId' => 14,
            'to' => [['email' => Auth::user()->email]],
            'params' => [
                'objet' => $support->object,
                'numero_support' => $support->numero_support,
                'cta' => env('APP_URL') . "/supports/" . $support->id,
                'destinataire' => Auth::user()->email
            ]
        ]);

        $support->load(['messages', 'messages.user', 'commande', 'equipement']);
        return response()->json([
            'supports' => Support::with(['messages', 'messages.user', 'commande', 'equipement', 'user'])
                ->where(function ($query) use ($request) {
                    if ($request->user_id)
                        $query->where('user_id', $request->user_id);
                    else
                        $query->where('entreprise_id', Auth::user()->entreprise_id);
                })->where('status', 'En cours')
                ->get(),
            'ticket' => $support
        ]);
    }
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'id' => 'required',
            'from' => 'required'
        ]);

        $support = Support::find($request->id);

        $support->messages()->create([
            'message' => $request->message,
            'from' => $request->from,
            'user_id' => Auth::id(),
            'support_id' => $support->id,
        ]);

        if ($request->from == 'user') {
            $this->emailService->sendEmail([
                'templateId' => 3,
                'to' => [['email' => env('MANYSTACK_CONTACT')]],
                'params' => [
                    'objet' => "Nouveau message sur le support",
                    'title' => $support->object,
                    'subtitle' => $request->message,
                    'cta_title' => "Voir le support",
                    'cta_url' => env('APP_URL') . "/supportsAdmin/" . $support->id
                ]
            ]);

            return response()->json(Support::with(['messages', 'messages.user', 'commande', 'equipement'])->where('entreprise_id', Auth::user()->entreprise_id)->where('status', 'En cours')->get());
        } else {
            $email = Support::with('user')->find($request->id)->user->email;
            $this->emailService->sendEmail([
                'templateId' => 12,
                'to' => [['email' => $email]],
                'params' => [
                    'objet' => $support->object,
                    'numero_support' => $support->numero_support,
                    'cta' => env('APP_URL') . "/supports/" . $support->id,
                    'destinataire' => $email
                ]
            ]);

            return response()->json(Support::with(['messages', 'messages.user', 'commande', 'equipement'])->where('status', 'En cours')->get());
        }
    }
    public function seachSupports(Request $request)
    {
        if ($request->input('superadmin') == 'true') {
            return response()->json([
                'searchResults' => Support::with(['messages', 'messages.user', 'commande', 'equipement', 'user'])->where('object', 'like', "%$request->searchInput%")->orWhere('numero_support', 'like', "%$request->searchInput%")->get()
            ]);
        }

        return response()->json([
            'searchResults' => Support::with(['messages', 'messages.user', 'commande', 'equipement', 'user'])->where('entreprise_id', Auth::user()->entreprise_id)->where('object', 'like', "%$request->searchInput%")->orWhere('numero_support', 'like', "%$request->searchInput%")->get()
        ]);
    }
    public function clotureTicket(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $support = Support::find($request->id);
        $support->status = 'Résolu';
        $support->save();

        $email = Support::with('user')->find($request->id)->user->email;
        $this->emailService->sendEmail([
            'templateId' => 12,
            'to' => [['email' => $email]],
            'params' => [
                'objet' => $support->object,
                'numero_support' => $support->numero_support,
                'destinataire' => $email
            ]
        ]);
    }
}
