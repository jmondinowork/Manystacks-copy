<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Services\EmailService;
use App\Services\IONService;
use App\Services\MicrosoftService;
use App\Services\SIRHService;
use App\Models\EntrepriseInformation;
use App\Models\AdresseLivraison;
use GuzzleHttp\Client;

use App\Models\Commande;
use App\Services\GoogleService;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $emailService;
    protected $ionService;
    protected $microsoftService;
    protected $googleService;
    protected $SIRHService;

    public function __construct(EmailService $emailService, IONService $ionService, MicrosoftService $microsoftService, GoogleService $googleService)
    {
        $this->emailService = $emailService;
        $this->ionService = $ionService;
        $this->microsoftService = $microsoftService;
        $this->googleService = $googleService;
    }

    public function initializeSIRHService($name)
    {
        $this->SIRHService = new SIRHService($name);
    }

    public static function generateUniqueReference()
    {
        $date = now()->format('Ymd');
        $lastId = Commande::max('id') + 1;
        $sequentialNumber = str_pad($lastId, 3, '0', STR_PAD_LEFT);

        return $date . '-' . $sequentialNumber;
    }

    public function getCompanyInfo(string $siret)
    {
        $client = new Client(['base_uri' => 'https://api.insee.fr', 'timeout' => 2.0]);
        $consumerKey = config('api.SIRENE_CONSUMER_KEY');
        $consumerSecret = config('api.SIRENE_CONSUMER_SECRET');

        try {
            // Obtenir le token d'accès
            $response = $client->post('/token', [
                'verify' => false,
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$consumerKey}:{$consumerSecret}"),
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            $accessToken = $body['access_token'] ?? null;

            if (!$accessToken) {
                null;
            }

            // Obtenir la version de l'API
            $response = $client->get('https://api.insee.fr/entreprises/sirene/V3.11/informations', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]);

            $info = json_decode((string) $response->getBody(), true);
            $versionService = 'V' . substr($info['versionService'], 0, 4);


            $response = $client->get("https://api.insee.fr/entreprises/sirene/{$versionService}/siret/{$siret}", [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]);

            $companyInfo = json_decode((string) $response->getBody(), true);
            return $companyInfo;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function createEntreprise($siret)
    {
        if ($companyInfo = $this->getCompanyInfo($siret)) {
            $entreprise = EntrepriseInformation::create([
                'siret' => $siret,
                'auto_entreprise' => $companyInfo['etablissement']['uniteLegale']['categorieJuridiqueUniteLegale'] === '1000',
                'raison_sociale' => $companyInfo['etablissement']['uniteLegale']['denominationUniteLegale'],
                'adresse' => $companyInfo['etablissement']['adresseEtablissement']['numeroVoieEtablissement'] . ' ' . $companyInfo['etablissement']['adresseEtablissement']['typeVoieEtablissement'] . ' ' . $companyInfo['etablissement']['adresseEtablissement']['libelleVoieEtablissement'],
                'complement_adresse' => $companyInfo['etablissement']['adresseEtablissement']['complementAdresseEtablissement'],
                'code_postal' => $companyInfo['etablissement']['adresseEtablissement']['codePostalEtablissement'],
                'ville' => $companyInfo['etablissement']['adresseEtablissement']['libelleCommuneEtablissement'],
                'profile_img' => '/images/entreprise-icon.png',
                'pays' => 'France',
            ]);

            AdresseLivraison::create([
                'entreprise_id' => $entreprise->id,
                'primary' => 1,
                'default' => 1,
                'titre' => 'Siège social',
                'adresse' => isset($companyInfo['etablissement']['adresseEtablissement']) ? $companyInfo['etablissement']['adresseEtablissement']['numeroVoieEtablissement'] . ' ' . $companyInfo['etablissement']['adresseEtablissement']['typeVoieEtablissement'] . ' ' . $companyInfo['etablissement']['adresseEtablissement']['libelleVoieEtablissement'] : null,
                'complement_adresse' => $companyInfo['etablissement']['adresseEtablissement']['complementAdresseEtablissement'] ?? null,
                'code_postal' => $companyInfo['etablissement']['adresseEtablissement']['codePostalEtablissement'] ?? null,
                'ville' => $companyInfo['etablissement']['adresseEtablissement']['libelleCommuneEtablissement'] ?? null,
                'pays' => 'France'
            ]);
        } else {
            $entreprise = null;
        }

        return $entreprise;
    }
}
