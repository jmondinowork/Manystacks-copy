<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\OauthToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IONService
{
    protected $client;
    protected $accountId;
    protected $serviceName;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://ion.tdsynnex.com/']);
        $this->accountId = config('api.ION_ACCOUNT_ID');
        $this->serviceName = 'ION';
    }

    public function getToken()
    {
        $tokenRecord = OauthToken::where('service_name', $this->serviceName)->first();

        if ($tokenRecord && $tokenRecord->access_token && Carbon::now()->lt(Carbon::parse($tokenRecord->access_token_expires_at))) {
            return $tokenRecord->access_token;
        }

        $refreshToken = $tokenRecord->refresh_token;


        if (!$refreshToken) {
            throw new \Exception('Refresh token is expired or not available.');
        }

        $response = $this->client->post('oauth/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        OauthToken::updateOrCreate(
            ['service_name' => $this->serviceName],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'access_token_expires_at' => Carbon::now()->addSeconds($data['expires_in']),
                'refresh_token_expires_at' => Carbon::now()->addDays(32),
                'updated_at' => now(),
            ]
        );

        return $data['access_token'];
    }

    public function request($method, $uri, $options = [])
    {
        $token = $this->getToken();
        $options['headers']['Authorization'] = 'Bearer ' . $token;

        try {
            $response = $this->client->request($method, $uri, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function createOrder($customerId, $body)
    {
        $options = [
            'json' => $body
        ];

        return $this->request('POST', "api/v3/accounts/{$this->accountId}/customers/{$customerId}/orders", $options);
    }

    public function getClientId($siret)
    {
        $options = [
            'query' => [
                'page_size' => 1000,
            ],
        ];

        $customers = $this->request('GET', "api/v3/accounts/{$this->accountId}/customers", $options);

        foreach ($customers['customers'] as $customer) {
            if ($customer['customerTitle'] === $siret) {
                return basename($customer['name']);
            }
        }
    }

    public function getOrder($customerId, $orderId)
    {
        return $this->request('GET', "api/v3/accounts/{$this->accountId}/customers/{$customerId}/orders/{$orderId}");
    }

    public function getOrders($customerId)
    {
        $options = [
            'query' => [
                'page_size' => 1000,
            ],
        ];
        return $this->request('GET', "api/v3/accounts/{$this->accountId}/customers/{$customerId}/orders", $options);
    }

    public function getActiveSubscriptions($customerId)
    {
        $options = [
            'query' => [
                'customerId' => $customerId,
                'subscriptionStatus' => 'active',
            ],
        ];

        $subscriptions = $this->request('GET', "api/v3/accounts/{$this->accountId}/subscriptions", $options);

        return $subscriptions['items'] ?? [];
    }

    public function getSubscriptions($customerId)
    {
        $options = [
            'query' => [
                'customerId' => $customerId,
            ],
        ];

        return $this->request('GET', "api/v3/accounts/{$this->accountId}/subscriptions", $options);
    }

    public function getLicences($filter)
    {
        $options = [
            'query' => [
                'filter.name' => $filter,
                'page_size' => 10000,
            ],
        ];

        return $this->request('GET', "api/v3/accounts/{$this->accountId}/products", $options);
    }

    // public function cancelOrder($customerId, $orderItem)
    // {
    //     $options = [
    //         'json' => [
    //             "orderItems" => [
    //                 [
    //                     "productId" => $orderItem->reference_id,
    //                     "skuId" => $orderItem->sku_id,
    //                     "planId" => $orderItem->plan_id,
    //                     "action" => "UPDATE",
    //                     "quantity" => $orderItem->quantity,
    //                     "resourceId" => $orderItem->licence_resource_id,
    //                     "attributes" => [
    //                         [
    //                             "name" => "operations",
    //                             "value" => "updateSubscription"
    //                         ],
    //                         [
    //                             "name" => "renewalSetting",
    //                             "value" => "auto-off"
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ];

    //     return $this->request('POST', "api/v3/accounts/{$this->accountId}/customers/{$customerId}/orders", $options);
    // }
}
