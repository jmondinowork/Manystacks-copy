<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CommandeProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\TenantController;

class CreateGoogleUserAfterLicenceIsActive extends Command
{
    protected $signature = 'google:createUserAfterLicenceIsActive  {userId}';
    protected $description = "Create Google user after licence is active";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $userAuth = User::with('entreprise')->find($userId);

        $users = User::where('tenant_name', 'google')->where('entreprise_id', $userAuth->entreprise_id)->get()->where('tenant_name', 'waiting');

        if ($users) {
            foreach ($users as $user) {
                $product = CommandeProduct::whereNull('user_attributed_id')
                    ->where('entreprise_id', $userAuth->entreprise_id)
                    ->where('status', 'active')
                    ->where('reference_id', 'like', '%GoogleWorkspace%')
                    ->first();

                if ($product) {

                    // ça ne fonctionne pas
                    $syncRequest = new Request([
                        'userid'       => $user->id,
                        'email'        => $user->email,
                        'email_perso'  => $user->email_perso,
                        'tenant'       => 'google',
                    ]);

                    $tenantController = app()->make(TenantController::class);
                    $tenantController->createTenantAccount($syncRequest);
                }
            }
        }
    }
}
