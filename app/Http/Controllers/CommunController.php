<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Commande;
use App\Models\CommandeProduct;
use App\Models\Support;
use App\Models\Product;

class CommunController extends Controller
{
    public function filterSearch(Request $request)
    {
        $table = $request->input('table');
        $colonnes = $request->input('colonnes');
        $relationColonne = $request->input('relationColonne');
        $format = $request->input('format');

        if ($table == 'commande_products') {
            $query = CommandeProduct::query();
            if ($format == 'licences') {
                $query->where('categorie', 'licences');
                $query->whereIn('status', ['active', 'provisioning']);
            } else {
                $query->where('categorie', '!=', 'licences');
                $query->where('status', '!=', 'pending');
                $query->with(['userAttributed', 'userAttributed.tags']);
            }
        } else if ($table == 'equipe') {
            $query = User::query();
            $query->where('type', 'Personne');
            $query->with(['commandeProducts', 'tags']);
        } else if ($table == 'salles') {
            $query = User::query();
            $query->where('type', 'Salle');
            $query->with(['commandeProducts', 'tags']);
        } else if ($table == 'commandes') {
            $query = Commande::query();
            $query->with(['commandeProducts']);
        } else if ($table == 'supports') {
            $query = Support::query();
            $query->with(['messages', 'messages.user', 'commande', 'equipement', 'user']);
        } else if ($table == 'products') {
            $query = Product::query();
        }

        if ($request->input('superadmin') == false)
            $query->where('entreprise_id', Auth::user()->entreprise_id);

        foreach ($colonnes as $colonne) {
            $selectedOptions = collect($request->input($colonne))->filter(function ($option) {
                return $option['selected'] == true;
            })->pluck('title')->toArray();

            if (!empty($selectedOptions)) {
                // Vérifier si "Aucun" est présent dans les options sélectionnées
                $includeNull = in_array('Aucun', $selectedOptions);
                if ($includeNull) {
                    // On retire "Aucun" du tableau pour ne pas le comparer dans le whereIn
                    $selectedOptions = array_diff($selectedOptions, ['Aucun']);
                }

                if (method_exists($query->getModel(), $colonne)) {
                    // Si la colonne est une relation, on utilise whereHas
                    $query->whereHas($colonne, function ($query) use ($selectedOptions, $relationColonne, $includeNull) {
                        $query->where(function ($query) use ($selectedOptions, $relationColonne, $includeNull) {
                            if (!empty($selectedOptions)) {
                                $query->whereIn($relationColonne, $selectedOptions);
                                if ($includeNull) {
                                    $query->orWhereNull($relationColonne);
                                }
                            } elseif ($includeNull) {
                                $query->whereNull($relationColonne);
                            }
                        });
                    });
                } else {
                    // Sinon, on applique la condition directement sur la colonne
                    $query->where(function ($query) use ($colonne, $selectedOptions, $includeNull) {
                        if (!empty($selectedOptions)) {
                            $query->whereIn($colonne, $selectedOptions);
                            if ($includeNull) {
                                $query->orWhereNull($colonne);
                            }
                        } elseif ($includeNull) {
                            $query->whereNull($colonne);
                        }
                    });
                }
            }
        }

        $filteredData = $query->get();

        if ($format == 'licences') {
            $filteredData = $filteredData->groupBy('name')->map(function ($items, $key) {
                return [
                    'name' => $key,
                    'image_principale' => $items->first()->image_principale,
                    'fournisseur' => $items->first()->fournisseur,
                    'total' => $items->whereIn('status', ['active', 'provisioning'])->count(),
                    'prix_u' => $items->first()->prix,
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
            $filteredData = $filteredData->values()->toArray();
        }

        return response()->json($filteredData);
    }

    public function deleteRecord(Request $request)
    {
        $request->validate([
            'table' => 'required',
            'ids' => 'required'
        ]);
        $modelName = 'App\Models\\' . ucfirst($request->input('table'));

        $ids = $request->input('ids');
        $deleted = 0;
        foreach ($ids as $id) {
            $model = $modelName::find($id);
            if ($request->input('table') == 'commandeProduct' && $model->ref_fournisseur != null) {
                continue;
            }
            if ($request->input('table') == 'user') {
                if ($model->id == Auth::user()->id) continue;
                if ($model->tenant_user_id && $model->tenant_name === 'microsoft') {
                    $token = $this->microsoftService->getAccessToken();
                    $this->microsoftService->deleteUser($token, $model->tenant_user_id);
                } else if ($model->tenant_user_id && $model->tenant_name === 'google') {
                    $token = $this->googleService->getAccessToken();
                    $this->googleService->deleteUser($token, $model->tenant_user_id);
                }
                CommandeProduct::where('user_attributed_id', $model->id)->update(['user_attributed_id' => null]);
            }
            if ($model) {
                $model->delete();
                $deleted++;
            }
        }

        return response()->json(['deleted' => $deleted]);
    }
}
