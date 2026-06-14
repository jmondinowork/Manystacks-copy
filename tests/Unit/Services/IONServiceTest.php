<?php

namespace Tests\Unit\Services;

use App\Models\OauthToken;
use App\Services\IONService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class IONServiceTest extends TestCase
{
    use RefreshDatabase;

    private function cachedToken(): void
    {
        OauthToken::create([
            'service_name' => 'ION',
            'access_token' => 'cached-token',
            'refresh_token' => 'refresh',
            'access_token_expires_at' => now()->addHour(),
        ]);
    }

    private function withMockedClient(IONService $service, $client): void
    {
        $property = new \ReflectionProperty($service, 'client');
        $property->setAccessible(true);
        $property->setValue($service, $client);
    }

    public function test_get_token_returns_cached_token_when_still_valid(): void
    {
        $this->cachedToken();

        $this->assertSame('cached-token', (new IONService())->getToken());
    }

    public function test_get_token_throws_when_no_refresh_token_is_available(): void
    {
        OauthToken::create([
            'service_name' => 'ION',
            'access_token' => 'expired',
            'refresh_token' => null,
            'access_token_expires_at' => now()->subHour(),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Refresh token is expired or not available.');

        (new IONService())->getToken();
    }

    public function test_request_returns_null_when_the_http_call_fails(): void
    {
        $this->cachedToken();

        $service = new IONService();
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')->andThrow(new \RuntimeException('boom'));
        $this->withMockedClient($service, $client);

        $this->assertNull($service->getOrders('customer-1'));
    }

    public function test_get_orders_returns_the_decoded_payload(): void
    {
        $this->cachedToken();

        $service = new IONService();
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->once()
            ->andReturn(new Response(200, [], json_encode(['orders' => [['id' => 1]]])));
        $this->withMockedClient($service, $client);

        $this->assertSame(['orders' => [['id' => 1]]], $service->getOrders('customer-1'));
    }

    public function test_get_client_id_matches_the_customer_by_siret(): void
    {
        $this->cachedToken();

        $service = new IONService();
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')->andReturn(new Response(200, [], json_encode([
            'customers' => [
                ['customerTitle' => '00000000000000', 'name' => 'accounts/1/customers/11'],
                ['customerTitle' => '12345678901234', 'name' => 'accounts/1/customers/42'],
            ],
        ])));
        $this->withMockedClient($service, $client);

        $this->assertSame('42', $service->getClientId('12345678901234'));
    }
}
