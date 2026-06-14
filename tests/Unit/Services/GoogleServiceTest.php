<?php

namespace Tests\Unit\Services;

use App\Models\EntrepriseInformation;
use App\Models\OauthToken;
use App\Models\User;
use App\Services\GoogleService;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use League\OAuth2\Client\Provider\GenericProvider;
use Mockery;
use Tests\TestCase;

class GoogleServiceTest extends TestCase
{
    use RefreshDatabase;

    private function withMockedProvider(GoogleService $service, $provider): void
    {
        $property = new \ReflectionProperty($service, 'provider');
        $property->setAccessible(true);
        $property->setValue($service, $provider);
    }

    public function test_authorization_url_targets_google_with_state(): void
    {
        config(['api.GOOGLE_CLIENT_ID' => 'test-client-id']);

        $url = (new GoogleService())->getAuthorizationUrl();

        $this->assertStringContainsString('accounts.google.com', $url);
        $this->assertStringContainsString('test-client-id', $url);
        $this->assertStringContainsString('state=', $url);
    }

    public function test_get_access_token_returns_the_stored_token_when_valid(): void
    {
        $entreprise = EntrepriseInformation::create(['raison_sociale' => 'Acme', 'siret' => '11122233344455']);
        $user = User::factory()->create(['entreprise_id' => $entreprise->id]);

        OauthToken::create([
            'service_name' => 'google',
            'entreprise_id' => $entreprise->id,
            'access_token' => 'stored-google-token',
            'access_token_expires_at' => now()->addHour(),
        ]);

        $this->actingAs($user);

        $this->assertSame('stored-google-token', (new GoogleService())->getAccessToken());
    }

    public function test_get_users_returns_the_directory_users(): void
    {
        $service = new GoogleService();
        $provider = Mockery::mock(GenericProvider::class);
        $provider->shouldReceive('getAuthenticatedRequest')->andReturn(new Request('GET', '/'));
        $provider->shouldReceive('getResponse')
            ->andReturn(new Response(200, [], json_encode(['users' => [['id' => 'u1'], ['id' => 'u2']]])));
        $this->withMockedProvider($service, $provider);

        $this->assertSame([['id' => 'u1'], ['id' => 'u2']], $service->getUsers('token'));
    }

    public function test_get_user_apps_maps_token_items_to_titles(): void
    {
        $service = new GoogleService();
        $provider = Mockery::mock(GenericProvider::class);
        $provider->shouldReceive('getAuthenticatedRequest')->andReturn(new Request('GET', '/'));
        $provider->shouldReceive('getResponse')
            ->andReturn(new Response(200, [], json_encode(['items' => [['displayText' => 'Slack'], ['displayText' => 'Notion']]])));
        $this->withMockedProvider($service, $provider);

        $this->assertSame([
            ['title' => 'Slack', 'created_at' => ''],
            ['title' => 'Notion', 'created_at' => ''],
        ], $service->getUserApps('token', 'user-1'));
    }
}
