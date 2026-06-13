<?php

namespace App\Http\Controllers;

use App\Models\CommandeProduct;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Commande;
use App\Models\IonLicence;
use App\Models\Product;

class MesLicencesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $licences = CommandeProduct::where('entreprise_id', Auth::user()->entreprise_id)
            ->where('categorie', 'licences')
            ->whereIn('status', ['active', 'provisioning'])
            ->get();

        $groupedLicenses = $licences->groupBy('name')->map(function ($items, $key) {
            return [
                'name' => $key,
                'image_principale' => $items->first()->image_principale,
                'fournisseur' => $items->first()->fournisseur,
                'total' => $items->whereIn('status', ['active', 'provisioning'])->count(),
                // 'prix_u' => $items->first()->prix,
                'available' => $items->whereNull('user_attributed_id')->where('status', 'active')->count(),
                'assigned' => $items->whereNotNull('user_attributed_id')->where('status', 'active')->count(),
                'on_hold' => $items->where('status', 'provisioning')->count(),
                'slug' => $items->first()->slug,
                'assignedUsers' => $items->whereNotNull('user_attributed_id')->map(function ($item) {
                    return [
                        'name' => User::where('id', $item->user_attributed_id)->first()->name,
                        'profile_img' => User::where('id', $item->user_attributed_id)->first()->profile_img,
                    ];
                }),
            ];
        });
        $groupedLicenses = $groupedLicenses->values()->toArray();

        return Inertia::render('Licences/Index', [
            'mes_licences' => $groupedLicenses
        ]);
    }
    public function licence(Request $request)
    {
        $licences = CommandeProduct::with(['userAttributed', 'userAttributed.tags'])
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->where('categorie', 'licences')
            ->where('slug', $request->slug)
            ->whereIn('status', ['active', 'provisioning'])
            ->get();

        if ($licences->isEmpty()) {
            return redirect()->route('mes-licences');
        }

        if ($licences[0]->fournisseur === 'microsoft' && $licences[0]->ref_fournisseur !== null)
            $licences[0]->product_id = Product::where('ref_fournisseur', $licences[0]->ref_fournisseur)->first()->id;
        else if ($licences[0]->fournisseur === 'google')
            $licences[0]->product_id = Product::where('reference_id', $licences[0]->reference_id)->where('name', $licences[0]->name)->first()->id;
        else
            $licences[0]->product_id = null;

        $users = User::with(['commandeProducts', 'tags'])
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->where('type', 'Personne')
            ->where('tenant_user_id', '!=', null)
            ->whereDoesntHave('commandeProducts', function ($query) use ($licences) {
                $query->where('name', $licences->first()->name);
            })
            ->get();

        return Inertia::render('Licences/Licence', [
            'licences' =>  $licences,
            'users' =>  $users
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function searchLicences(Request $request)
    {
        $licences = CommandeProduct::where('entreprise_id', Auth::user()->entreprise_id)
            ->where('categorie', 'licences')
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->searchInput . '%');
            })
            ->get();

        $groupedLicenses = $licences->groupBy('name')->map(function ($items, $key) {
            return [
                'name' => $key,
                'image_principal' => $items->first()->image_principal,
                'fournisseur' => $items->first()->fournisseur,
                'total' => $items->count(),
                'prix' => $items->first()->prix,
                'available' => $items->whereNull('user_attributed_id')->where('status', 'COMPLETED')->count(),
                'assigned' => $items->whereNotNull('user_attributed_id')->count(),
                'on_hold' => $items->where('status', 'ON_HOLD')->count(),
            ];
        });
        $groupedLicenses = $groupedLicenses->values()->toArray();

        return response()->json([
            'searchResults' =>  $groupedLicenses
        ]);
    }
    public function unassignLicence(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer'
        ]);

        $commandeProductId = $validatedData['id'];
        $commandeProduct = CommandeProduct::find($commandeProductId);

        $user = User::where('id', $commandeProduct->user_attributed_id)->first();
        if (!$user->tenant_user_id && $user->tenant_name === 'microsoft') {
            return response()->json([
                'message' => 'L\'utilisateur n\'est pas synchronisé avec Microsoft Entra ID',
            ], 400);
        }

        try {
            if ($user->tenant_name === 'microsoft') {
                $token = $this->microsoftService->getAccessToken();
                $this->microsoftService->unAssignLicence($token, $user->tenant_user_id, $commandeProduct->guid);
            }

            $commandeProduct->user_attributed_id = null;
            $commandeProduct->save();

            $users = User::with(['commandeProducts', 'tags'])
                ->where('entreprise_id', Auth::user()->entreprise_id)
                ->where('type', 'Personne')
                ->whereDoesntHave('commandeProducts', function ($query) use ($commandeProduct) {
                    $query->where('name', $commandeProduct->name);
                })
                ->get();

            return response()->json([
                'licences' => CommandeProduct::with(['userAttributed', 'userAttributed.tags'])
                    ->where('entreprise_id', Auth::user()->entreprise_id)
                    ->where('categorie', 'licences')
                    ->where('slug', $commandeProduct->slug)
                    ->get(),
                'user' => $user,
                'users' => $users
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la désattribution de la licence',
                'error' => $th->getMessage()
            ], 400);
        }
    }
    public function assignLicence(Request $request)
    {
        $validatedData = $request->validate([
            'licence_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        $commandeProductId = $validatedData['licence_id'];
        $commandeProduct = CommandeProduct::find($commandeProductId);

        $userId = $validatedData['user_id'];
        $user = User::find($userId);
        if (!$user->tenant_user_id && $user->tenant_name === 'microsoft') {
            return response()->json([
                'message' => 'L\'utilisateur n\'est pas synchronisé avec Microsoft Entra ID',
            ], 400);
        }

        try {
            if ($user->tenant_name === 'microsoft') {
                $token = $this->microsoftService->getAccessToken();
                $this->microsoftService->assignLicence($token, $user->tenant_user_id, $commandeProduct->guid);
            }

            $commandeProduct->user_attributed_id = $userId;
            $commandeProduct->save();

            $users = User::with(['commandeProducts', 'tags'])
                ->where('entreprise_id', Auth::user()->entreprise_id)
                ->where('type', 'Personne')
                ->whereDoesntHave('commandeProducts', function ($query) use ($commandeProduct) {
                    $query->where('name', $commandeProduct->name);
                })
                ->get();

            return response()->json([
                'licences' => CommandeProduct::with(['userAttributed', 'userAttributed.tags'])
                    ->where('entreprise_id', Auth::user()->entreprise_id)
                    ->where('categorie', 'licences')
                    ->where('slug', $commandeProduct->slug)
                    ->get(),
                'users' => $users,
                'user' => User::with(['commandeProducts', 'tags'])->where('id', $userId)->first()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'attribution de la licence',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
