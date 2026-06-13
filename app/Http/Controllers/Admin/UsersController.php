<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntrepriseInformation;
use App\Models\AdresseLivraison;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function usersAdmin()
    {
        return Inertia::render('Admin/Entreprises/Index', [
            'usersAdmin' => EntrepriseInformation::get()
        ]);
    }

    public function entreprise($entrepriseId)
    {
        $entreprise = EntrepriseInformation::find($entrepriseId);
        $siblings = $entreprise->siblings();
        $entreprise->siblings = $siblings;

        return Inertia::render('Admin/Entreprises/Entreprise', [
            'entreprise' => $entreprise,
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function linkEntreprise(Request $request)
    {
        $request->validate([
            'siret' => 'required',
            'currentEntrepriseId' => 'required',
        ]);

        if ($entreprise = $this->createEntreprise($request->siret)) {
            $currentEntreprise = EntrepriseInformation::find($request->currentEntrepriseId)->with('siblings')->first();

            if ($currentEntreprise->group_id) {
                $entreprise->group_id = $currentEntreprise->group_id;
                $entreprise->save();
            } else {
                $currentEntreprise->group_id = $currentEntreprise->id;
                $currentEntreprise->save();

                $entreprise->group_id = $currentEntreprise->group_id;
                $entreprise->save();
            }

            return response()->json([
                'entreprise' => $currentEntreprise
            ]);
        } else {
            return response()->json([
                'message' => 'Aucune entreprise trouvée pour ce siret',
            ], 404);
        }
    }
    public function searchUsers(Request $request)
    {
        $request->validate([
            'searchInput' => 'required',
        ]);

        if ($request->input('superadmin') == 'true') {
            return response()->json([
                'searchResults' => User::with(['entreprise'])
                    ->where('type', '=', 'Personne')
                    ->where(function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->searchInput . '%')
                            ->orWhere('email', 'like', '%' . $request->searchInput . '%')
                            ->orWhereHas('entreprise', function ($query) use ($request) {
                                $query->where('raison_sociale', 'like', '%' . $request->searchInput . '%');
                            });
                    })->get()
            ]);
        }
    }
}
