<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use App\Models\CommandeProduct;
use App\Models\OauthToken;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Services\MicrosoftService;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleService;
use Illuminate\Support\Facades\Crypt;
use App\Services\IonService;
use App\Models\Product;
use App\Models\IonLicence;
use League\Csv\Reader;
use Illuminate\Support\Facades\Hash;



class TestController extends Controller
{
    public function index(Request $request, TenantsController $tenantsController)
    {
    }

    public function getIonLicenses()
    {
        $ion_licences = $this->ionService->getLicences('Microsoft');
        $ion_licences = $ion_licences['products'];

        $callback = function () use ($ion_licences) {
            // Ouvrir le flux en écriture
            $handle = fopen('php://output', 'w');

            // Écriture de l'en-tête du CSV
            fputcsv($handle, ['Nom', 'Description Marketing', 'Image', 'Fournisseur', 'Product ID', 'SKU ID', 'Plan ID', 'GUID', 'Facturation']);

            // Parcours des produits et écriture des licences manquantes
            foreach ($ion_licences as $product) {
                $productId = basename($product['name']);
                $productName = $product['marketing']['displayName'];

                foreach ($product['definition']['skus'] as $sku) {
                    $skuId = $sku['id'];

                    foreach ($sku['plans'] as $plan) {
                        $planId = $plan['id'];
                        $facturation = $plan['billingPeriod'];

                        // Vérifier si la licence n'existe pas en base
                        if (IonLicence::where('plan_id', $planId)->doesntExist()) {
                            // Écriture d'une ligne dans le CSV
                            fputcsv($handle, [$productName, '', '', 'microsoft', $productId, $skuId, $planId, '', $facturation]);
                        }
                    }
                }
            }

            // Fermeture du flux
            fclose($handle);
        };

        // Retourne une réponse qui permet de télécharger le CSV
        return response()->streamDownload($callback, 'missing_licenses.csv', [
            'Content-Type' => 'text/csv'
        ]);
    }
}
