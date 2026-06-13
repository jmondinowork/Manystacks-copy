<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MicrosoftService;
use App\Models\User;
use App\Models\CommandeProduct;

class UpdateDevicesEnrollmentMicrosoft extends Command
{
    protected $signature = 'microsoft:devices {userId}';
    protected $description = 'Update Microsoft devices enrollment';
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

        $devices = $this->microsoftService->getManagedDevices($access_token);

        foreach ($devices as $device) {
            $categorie = $device['operatingSystem'] === 'Windows' || $device['operatingSystem'] === 'macOS' ? 'ordinateurs' : 'telephones';

            $data = [
                'entreprise_id' => $userAuth->entreprise_id,
                'systeme_exploitation' => $device['operatingSystem'],
                'categorie' => $categorie,
                'sous_categorie' => $categorie,
                'status' => 'En service',
                'marque' => $device['manufacturer'],
                'modele' => $device['model'],
                'enrollment_id' => $device['id'],
                'image_principale' => $categorie === 'ordinateurs' ? '/images/created_ordinateurs_icon.svg' : '/images/created_telephones_icon.svg',
                'user_attributed_id' => User::where('email', $device['userPrincipalName'])->first()->id ?? null,
            ];

            CommandeProduct::updateOrCreate(
                ['numero_unique' => $device['serialNumber']],
                $data
            );
        }
    }
}
