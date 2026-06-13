<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CommandeProduct;
use App\Models\EntrepriseDomain;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantsController extends Controller
{
    public function redirect(Request $request, $tenant)
    {
        session(['redirect_to' => $request->input('redirect_to') ?? url()->previous()]);

        switch ($tenant) {
            case 'microsoft':
                return redirect($this->microsoftService->getAuthorizationUrl());

            case 'google':
                return redirect($this->googleService->getAuthorizationUrl());
        }
    }

    public function callback(Request $request, $tenant)
    {
        $redirectTo = session('redirect_to');
        session()->forget('redirect_to');

        // Reset all attributions on licences
        CommandeProduct::where('entreprise_id', Auth::user()->entreprise_id)
            ->where('categorie', 'licences')
            ->where('fournisseur', $tenant)
            ->update(['user_attributed_id' => null]);

        try {
            switch ($tenant) {
                case 'microsoft':
                    $access_token = $this->microsoftService->initializeAccessToken($request->input('code'));
                    $users = $this->microsoftService->getUsers($access_token);
                    $devices = $this->microsoftService->getManagedDevices($access_token);
                    $this->createDevices($devices);
                    break;

                case 'google':
                    $access_token = $this->googleService->initializeAccessToken($request->input('code'));
                    $users = $this->googleService->getUsers($access_token);
                    break;
            }

            foreach ($users as $user) {
                $newUser = $this->initUsers($user, $tenant, $access_token);

                EntrepriseDomain::firstOrCreate([
                    'domain' => Str::after($newUser->email, '@'),
                    'tenant' => $tenant,
                    'entreprise_id' => Auth::user()->entreprise_id
                ]);

                // Attribution des équipements en attente
                if ($equipements = CommandeProduct::where('attribution_waiting', $newUser->email)->where('entreprise_id', $newUser->entreprise_id)->get()) {
                    foreach ($equipements as $equipement) {
                        $equipement->update(['user_attributed_id' => $newUser->id, 'attribution_waiting' => null]);
                    }
                }

                if ($tenant === 'microsoft') {
                    $this->updateLicencesmicrosoft($newUser, $access_token);
                }
            }

            if ($tenant === 'google') {
                $licences = $this->googleService->getLicenses($access_token);
                $this->updateLicencesGoogle($licences);
            }

            return redirect()->to($redirectTo)->with('success', 'L\'intégration a réussi, vos collaborateurs ont été ajoutés avec succès');
        } catch (\Throwable $th) {
            return redirect()->to($redirectTo)->with('error', 'L\'intégration a échoué, veuillez bien vérifier les informations saisies');
        }
    }

    public function recreateTenantAccess(Request $request, $tenant)
    {
        $integrations = config('integration.tenant');

        return Inertia::render('RecreateTenantAccess', [
            'tenant' => $tenant,
            'redirect_to' => url()->previous(),
            'integrations' => $integrations
        ]);
    }

    private function initUsers($user, $tenant, $access_token)
    {
        $entrepriseId = Auth::user()->entreprise_id;

        $basePayload = [
            'entreprise_id' => $entrepriseId,
            'tenant_name' => $tenant,
            'type' => 'Personne',
        ];

        switch ($tenant) {
            case 'microsoft':
                $email = $user['userPrincipalName'];

                $basePayload += [
                    'name' => $user['displayName'],
                    'role' => $email === Auth::user()->email ? 'admin' : 'collaborateur',
                    'tel' => $user['businessPhones'][0] ?? $user['mobilePhone'] ?? null,
                    'poste' => $user['jobTitle'],
                    'tenant_user_id' => $user['id'],
                    'profile_img' => $this->microsoftService->getPhotoUser($access_token, $user['id'])
                ];
                break;

            case 'google':
                $email = $user['primaryEmail'];

                $basePayload += [
                    'name' => $user['name']['fullName'],
                    'role' => $email === Auth::user()->email ? 'admin' : 'collaborateur',
                    'tel' => $user['phones'][0]['value'] ?? null,
                    'tenant_user_id' => $user['id'],
                    'profile_img' => $this->googleService->getPhotoUser($access_token, $user['id'])
                ];
                break;
        }

        return User::updateOrCreate(
            ['email' => $email],
            $basePayload
        );
    }

    private function createDevices($devices)
    {
        foreach ($devices as $device) {
            $categorie = $device['operatingSystem'] === 'Windows' || $device['operatingSystem'] === 'Mac' ? 'ordinateurs' : 'telephones';

            $data = [
                'entreprise_id' => Auth::user()->entreprise_id,
                'name' => $device['model'],
                'systeme_exploitation' => $device['operatingSystem'],
                'categorie' => $categorie,
                'sous_categorie' => $categorie,
                'status' => 'En service',
                'marque' => $device['manufacturer'],
                'modele' => $device['model'],
                'image_principale' => $categorie === 'ordinateurs' ? '/images/created_ordinateurs_icon.svg' : '/images/created_telephones_icon.svg',
                'numero_unique' => $device['serialNumber'],
                'user_attributed_id' => User::where('email', $device['userPrincipalName'])->first()->id ?? null,
            ];

            CommandeProduct::updateOrCreate(
                ['enrollment_id' => $device['id']],
                $data
            );
        }
    }

    private function updateLicencesGoogle($licences)
    {
        foreach ($licences as $licence) {
            $commandeProduct = CommandeProduct::where('entreprise_id', Auth::user()->entreprise_id)
                ->where('guid', $licence['skuId'])
                ->whereNull('user_attributed_id')
                ->where('status', 'active')
                ->first();

            $user  = User::where('email', $licence['userId'])->first();

            if ($commandeProduct) {
                CommandeProduct::find($commandeProduct->id)->update([
                    'user_attributed_id' => $user->id,
                ]);
            }
        }
    }

    private function updateLicencesMicrosoft($newUser, $access_token)
    {
        if ($licences = $this->microsoftService->getLicencesUser($access_token, $newUser->tenant_user_id)) {
            foreach ($licences as $licence) {
                $commandeProduct = CommandeProduct::where('entreprise_id', Auth::user()->entreprise_id)
                    ->where('guid', $licence['skuId'])
                    ->whereNull('user_attributed_id')
                    ->where('status', 'active')
                    ->first();

                if ($commandeProduct) {
                    CommandeProduct::find($commandeProduct->id)->update([
                        'user_attributed_id' => $newUser->id,
                    ]);
                }
            }
        }
    }

    public function checkUserEmailTenant(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'tenant' => 'required|string'
        ]);

        switch ($validated['tenant']) {
            case 'microsoft':
                $token = $this->microsoftService->getAccessToken();
                $exists = $this->microsoftService->getUserByEmail($token, $validated['email']) ? true : false;
                break;

            default:
                $token = $this->googleService->getAccessToken();
                $exists = $this->googleService->getUserByEmail($token, $validated['email']) ? true : false;
                break;
        }

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function createTenantAccount(Request $request)
    {
        $validated = $request->validate([
            'userid' => 'required',
            'email' => 'required',
            'email_perso' => 'nullable',
            'tenant' => 'required|string'
        ]);

        $user = User::with(['commandeProducts', 'tags'])->find($validated['userid']);
        $password = Str::random(8) . Str::upper(Str::random(1)) . Str::lower(Str::random(1)) . collect(str_split('0123456789'))->random(1)[0] . collect(str_split('!@#$%^&*'))->random(1)[0];

        switch ($validated['tenant']) {
            case 'microsoft':
                $token = $this->microsoftService->getAccessToken();
                if ($this->microsoftService->getUserByEmail($token, $validated['email'])) {
                    return response()->json([
                        'error' => "L'adresse email est déjà utilisée, veuillez choisir un autre pattern.",
                    ]);
                }

                $body = [
                    'accountEnabled' => true,
                    'displayName' => $user->name,
                    'mailNickname' => Str::slug($user->name),
                    'userPrincipalName' => $validated['email'],
                    'mail' => $validated['email'],
                    'jobTitle' => $user->poste,
                    'businessPhones' => $user->tel ? [$user->tel] : [],
                    'passwordProfile' => [
                        'forceChangePasswordNextSignIn' => true,
                        'password' => $password,
                    ],
                    'usageLocation' => 'FR',
                    'passwordPolicies' => 'DisablePasswordExpiration',
                ];
                $data = $this->microsoftService->createUser($token, $body);

                $templateId = 16;
                break;
            case 'google':
                $token = $this->googleService->getAccessToken();
                if ($this->googleService->getUserByEmail($token, $validated['email'])) {
                    return response()->json([
                        'error' => "L'adresse email est déjà utilisée, veuillez choisir un autre pattern.",
                    ]);
                }

                $body = [
                    "primaryEmail" => $validated['email'],
                    "name" => [
                        "givenName" => explode(' ', $user->name)[0],
                        "familyName" => explode(' ', $user->name)[1]
                    ],
                    "password" => $password,
                    "changePasswordAtNextLogin" => true,
                ];
                $data = $this->googleService->createUser($token, $body);

                $templateId = 17;

                CommandeProduct::where('entreprise_id', $user->entreprise_id)
                    ->where('reference_id', 'like', '%GoogleWorkspace%')
                    ->where('status', 'active')
                    ->update(['user_attributed_id' => $user->id]);
                break;
        }

        $user->update([
            'tenant_name' => $validated['tenant'],
            'tenant_user_id' => $data['id'],
            'email' => $validated['email'],
            'email_perso' => $validated['email_perso'],
        ]);

        $this->emailService->sendEmail([
            'templateId' => $templateId,
            'to' => [['email' => $validated['email_perso']]],
            'params' => [
                'name' => $user->name,
                'email' => $validated['email'],
                'password' => $password,
                'destinataire' => $validated['email_perso']
            ]
        ]);

        return response()->json([
            'user' => $user,
        ]);
    }
}
