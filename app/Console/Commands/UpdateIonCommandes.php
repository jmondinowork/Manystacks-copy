<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Commande;
use App\Models\User;
use App\Services\IONService;

class UpdateIonCommandes extends Command
{
    protected $signature = 'ionCommandes:update {userId}';
    protected $description = 'Met à jour le statut des commandes passées sur ION';

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

        $commandes = Commande::with(['commandeProducts'])
            ->where('entreprise_id', $user->entreprise_id)
            ->whereNotNull('commande_ion_id')
            ->where(function ($query) {
                $query->where('statut', '!=', 'Terminée')
                    ->orWhere('statut', '!=', 'Erreur');
            })
            ->get();

        if (!$commandes->isEmpty()) {
            foreach ($commandes as $commande) {
                $ion_order = $this->ionService->getOrder($user->entreprise->ion_id, $commande->commande_ion_id);

                if ($ion_order) {
                    switch ($ion_order['status']) {
                        case 'COMPLETED':
                            $commande->update(['statut' => 'Terminée']);
                            $commande->commandeProducts()->where('fournisseur', 'google')->update(['commande_status' => 'COMPLETED', 'status' => 'active']);
                            break;
                        case 'ERROR':
                            $commande->update(['statut' => 'Erreur']);
                            $commande->commandeProducts()->update(['commande_status' => 'ERROR', 'status' => 'error']);
                            break;
                    }
                }
            }
        }
    }
}
