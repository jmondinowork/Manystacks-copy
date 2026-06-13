<?php

namespace App\Console\Commands;

use App\Services\MicrosoftService;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CommandeProduct;

class UpdateMicrosoftUsers extends Command
{
    protected $signature = 'microsoft:users {userId}';
    protected $description = 'Update Microsoft users';
    protected $microsoftService;


    public function __construct(MicrosoftService $microsoftService)
    {
        parent::__construct();
        $this->microsoftService = $microsoftService;
    }

    public function handle()
    {
        $userId = $this->argument('userId');
        $userAuth = User::find($userId);

        $access_token = $this->microsoftService->getAccessToken();
        $users = $this->microsoftService->getUsers($access_token);
        $microsoftTenantUserIds = [];

        foreach ($users as $user) {
            $newUser = User::updateOrCreate(
                ['email' => $user['userPrincipalName']],
                [
                    'name' => $user['displayName'],
                    'poste' => $user['jobTitle'],
                    'type' => 'Personne',
                    'role' => $user['userPrincipalName'] === $userAuth->email ? $userAuth->role : 'collaborateur',
                    'tel' => $user['businessPhones'][0] ?? $user['mobilePhone'] ?? null,
                    'tenant_user_id' => $user['id'],
                    'tenant_name' => 'microsoft',
                    'entreprise_id' => $userAuth->entreprise_id,
                    'profile_img' => $this->microsoftService->getPhotoUser($access_token, $user['id'])
                ]
            );

            $microsoftTenantUserIds[] = $user['id'];

            if ($licences = $this->microsoftService->getLicencesUser($access_token, $newUser->tenant_user_id)) {
                foreach ($licences as $licence) {
                    $commandeProduct = CommandeProduct::where('entreprise_id', $userAuth->entreprise_id)
                        ->where('guid', $licence['skuId'])
                        ->whereNull('user_attributed_id')
                        ->where('status', 'active')
                        ->first();

                    if ($commandeProduct) {
                        $commandeProduct->update(['user_attributed_id' => $newUser->id]);
                    }
                }
            }
        }

        User::where('tenant_name', 'microsoft')
            ->where('entreprise_id', $userAuth->entreprise_id)
            ->whereNotIn('tenant_user_id', $microsoftTenantUserIds)
            ->update(['tenant_user_id' => null, 'tenant_name' => null]);
    }
}
