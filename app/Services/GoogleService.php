<?php

namespace App\Services;

use App\Models\CommandeProduct;
use League\OAuth2\Client\Provider\GenericProvider;
use App\Models\OauthToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class GoogleService
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId'                => config('api.GOOGLE_CLIENT_ID'),
            'clientSecret'            => config('api.GOOGLE_CLIENT_SECRET'),
            'redirectUri'             => env('GOOGLE_REDIRECT_URI'),
            'urlAuthorize'            => 'https://accounts.google.com/o/oauth2/auth',
            'urlAccessToken'          => 'https://oauth2.googleapis.com/token',
            'urlResourceOwnerDetails' => 'https://openidconnect.googleapis.com/v1/userinfo',
        ]);
    }

    public function getAuthorizationUrl()
    {
        $authorizationUrl = $this->provider->getAuthorizationUrl([
            'scope' => implode(' ', [
                'https://www.googleapis.com/auth/admin.directory.user',
                // 'https://www.googleapis.com/auth/userinfo.email',
                // 'https://www.googleapis.com/auth/userinfo.profile',
                // 'openid',
                'https://www.googleapis.com/auth/admin.directory.user.security',
                'https://www.googleapis.com/auth/apps.licensing',
                'https://www.googleapis.com/auth/admin.directory.customer',
            ]),
            'access_type' => 'offline',
            'approval_prompt' => 'force',
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
            ['service_name' => 'google', 'entreprise_id' => Auth::user()->entreprise_id],
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
        $token = OauthToken::where('service_name', 'google')
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->first();

        if ($token && $token->isAccessTokenExpired()) {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $token->refresh_token
            ]);

            $token->update([
                'access_token' => $newAccessToken->getToken(),
                // 'refresh_token' => $newAccessToken->getRefreshToken(),
                'access_token_expires_at' => Carbon::createFromTimestamp($newAccessToken->getExpires()),
                'refresh_token_expires_at' => null,
            ]);

            return $newAccessToken->getToken();
        }

        return $token->access_token;
    }

    public function getActualCustomerId($accessToken)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            'https://admin.googleapis.com/admin/directory/v1/customers/my_customer',
            $accessToken
        );

        $response = $this->provider->getResponse($request);
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['id'] ?? null;
    }

    public function createUser($accessToken, array $body)
    {
        $client = new Client();

        $response = $client->post(
            'https://admin.googleapis.com/admin/directory/v1/users',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $body,
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function deleteuser($accessToken, $userId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'DELETE',
            "https://admin.googleapis.com/admin/directory/v1/users/$userId",
            $accessToken
        );

        $response = $this->provider->getResponse($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getUsers($accessToken)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            'https://admin.googleapis.com/admin/directory/v1/users?customer=my_customer',
            $accessToken
        );

        $response = $this->provider->getResponse($request);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['users'] ?? [];
    }

    public function getPhotoUser($accessToken, $userId)
    {
        try {
            $request = $this->provider->getAuthenticatedRequest(
                'GET',
                "https://admin.googleapis.com/admin/directory/v1/users/$userId/photos/thumbnail",
                $accessToken
            );

            $response = $this->provider->getResponse($request);

            $imageContent = $response->getBody()->getContents();
            $data = json_decode($imageContent, true);
            $photoData = $data['photoData'];
            $base64 = strtr($photoData, '-_', '+/');
            $imageData = base64_decode($base64);

            $profileImgName = "users/{$userId}/profile_img_" . Str::random(6) . ".jpg";
            Storage::disk('s3')->put($profileImgName, $imageData, 'public');
            $profileImgUrl = Storage::disk('s3')->url($profileImgName);

            return $profileImgUrl;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getUserByEmail($accessToken, $email)
    {
        try {
            $request = $this->provider->getAuthenticatedRequest(
                'GET',
                "https://admin.googleapis.com/admin/directory/v1/users/$email",
                $accessToken
            );

            $response = $this->provider->getResponse($request);

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getUserApps($accessToken, $userId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            "https://admin.googleapis.com/admin/directory/v1/users/$userId/tokens",
            $accessToken
        );

        $response = $this->provider->getResponse($request);

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data['items'])) {
            $apps = [];

            foreach ($data['items'] as $item) {
                $apps[] = [
                    'title' => $item['displayText'],
                    'created_at' => '',
                ];
            }

            return $apps;
        }

        return [];
    }

    public function getLicenses($accessToken)
    {
        $customerId = $this->getActualCustomerId($accessToken);

        if (!$customerId) {
            $customerId = 'my_customer';
        }

        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            "https://licensing.googleapis.com/apps/licensing/v1/product/Google-Apps/users?customerId={$customerId}",
            $accessToken
        );

        $response = $this->provider->getResponse($request);
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['items'] ?? [];
    }

    public function assignLicence($accessToken, $userId, $skuId, $productId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            "https://licensing.googleapis.com/apps/licensing/v1/product/{$productId}/sku/{$skuId}/user?userId={$userId}",
            $accessToken
        );

        $response = $this->provider->getResponse($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function unAssignLicence($accessToken, $userId, $skuId, $productId)
    {
        $request = $this->provider->getAuthenticatedRequest(
            'DELETE',
            "https://licensing.googleapis.com/apps/licensing/v1/product/{$productId}/sku/{$skuId}/user/{$userId}",
            $accessToken
        );

        $response = $this->provider->getResponse($request);

        return json_decode($response->getBody()->getContents(), true);
    }

}
