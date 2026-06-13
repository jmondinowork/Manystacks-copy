<?php

namespace App\Console\Commands;

use App\Services\GoogleService;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CommandeProduct;

class UpdateGoogleUsers extends Command
{
    protected $signature = 'google:update {userId}';
    protected $description = 'Update Google users';
    protected $googleService;


    public function __construct(GoogleService $googleService)
    {
        parent::__construct();
        $this->googleService = $googleService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $userAuth = User::find($userId);

        $access_token = $this->googleService->getAccessToken();
        $users = $this->googleService->getUsers($access_token);
        $googleTenantUserIds = [];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['primaryEmail']],
                [
                    'tenant_user_id' => $user['id'],
                    'tenant_name' => 'google',
                    'name' => $user['name']['fullName'],
                    'type' => 'Personne',
                    'role' => $user['primaryEmail'] === $userAuth->email ? 'admin' : 'collaborateur',
                    'tel' => $user['phones'][0]['value'] ?? null,
                    'entreprise_id' =>  $userAuth->entreprise_id,
                ]
            );

            $googleTenantUserIds[] = $user['id'];
        }

        User::where('tenant_name', 'google')
            ->where('entreprise_id', $userAuth->entreprise_id)
            ->whereNotIn('tenant_user_id', $googleTenantUserIds)
            ->update(['tenant_user_id' => null, 'tenant_name' => null]);

        $licences = $this->googleService->getLicenses($access_token);

        foreach ($licences as $licence) {
            $commandeProduct = CommandeProduct::where('entreprise_id', $userAuth->entreprise_id)
                ->where('guid', $licence['skuId'])
                ->whereNull('user_attributed_id')
                ->where('status', 'active')
                ->first();

            $user = User::where('email', $licence['userId'])->first();

            if ($commandeProduct) {
                CommandeProduct::find($commandeProduct->id)->update([
                    'user_attributed_id' => $user->id,
                ]);
            }
        }
    }
}
