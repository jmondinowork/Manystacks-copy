<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IONService;
use App\Models\User;
use App\Models\Commande;
use App\Models\CommandeProduct;
use App\Models\Product;
use App\Models\IonLicence;
use Illuminate\Support\Str;

class GetNewLicenses extends Command
{
    protected $signature = 'licenses:getNew  {userId}';
    protected $description = "Récupère les nouvelles licences si jamais";

    protected $ionService;

    public function __construct(IONService $ionService)
    {
        parent::__construct();
        $this->ionService = $ionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $user = User::with('entreprise')->find($userId);
        $client_id = $user->entreprise->ion_id;

        if ($subscriptions = $this->ionService->getActiveSubscriptions($client_id)) {
            CommandeProduct::where('entreprise_id', $user->entreprise_id)
                ->where('categorie', 'licences')
                ->where('status', '!=', 'provisioning')
                ->delete();

            foreach ($subscriptions as $subscription) {
                for ($i = 0; $i < intval($subscription['subscriptionTotalLicenses']); $i++) {
                    $productRef = Product::with('images')->where('plan_id', $subscription['ccpPlanId'])->where('sku_id', $subscription['ccpSkuId'])->first() ?? IonLicence::where('plan_id', $subscription['ccpPlanId'])->where('sku_id', $subscription['ccpSkuId'])->first();

                    if ($productRef) {
                        CommandeProduct::create([
                            'commande_id' => null,
                            'entreprise_id' => $user->entreprise->id,
                            'quantity' => 1,
                            'sku_id' => $subscription['ccpSkuId'],
                            'plan_id' => $subscription['ccpPlanId'],
                            'reference_id' => $subscription['ccpProductId'],
                            'commande_status' => 'COMPLETED',
                            // 'licence_id' => $subscription['subscriptionId'],
                            // 'licence_item_id' => $subscription['subscriptionId'],
                            'licence_resource_id' => $subscription['subscriptionId'],
                            'status' => 'active',
                            'name' => $productRef->name,
                            'slug' => Str::slug($productRef->name),
                            'categorie' => 'licences',
                            'sous_categorie' => 'licences',
                            'ref_fournisseur' => $productRef->ref_fournisseur ?? null,
                            'image_principale' => $productRef->image_principale,
                            'prix' => $productRef->prix ?? $subscription['price'],
                            'type_licence' => $productRef->type_licence ?? $productRef->facturation_period,
                            'guid' => $productRef->guid,
                            'fournisseur' => $productRef->fournisseur,
                            'date_debut_licence' => $subscription['subscriptionStartDate'],
                            'date_fin_licence' => $subscription['subscriptionEndDate'],
                            'auto_renew' => $subscription['subscriptionRenewStatus'] === 'ENABLED' ? 1 : 0,
                        ]);
                    }
                }
            }
        }
    }
}
