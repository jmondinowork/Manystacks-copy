<?php

namespace App\Services;

use League\OAuth2\Client\Provider\GenericProvider;
use Carbon\Carbon;

use App\Models\OauthToken;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class SIRHService
{
    protected $provider;
    protected $serviceName;

    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;

        $this->provider = new GenericProvider([
            'clientId'                => config('api.' . strtoupper($serviceName) . "_API_KEY"),
            'clientSecret'            => config('api.' . strtoupper($serviceName) . "_API_SECRET"),
            'redirectUri'             => env(strtoupper($serviceName) . "_REDIRECT_URI"),
            'urlAuthorize'            => config('api.' . strtoupper($serviceName) . "_URL_AUTHORIZE"),
            'urlAccessToken'          => config('api.' . strtoupper($serviceName) . "_URL_TOKEN"),
            'urlResourceOwnerDetails' => config('api.' . strtoupper($serviceName) . "_URL_RESSOURCE"),
        ]);
    }

    public function getAuthorizationUrl()
    {
        $authorizationUrl = $this->provider->getAuthorizationUrl([
            'scope' => config('api.' . strtoupper($this->serviceName) . "_SCOPE"),
            'state' => bin2hex(random_bytes(16)),
        ]);

        session(['oauth2state' => $this->provider->getState()]);

        return $authorizationUrl;
    }

    public function initializeAccessToken($code)
    {
        $accessToken = $this->provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        $sirhCompanyId = $this->getCompanyId($accessToken->getToken());

        OauthToken::updateOrCreate(
            ['service_name' => $this->serviceName, 'entreprise_id' => Auth::user()->entreprise_id],
            [
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'access_token_expires_at' => $accessToken->getExpires(),
                'refresh_token_expires_at' => null,
                'company_id' => $sirhCompanyId,
                'type' => 'sirh',
            ]
        );

        return [
            'access_token' => $accessToken->getToken(),
            'company_id' => $sirhCompanyId,
        ];
    }

    public function getAccessToken($entrepriseId)
    {
        $token = OauthToken::where('service_name', $this->serviceName)
            ->where('entreprise_id', $entrepriseId)
            ->first();

        if ($token && $token->isAccessTokenExpired()) {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $token->refresh_token
            ]);

            $token->update([
                'access_token' => $newAccessToken->getToken(),
                'refresh_token' => $newAccessToken->getRefreshToken(),
                'access_token_expires_at' => Carbon::createFromTimestamp($newAccessToken->getExpires()),
            ]);

            return [
                'access_token' => $newAccessToken->getToken(),
                'company_id' => $token->company_id,
            ];
        }

        if ($token) {
            return [
                'access_token' => $token->access_token,
                'company_id' => $token->company_id,
            ];
        } else
            return null;
    }

    public function getCompanyId($accessToken)
    {
        $introspectionUrl = config('api.' . strtoupper($this->serviceName) . "_URL_COMPANY");

        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            $introspectionUrl,
            $accessToken,
            [
                'headers' => [
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode([
                    'token' => $accessToken,
                ])
            ]
        );

        $response = $this->provider->getResponse($request);
        $data = json_decode($response->getBody(), true);

        return $data['company_id'] ?? null;
    }

    public function getEmployees($accessToken, $companyId)
    {
        $employees = [];
        $nextPageToken = null;

        do {
            $url = str_replace('{companyId}', $companyId, config('api.' . strtoupper($this->serviceName) . "_URL_EMPLOYEES")) . '?maxResults=50';
            if ($nextPageToken) {
                $url .= '&nextPageToken=' . urlencode($nextPageToken);
            }

            // Make the authenticated request
            $request = $this->provider->getAuthenticatedRequest(
                'GET',
                $url,
                $accessToken
            );

            $response = $this->provider->getResponse($request);
            $data = json_decode($response->getBody(), true);

            // Append the current page's employees to the result array
            if (isset($data['collaborators'])) {
                $employees = array_merge($employees, $data['collaborators']);
            }

            // Get the nextPageToken from the response, if available
            $nextPageToken = $data['meta']['nextPageToken'] ?? null;
        } while ($nextPageToken); // Continue until no nextPageToken is present

        return $employees;
    }

    public function getEmployeesNoOauth($accessToken, $domain)
    {
        $client = new Client();
        $employees = [];
        $url = str_replace('{companyDomain}', $domain, config('api.' . strtoupper($this->serviceName) . "_URL_EMPLOYEES"));

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => "lucca application=$accessToken",
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'fields' => "id, displayName, firstName, lastName, mail, picture, dtContractStart, dtContractEnd, directLine",
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(), true);
                $employees = $data['data']['items'];
            }
        } catch (\Exception $e) {
            $employees = $e->getMessage();
            error_log('Erreur lors de la récupération des employés : ' . $e->getMessage());
        }

        return $employees;
    }
}
