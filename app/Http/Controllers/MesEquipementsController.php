<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CommandeProduct;
use App\Models\Historique;
use App\Models\Product;
use App\Models\User;
use App\Models\AdresseLivraison;
use App\Models\Tag;
use Aws\History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use League\Csv\Reader;
use Exception;
use SplFileObject;

class MesEquipementsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $equipements = CommandeProduct::with(['userAttributed', 'userAttributed.tags'])
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->where('sous_categorie', '!=', 'licences')
            ->where('status', '!=', 'pending')
            ->orderBy('id', 'desc')
            ->get();

        $typeEquipement = Product::select('sous_categorie', 'categorie')
            ->distinct()
            ->get()->toArray();

        return Inertia::render('MesEquipements/Index', [
            "mes_equipements"   =>  $equipements,
            "typeEquipement"    =>  $typeEquipement
        ]);
    }

    public function equipement($equipement_id)
    {
        if (!$equipement = CommandeProduct::with(['userAttributed', 'commande', 'commande.commandeProducts', 'userAttributed.tags'])->where('id', $equipement_id)->where('entreprise_id', Auth::user()->entreprise_id)->first()) {
            return redirect()->route('mes-equipements');
        }

        if ($equipement->enrollment_id) {
            $token = $this->microsoftService->getAccessToken();
            $device = $this->microsoftService->getManagedDevice($token, $equipement->enrollment_id);
            if ($device)
                $equipement->deviceDatas = $device;
        } else {
            $equipement->deviceDatas = ['deviceActionResults' => [['actionName' => 'reboot', 'actionState' => 'success', 'startDateTime' => '2021-09-01T08:00:00Z', 'lastUpdatedDateTime' => '2021-09-01T08:00:00Z']]];
        }

        $historiques = Historique::where('equipement_id', $equipement_id)->orderBy('id', 'desc')->take(10)->get();
        $attributions = User::with('tags')->where('entreprise_id', Auth::user()->entreprise_id)->where('type', '!=', '')->where('id', '!=', $equipement->user_attributed_id)->get();
        $adresses = AdresseLivraison::where('entreprise_id', Auth::user()->entreprise_id)->get();

        return Inertia::render('MesEquipements/Equipement', [
            "equipement"  =>  $equipement,
            "historiques"  =>  $historiques,
            "attributions"  =>  $attributions,
            "adresses"  =>  $adresses,
            "tags"  =>  Tag::where('entreprise_id', Auth::user()->entreprise_id)->get()
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function editEquipement(Request $request) // HORRIBLE A REFAIRE
    {
        $validated_data = $request->validate([
            'id' => 'required',
            'note' => 'sometimes|required',
            'status' => 'sometimes|required',
            'user_attributed_id' => 'sometimes',
        ]);

        $product = CommandeProduct::find($validated_data['id']);
        try {
            if ($product->categorie == 'licences') {
                $user = User::find($request->user_attribution);

                if ($product->fournisseur == 'microsoft') {
                    if (!$user->tenant_user_id)
                        return response()->json([
                            'message' => 'L\'utilisateur n\'est pas synchronisé avec Microsoft Entra ID',
                        ], 400);

                    $token = $this->microsoftService->getAccessToken();
                    if ($validated_data['user_attributed_id'])
                        $this->microsoftService->assignLicence($token, $user->tenant_user_id, $product->guid);
                    else
                        $this->microsoftService->unAssignLicence($token, $user->tenant_user_id, $product->guid);
                } else if ($product->fournisseur == 'google') {
                    if (!$user->tenant_user_id)
                        return response()->json([
                            'message' => 'L\'utilisateur n\'est pas synchronisé avec Google Workspace',
                        ], 400);

                    $token = $this->googleService->getAccessToken();
                    if ($validated_data['user_attributed_id'])
                        $this->googleService->assignLicence($token, $user->tenant_user_id, $product->guid, $product->reference_id);
                    else
                        $this->googleService->unAssignLicence($token, $user->tenant_user_id, $product->guid, $product->reference_id);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de' . ($validated_data['user_attributed_id'] ? ' l\'attribution' : ' la désattribution') . ' de la licence',
            ], 400);
        }

        $filtered_data = array_filter($validated_data, function ($value, $key) {
            return $key === 'user_attributed_id' || (!is_null($value) && $value !== '');
        }, ARRAY_FILTER_USE_BOTH);

        CommandeProduct::where('id', $validated_data['id'])->update($filtered_data);

        if (array_key_exists('status', $validated_data)) {
            $history = "";
            if ($validated_data['status'] === "En réserve" || $validated_data['status'] === "Hors service" || $validated_data['status'] === "En maintenance") {
                CommandeProduct::where('id', $validated_data['id'])->update(['user_attributed_id' => null]);
                $history = "L'équipement a été retiré à l'utilisateur";

                if ($validated_data['status'] === "En maintenance") {
                    $this->emailService->sendEmail([
                        'templateId' => 3,
                        'to' => [['email' => env('MANYSTACK_CONTACT')]],
                        'params' => [
                            'objet' => "Equipement en maintenance",
                            'title' => "Equipement en maintenance",
                            'subtitle' => "L'entreprise " . Auth::user()->entreprise->name . " a mis l'équipement " . CommandeProduct::find($validated_data['id'])->numero_unique . " en maintenance",
                            'cta_title' => "",
                            'cta_url' => ""
                        ]
                    ]);
                }
            }
            Historique::create([
                'title' => "Changement d'état",
                'description' => "Mise " . lcfirst($validated_data['status']) . " de l'équipement\n" . $history,
                'equipement_id' => $validated_data['id']
            ]);
        }
        if (array_key_exists('user_attributed_id', $validated_data)) {
            $description = $validated_data['user_attributed_id'] ?
                "Attribution de l'équipement à " . User::find($validated_data['user_attributed_id'])->name :
                "Retrait de l'équipement à " . $product->userAttributed->name;

            Historique::create([
                'title' => "Changement d'attribution",
                'description' => $description,
                'equipement_id' => $validated_data['id']
            ]);
        }

        $attributions = User::with(['commandeProducts', 'tags'])->where('id', $request->user_attribution)->first();
        if ($attributions)
            $licencesNames = $attributions->commandeProducts->where('sous_categorie', 'licences')->pluck('name')->unique()->toArray();
        else
            $licencesNames = [];

        $equipement_available = CommandeProduct::with(['userAttributed', 'userAttributed.tags'])
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->whereNotIn('name', $licencesNames)
            ->where('status', '!=', 'pending')
            ->whereNull('user_attributed_id')
            ->where(function ($query) use ($attributions) {
                if ($attributions && $attributions->type == 'Salle') {
                    $query->where('categorie', '!=', 'licences');
                } elseif ($query->where('categorie', 'licences')) {
                    $query->where('status', 'active');
                }
            })
            ->get();
        $unique_licences = $equipement_available->where('sous_categorie', 'licences')->unique('name');
        $other_products = $equipement_available->where('sous_categorie', '!=', 'licences');
        $equipement_available = $unique_licences->merge($other_products);

        return [
            'equipement' => $equipement = CommandeProduct::with(['userAttributed', 'commande', 'userAttributed.tags'])->where('id', $validated_data['id'])->first(),
            'historiques' => Historique::where('equipement_id', $validated_data['id'])->orderBy('id', 'desc')->take(10)->get(),
            'attribution' => $attributions,
            'attributions_available' => User::with('tags')->where('entreprise_id', Auth::user()->entreprise_id)->where('type', '!=', '')->where('id', '!=', $equipement->user_attributed_id)->get(),
            'equipement_available' => $equipement_available,
        ];
    }
    public function createEquipement(Request $request)
    {
        $allData = $request->all();
        $validatedData = $request->validate([
            'name' => 'required',
            'sous_categorie' => 'required',
            'status' => 'required',
        ]);
        $allData['entreprise_id'] = Auth::user()->entreprise_id;
        $allData['image_principale'] = "/images/created_" .  $validatedData['sous_categorie'] . "_icon.svg";

        $table = (new CommandeProduct)->getTable();
        $columns = Schema::getConnection()->getSchemaBuilder()->getColumnListing($table);
        $filteredData = array_filter(
            $allData,
            function ($key) use ($columns) {
                return in_array($key, $columns);
            },
            ARRAY_FILTER_USE_KEY
        );

        $product = CommandeProduct::create($filteredData);

        Historique::create([
            'title' => "Création d'équipement",
            'description' => "Création d'un nouvel équipement externe",
            'equipement_id' => $product->id
        ]);

        return CommandeProduct::with(['userAttributed', 'userAttributed.tags'])
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->where('sous_categorie', '!=', 'licences')
            ->where('status', '!=', 'pending')
            ->orderBy('id', 'desc')
            ->get();
    }
    public function searchEquipements(Request $request)
    {
        return response()->json([
            'searchResults' => CommandeProduct::with(['userAttributed', 'userAttributed.tags'])
                ->where('entreprise_id', Auth::user()->entreprise_id)
                ->where('sous_categorie', '!=', 'licences')
                ->where('status', '!=', 'pending')
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->searchInput . '%')
                        ->orWhere('numero_unique', 'like', '%' . $request->searchInput . '%')
                        ->orWhere('status', 'like', '%' . $request->searchInput . '%');
                })
                ->orderBy('id', 'desc')
                ->get()
        ]);
    }
    public function editCaracteristiqueImported(Request $request)
    {
        $allData = $request->all();
        $validatedData = $request->validate([
            'id' => 'required',
        ]);

        if ($request->hasFile('image_principale')) {
            $image_principale_name =  Auth::user()->name . "/image_principale_" . Str::random(6) . $request->name;
            Storage::disk('s3')->put($image_principale_name, file_get_contents($request->file("image_principale")), 'public');
            $image_principale_url = Storage::disk('s3')->url($image_principale_name);
        } else $image_principale_url = $request->image_principale ?? NULL;
        $allData['image_principale'] = $image_principale_url;

        $table = (new CommandeProduct)->getTable();
        $columns = Schema::getConnection()->getSchemaBuilder()->getColumnListing($table);
        $filteredData = array_filter(
            $allData,
            function ($key) use ($columns) {
                return in_array($key, $columns);
            },
            ARRAY_FILTER_USE_KEY
        );
        CommandeProduct::where('id', $validatedData['id'])->update($filteredData);

        Historique::create([
            'title' => "Modification de caractéristiques",
            'description' => "Modification des caractéristiques de l'équipement",
            'equipement_id' => $validatedData['id']
        ]);

        return CommandeProduct::with(['userAttributed', 'commande', 'commande.commandeProducts', 'userAttributed.tags'])->where('id', $validatedData['id'])->where('entreprise_id', Auth::user()->entreprise_id)->first();
    }

    public function importerCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $user = User::find($request->user_id) ?? Auth::user();

        $file = $request->file('file');
        $file_path = $file->storeAs('csv', $file->getClientOriginalName(), 'public');
        $filePath = storage_path('app/public/' . $file_path);
        $file = new SplFileObject($filePath, 'r');
        $firstLine = $file->fgets();
        $delimiters = [";", ",", "\t", "|"];
        $counts = [];
        foreach ($delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($firstLine, $delimiter);
        }
        $delimiter = array_search(max($counts), $counts);

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setDelimiter($delimiter);
        $csv->setHeaderOffset(0);

        try {
            foreach ($csv->getRecords() as $index => $record) {
                $record = array_map(function ($value) {
                    return $value === "" ? null : $value;
                }, $record);

                $requiredColumns = ['Nom', 'Catégorie'];
                $missingColumns = [];

                foreach ($requiredColumns as $column) {
                    if (!isset($record[$column]) || $record[$column] === null)
                        $missingColumns[] = $column;
                }
                if (!empty($missingColumns)) {
                    $missingColumnsList = implode(', ', $missingColumns);
                    throw new Exception("Il semblerait que la/les colonne(s) \"$missingColumnsList\" soient manquantes sur certains équipements.");
                }
            }

            foreach ($csv->getRecords() as $index => $record) {
                $record = array_map(function ($value) {
                    return $value === "" ? null : $value;
                }, $record);

                $typeEquipements = Product::select('categorie', 'sous_categorie')->distinct()->get()->toArray();
                $category = preg_replace('/\p{Mn}/u', '', \Normalizer::normalize(strtolower($record['Catégorie']), \Normalizer::FORM_D));
                $pattern = "/^" . preg_quote($category, '/') . "s?$/i";
                $currentCategory = "Autres";
                $currentSousCategory = "";
                foreach ($typeEquipements as $item) {
                    $normalizedSousCategorie = preg_replace(
                        '/\p{Mn}/u',
                        '',
                        \Normalizer::normalize(strtolower($item['sous_categorie']), \Normalizer::FORM_D)
                    );
                    if (preg_match($pattern, $normalizedSousCategorie)) {
                        $currentCategory = $item['categorie'];
                        $currentSousCategory = $item['sous_categorie'];
                        break;
                    }
                }


                $typeStatus = ['En service', 'En réserve', 'Hors service', 'En maintenance'];
                $status = $record['Status'];
                if (substr($status, -1) == 's') {
                    $status = substr($status, 0, -1);
                }
                $status = preg_replace('/\p{Mn}/u', '', \Normalizer::normalize(strtolower($status), \Normalizer::FORM_D));
                $typeStatusTransformed = array_map(function ($status) {
                    return preg_replace('/\p{Mn}/u', '', \Normalizer::normalize(strtolower($status), \Normalizer::FORM_D));
                }, $typeStatus);
                $pattern = "/^" . preg_quote($status, '/') . "$/i";
                $matches = preg_grep($pattern, $typeStatusTransformed);
                $currentStatus = !empty($matches) ? $typeStatus[key($matches)] : "En service";


                $product = CommandeProduct::create([
                    'name' => $record['Nom'],
                    'categorie' => $currentCategory,
                    'sous_categorie' => $record['Catégorie'],
                    'numero_unique' => $record['Numéro de série'],
                    'type_contrat' => $record['Type de contrat'],
                    'prix' => $record['Prix'],
                    'image_principale' => $record['Images'] ?? '/images/created_' . $currentSousCategory . '_icon.svg',
                    'etat' => $record['Etat'],
                    'status' => $currentStatus,
                    'config' => $record['Config'],
                    'IMEI1' => $record['IMEI 1'],
                    'IMEI2' => $record['IMEI 2'],
                    'couleur' => $record['Couleur'],
                    'systeme_exploitation' => $record["Système D'exploitation"],
                    'camera' => $record['Caméra'],
                    'carac_camera' => $record['Caractéristique Caméra'],
                    'autonomie_batterie' => $record['Autonomie Batterie'],
                    'marque' => $record['Marque'],
                    'modele' => $record['Modèle'],
                    'ram' => $record['RAM'],
                    'stockage' => $record['Stockage'],
                    'type_stockage' => $record['Type De Stockage'],
                    'processeur' => $record['Processeur'],
                    'type_ecran' => $record["Type D'écran"],
                    'resolution_ecran' => $record["Résolution écran"],
                    'frequence_ecran' => $record['Fréquence écran'],
                    'temps_reponse_ecran' => $record['Temps de réponse écran'],
                    'taille_ecran' => $record['Taille Écran'],
                    'luminosite_ecran' => $record['Luminosité écran'],
                    'carte_graphique' => $record['Carte Graphique'],
                    'connectivite' => $record['Connectivité'],
                    'connectique' => $record['Connectique'],
                    'alimentation' => $record['Alimentation'],
                    'audio' => $record['Audio'],
                    'dimension' => $record['Dimensions'],
                    'poids' => $record['Poids'],
                    'clavier' => $record['Clavier'],
                    'attribution_waiting' => $record['Mail attribution'],
                    'entreprise_id' => $user->entreprise_id
                ]);

                $userAttribution = null;
                if ($record['Mail attribution']) {
                    $userAttribution = User::where('email', $record['Mail attribution'])->orWhere('email_perso', $record['Mail attribution'])->first();
                    if ($userAttribution) {
                        $product->update(['user_attributed_id' => $userAttribution->id, 'attribution_waiting' => null]);
                        Historique::create([
                            'title' => "Attribution d'équipement",
                            'description' => "Attribution de l'équipement à " . $userAttribution->name,
                            'equipement_id' => $product->id
                        ]);
                    }
                }

                Historique::create([
                    'title' => "Importation d'équipement",
                    'description' => "Importation d'un nouvel équipement externe",
                    'equipement_id' => $product->id
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            Storage::disk('public')->delete($file_path);
        }
    }

    public function editMultipleEquipements(Request $request)
    {
        $validatedData = $request->validate([
            'equipements' => 'required',
            'status' => 'required'
        ]);

        $equipements = CommandeProduct::whereIn('id', $validatedData['equipements'])->where('entreprise_id', Auth::user()->entreprise_id)->get();
        $equipements->each(function ($equipement) use ($validatedData) {
            $equipement->update(['status' => $validatedData['status']]);
            Historique::create([
                'title' => "Changement d'état",
                'description' => "Mise " . lcfirst($validatedData['status']) . " de l'équipement",
                'equipement_id' => $equipement->id
            ]);
        });
    }

    public function deleteEquipement(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required'
        ]);

        CommandeProduct::where('id', $validatedData['id'])->delete();
    }

    public function enrollEquipement(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required',
        ]);

        $product = CommandeProduct::find($validatedData['id']);

        $token = $this->microsoftService->getAccessToken();
        $enrolDevices = $this->microsoftService->getManagedDevices($token);

        $device = collect($enrolDevices)->firstWhere('serialNumber', $product->numero_unique);

        if ($device) {
            $product->update(['enrollment_id' => $device['id']]);

            Historique::create([
                'title' => "Enrôlement d'équipement",
                'description' => "Enrôlement de l'équipement dans Intune",
                'equipement_id' => $validatedData['id']
            ]);

            if ($device['userPrincipalName'] && $user = User::Where('email', $device['userPrincipalName'])->first()) {
                $product->update(['user_attributed_id' => $user->id]);
                Historique::create([
                    'title' => "Attribution d'équipement",
                    'description' => "Attribution de l'équipement à " . $user->name,
                    'equipement_id' => $validatedData['id']
                ]);
            }

            return response()->json([
                'message' => 'L\'équipement a été trouvé dans Intune',
            ], 200);
        } else {
            return response()->json([
                'message' => 'L\'équipement n\'a pas été trouvé dans Intune',
            ], 400);
        }
    }

    public function actionEquipement(Request $request)
    {
        $validated_data = $request->validate([
            'id' => 'required',
            'action' => 'required'
        ]);

        $product = CommandeProduct::find($validated_data['id']);
        $token = $this->microsoftService->getAccessToken();

        switch ($validated_data['action']) {
            case 'reboot':
                $this->microsoftService->rebootDevice($token, $product->enrollment_id);
                break;
            case 'reset':
                $this->microsoftService->resetDevice($token, $product->enrollment_id);
                break;
            case 'block':
                $this->microsoftService->lockDevice($token, $product->enrollment_id);
                break;
            case 'sync':
                $this->microsoftService->syncDevice($token, $product->enrollment_id);
                break;
        }

        Historique::create([
            'title' => "Action sur l'équipement",
            'description' => $validated_data['action'] . " sur l'équipement",
            'equipement_id' => $validated_data['id']
        ]);
    }

    public function updateAttribution(Request $request) // HORRIBLE A REFAIRE
    {
        $validated_data = $request->validate([
            'product_id' => 'required',
            'user_id' => 'required',
            'action' => 'required'
        ]);

        $product = CommandeProduct::find($validated_data['product_id']);
        $user = User::find($validated_data['user_id']);

        try {
            if ($product->sous_categorie == 'licences') {
                if ($product->fournisseur == 'microsoft') {
                    if (!$user->tenant_user_id)
                        return response()->json([
                            'message' => 'L\'utilisateur n\'est pas synchronisé avec Microsoft Entra ID',
                        ], 400);

                    $token = $this->microsoftService->getAccessToken();

                    if ($validated_data['action'] === 'attribuer')
                        $this->microsoftService->assignLicence($token, $user->tenant_user_id, $product->guid);
                    elseif ($validated_data['action'] === 'retirer')
                        $this->microsoftService->unAssignLicence($token, $user->tenant_user_id, $product->guid);
                } else if ($product->fournisseur == 'google') {
                    if (!$user->tenant_user_id)
                        return response()->json([
                            'message' => 'L\'utilisateur n\'est pas synchronisé avec Google Workspace',
                        ], 400);

                    $token = $this->googleService->getAccessToken();
                    if ($validated_data['action'] === 'attribuer')
                        $this->googleService->assignLicence($token, $user->tenant_user_id, $product->guid, $product->reference_id);
                    elseif ($validated_data['action'] === 'retirer')
                        $this->googleService->unAssignLicence($token, $user->tenant_user_id, $product->guid, $product->reference_id);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de' . ($validated_data['action'] === 'attribuer' ? ' l\'attribution' : ' la désattribution') . ' de la licence',
                'error' => $th->getMessage()
            ], 400);
        }

        $product->update([
            'user_attributed_id' => $validated_data['action'] == 'attribuer' ? $user->id : null,
        ]);

        Historique::create([
            'title' => "Attribution d'équipement",
            'description' => $validated_data['action'] == 'attribuer' ? "Attribution de l'équipement à " . $user->name : "Retrait de l'équipement à " . $user->name,
            'equipement_id' => $validated_data['product_id']
        ]);

        return response()->json([
            'message' => $validated_data['action'] == 'attribuer' ? "L'équipement a bien été attribué à " . $user->name : "L'équipement a bien été retiré à " . $user->name,
        ], 200);
    }
}
