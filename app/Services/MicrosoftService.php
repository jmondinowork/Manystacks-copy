<?php

namespace App\Services;

use App\Models\CommandeProduct;
use League\OAuth2\Client\Provider\GenericProvider;
use App\Models\OauthToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\ClientException;
use App\Exceptions\RecreateTenantAccessException;

class MicrosoftService
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId'                => config('api.MS_CLIENT_ID'),
            'clientSecret'            => config('api.MS_CLIENT_SECRET'),
            'redirectUri'             => env('MS_REDIRECT_URI'),
            'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
        ]);
    }

    private function handleResponse($request)
    {
        try {
            $response = $this->provider->getResponse($request);
        } catch (ClientException $e) {
            if ($e->getCode() === 403) {
                throw new RecreateTenantAccessException('microsoft');
            }
            throw $e;
        }

        return $response;
    }

    public function getAuthorizationUrl()
    {
        $authorizationUrl = $this->provider->getAuthorizationUrl([
            'scope' => 'openid profile User.Read Directory.Read.All Directory.ReadWrite.All Directory.Write.Restricted Group.ReadWrite.All offline_access Application.ReadWrite.All DeviceManagementApps.ReadWrite.All DeviceManagementConfiguration.ReadWrite.All DeviceManagementManagedDevices.ReadWrite.All DeviceManagementServiceConfig.ReadWrite.All Policy.ReadWrite.ApplicationConfiguration RoleManagement.ReadWrite.Directory User.DeleteRestore.All User.EnableDisableAccount.All User.ManageIdentities.All User.ReadWrite.All AuditLog.Read.All DeviceManagementManagedDevices.PrivilegedOperations.All UserAuthenticationMethod.Read.All',
            'prompt' => 'consent',
        ]);

        session(['oauth2state' => $this->provider->getState()]);

        return $authorizationUrl;
    }

    public function initializeAccessToken($code)
    {
        $accessToken = $this->provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        OauthToken::updateOrCreate(
            ['service_name' => 'microsoft', 'entreprise_id' => Auth::user()->entreprise_id],
            [
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'access_token_expires_at' => Carbon::createFromTimestamp($accessToken->getExpires()),
                'refresh_token_expires_at' => null,
                'type' => 'tenant',
            ]
        );

        return $accessToken->getToken();
    }

    public function getAccessToken()
    {
        $token = OauthToken::where('service_name', 'microsoft')
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->first();

        if ($token && $token->isAccessTokenExpired()) {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $token->refresh_token
            ]);

            $token->update([
                'access_token' => $newAccessToken->getToken(),
                'refresh_token' => $newAccessToken->getRefreshToken(),
                'access_token_expires_at' => Carbon::createFromTimestamp($newAccessToken->getExpires()),
                'refresh_token_expires_at' => null
            ]);

            return $newAccessToken->getToken();
        }

        return $token->access_token;
    }

    public function getTenantId($token)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            'https://graph.microsoft.com/v1.0/organization',
            $token
        );

        $response = $this->handleResponse($request);
        // Si la méthode handleResponse retourne une redirection, on la renvoie directement
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['value'][0]['id'] ?? null;
    }

    public function getManagedDevices($token)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            'https://graph.microsoft.com/v1.0/deviceManagement/managedDevices',
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['value'] ?? [];
    }

    public function getManagedDevice($token, $deviceId)
    {
        try {
            $request = $this->provider->getAuthenticatedRequest(
                'GET',
                "https://graph.microsoft.com/v1.0/deviceManagement/managedDevices/$deviceId",
                $token
            );

            $response = $this->handleResponse($request);
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            $data = json_decode($response->getBody()->getContents(), true);
            return $data;
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                // L'appareil n'existe plus
                CommandeProduct::where('enrollment_id', $deviceId)->update(['enrollment_id' => null]);
                return null;
            }
            throw $e;
        }
    }

    public function getUsers($token)
    {
        $url = 'https://graph.microsoft.com/v1.0/users?$select=businessPhones,displayName,givenName,jobTitle,mail,mobilePhone,officeLocation,preferredLanguage,surname,userPrincipalName,id,usageLocation';

        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            $url,
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['value'] ?? [];
    }

    public function getPhotoUser($token, $userId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            "https://graph.microsoft.com/v1.0/users/{$userId}/photo/\$value",
            $token
        );

        try {
            $response = $this->handleResponse($request);
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            // Si la réponse n’est pas 200, on considère qu’il n’y a pas de photo
            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $imageContent = $response->getBody()->getContents();
            $profileImgName = "users/{$userId}/profile_img_" . Str::random(6) . ".jpg";

            Storage::disk('s3')->put($profileImgName, $imageContent, 'public');
            $profileImgUrl = Storage::disk('s3')->url($profileImgName);
            return $profileImgUrl;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getLicencesUser($token, $userId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            "https://graph.microsoft.com/v1.0/users/{$userId}/licenseDetails",
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['value'] ?? [];
    }

    public function getUserByEmail($token, $email)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            'https://graph.microsoft.com/v1.0/users?$filter=userPrincipalName eq \'' . $email . '\'',
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['value'][0] ?? null;
    }

    public function createUser($token, $body)
    {
        $email = $body['userPrincipalName'];
        $existingUser = $this->getUserByEmail($token, $email);

        if ($existingUser) {
            return $existingUser;
        }

        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            'https://graph.microsoft.com/v1.0/users',
            $token,
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($body)
            ]
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function updateUser($token, $userId, $body)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'PATCH',
            "https://graph.microsoft.com/v1.0/users/{$userId}",
            $token,
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($body)
            ]
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function deleteUser($token, $userId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'DELETE',
            "https://graph.microsoft.com/v1.0/users/{$userId}",
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function assignLicence($token, $userId, $licenceId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            "https://graph.microsoft.com/v1.0/users/{$userId}/assignLicense",
            $token,
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'addLicenses' => [
                        [
                            'disabledPlans' => [],
                            'skuId' => $licenceId
                        ]
                    ],
                    'removeLicenses' => []
                ])
            ]
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function unAssignLicence($token, $userId, $licenceId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            "https://graph.microsoft.com/v1.0/users/{$userId}/assignLicense",
            $token,
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'addLicenses' => [],
                    'removeLicenses' => [$licenceId]
                ])
            ]
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function rebootDevice($token, $deviceId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            "https://graph.microsoft.com/v1.0/deviceManagement/managedDevices/$deviceId/rebootNow",
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function resetDevice($token, $deviceId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            "https://graph.microsoft.com/v1.0/deviceManagement/managedDevices/$deviceId/wipe",
            $token,
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'keepEnrollmentData' => true,
                    'keepUserData' => false,
                    'macOsUnlockCode' => null,
                    'windowsUnlockCode' => null
                ])
            ]
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function lockDevice($token, $deviceId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            "https://graph.microsoft.com/v1.0/deviceManagement/managedDevices/$deviceId/remoteLock",
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function syncDevice($token, $deviceId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            "https://graph.microsoft.com/v1.0/deviceManagement/managedDevices/$deviceId/syncDevice",
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function getAppRoleAssignments($token, $userId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            "https://graph.microsoft.com/v1.0/users/$userId/appRoleAssignments",
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (! empty($data['value'])) {
            $apps = [];
            foreach ($data['value'] as $value) {
                $apps[] = [
                    'title' => $value['resourceDisplayName'],
                    'created_at' => $value['createdDateTime']
                ];
            }
            return $apps;
        }

        return [];
    }

    public function listManagedApps($token)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            'https://graph.microsoft.com/v1.0/deviceAppManagement/mobileApps',
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        $apps = $data['value'] ?? [];

        // Filtrer les apps en "global" si nécessaire
        $apps = array_filter($apps, function ($app) {
            return !isset($app['appAvailability']) || $app['appAvailability'] !== 'global';
        });

        return $apps;
    }

    public function listApplications($token)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            'https://graph.microsoft.com/v1.0/applications',
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        $apps = $data['value'] ?? [];

        return $apps;
    }

    public function updateUsageLocation($token, $userId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'PATCH',
            "https://graph.microsoft.com/v1.0/users/$userId",
            $token,
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'usageLocation' => 'FR'
                ])
            ]
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    public function getAutheticationMethods($token, $userId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            "https://graph.microsoft.com/v1.0/users/$userId/authentication/methods",
            $token
        );

        $response = $this->handleResponse($request);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        $methods = $data['value'] ?? [];

        $hasMfa = false;
        $mfaMethods = [];

        foreach ($methods as $method) {
            $odataType = $method['@odata.type'] ?? null;

            switch ($odataType) {
                case '#microsoft.graph.phoneAuthenticationMethod':
                    // Phone-based MFA
                    $hasMfa = true;
                    $mfaMethods[] = [
                        'type'           => 'Téléphone',
                        'displayName'    => $method['phoneNumber']      ?? null,
                    ];
                    break;

                case '#microsoft.graph.microsoftAuthenticatorAuthenticationMethod':
                    // Microsoft Authenticator app
                    $hasMfa = true;
                    $mfaMethods[] = [
                        'type'             => 'Microsoft Authenticator',
                        'displayName'      => $method['displayName']     ?? null,
                    ];
                    break;

                case '#microsoft.graph.windowsHelloForBusinessAuthenticationMethod':
                    // Windows Hello for Business (often counted as MFA)
                    $hasMfa = true;
                    $mfaMethods[] = [
                        'type'            => 'Windows Hello',
                        'displayName'     => $method['displayName']     ?? null,
                    ];
                    break;

                case '#microsoft.graph.fido2AuthenticationMethod':
                    // FIDO2 security keys
                    $hasMfa = true;
                    $mfaMethods[] = [
                        'type'            => 'Fido2',
                        'displayName'     => $method['displayName']     ?? null,
                    ];
                    break;

                case '#microsoft.graph.softwareOathAuthenticationMethod':
                    // Software OATH TOTP
                    $hasMfa = true;
                    $mfaMethods[] = [
                        'type'            => 'Software Oath',
                        'displayName'     => $method['displayName']     ?? null,
                    ];
                    break;

                    // case '#microsoft.graph.temporaryAccessPassAuthenticationMethod':
                    //     // Temporary Access Pass (time-limited strong credential)
                    //     $hasMfa = true;
                    //     $mfaMethods[] = [
                    //         'type'            => 'temporary_access_pass',
                    //         'createdDateTime' => $method['createdDateTime'] ?? null,
                    //         'startDateTime'   => $method['startDateTime']   ?? null,
                    //         'endDateTime'     => $method['endDateTime']     ?? null,
                    //         'isUsable'        => $method['isUsable']        ?? null,
                    //     ];
                    //     break;

                case '#microsoft.graph.x509CertificateAuthenticationMethod':
                    // X.509 (cert-based) – typically a strong auth factor
                    $hasMfa = true;
                    $mfaMethods[] = [
                        'type'            => 'x509 Certificate',
                        'displayName'     => $method['displayName']     ?? null,
                    ];
                    break;
            }
        }

        return [
            'hasMfa' => $hasMfa,
            'mfaMethods' => $mfaMethods,
        ];
    }
}
