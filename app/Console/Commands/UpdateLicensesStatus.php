<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CommandeProduct;
use App\Models\User;
use App\Services\IONService;
use App\Services\MicrosoftService;
use App\Services\GoogleService;

class UpdateLicensesStatus extends Command
{
    protected $signature = 'licenses:update  {userId}';
    protected $description = 'Met à jour le statut des licences';

    protected $ionService;

    public function __construct(IONService $ionService)
    {
        parent::__construct();
        $this->ionService = $ionService;
    }

    /**
     * Execute the console command.
     */
    public function handle(MicrosoftService $microsoftService)
    {
        $userId = $this->argument('userId');
        $user = User::with('entreprise')->find($userId);

        $licences = CommandeProduct::where('categorie', 'licences')
            ->where('entreprise_id', 1)
            ->where(function ($query) {
                $query->where('date_fin_licence', '<', now())
                    ->where('auto_renew', false);
            })
            ->get();

        if (!$licences->isEmpty()) {
            $subscriptionsById = [];
            if ($subscriptions = $this->ionService->getSubscriptions($user->entreprise->ion_id)) {
                $subscriptionsById = array_combine(
                    array_column($subscriptions['items'], 'subscriptionId'),
                    $subscriptions['items']
                );
            }

            foreach ($licences as $licence) {
                if ($licence->status !== 'cancelled' && $licence->fournisseur === 'microsoft') {
                    $oldStatus = $licence->status;
                    $licence->status = $subscriptionsById[$licence->licence_resource_id]['subscriptionStatus'] ?? "";

                    if ($licence->status !== 'active')
                        $licence->user_attributed_id = null;

                    if ($licence->status === 'active' && $oldStatus !== 'active' && $licence->user_attributed_id) {
                        $token = $microsoftService->getAccessToken();
                        $userAttributed = User::find($licence->user_attributed_id);
                        $microsoftService->assignLicence($token, $userAttributed->tenant_user_id, $licence->guid);
                    }
                    $licence->save();
                }
            }
        }
    }
}
