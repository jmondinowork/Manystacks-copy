<?php

namespace Tests\Unit\Services;

use App\Models\EntrepriseInformation;
use App\Models\OauthToken;
use App\Services\SIRHService;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use League\OAuth2\Client\Provider\GenericProvider;
use Mockery;
use Tests\TestCase;

class SIRHServiceTest extends TestCase
{
    use RefreshDatabase;

    private function configurePayfit(): void
    {
        config([
            'api.PAYFIT_API_KEY' => 'payfit-key',
            'api.PAYFIT_API_SECRET' => 'payfit-secret',
            'api.PAYFIT_URL_AUTHORIZE' => 'https://oauth.payfit.com/authorize',
            'api.PAYFIT_URL_TOKEN' => 'https://oauth.payfit.com/token',
            'api.PAYFIT_URL_RESSOURCE' => 'https://oauth.payfit.com/me',
            'api.PAYFIT_SCOPE' => 'collaborators:read',
            'api.PAYFIT_URL_EMPLOYEES' => 'https://api.payfit.com/companies/{companyId}/collaborators',
        ]);
    }

    private function withMockedProvider(SIRHService $service, $provider): void
    {
        $property = new \ReflectionProperty($service, 'provider');
        $property->setAccessible(true);
        $property->setValue($service, $provider);
    }

    public function test_authorization_url_targets_the_configured_provider(): void
    {
        $this->configurePayfit();

        $url = (new SIRHService('payfit'))->getAuthorizationUrl();

        $this->assertStringContainsString('oauth.payfit.com', $url);
        $this->assertStringContainsString('payfit-key', $url);
    }

    public function test_get_access_token_returns_null_when_no_token_exists(): void
    {
        $this->configurePayfit();

        $this->assertNull((new SIRHService('payfit'))->getAccessToken(999));
    }

    public function test_get_access_token_returns_the_stored_token_and_company(): void
    {
        $this->configurePayfit();
        $entreprise = EntrepriseInformation::create(['raison_sociale' => 'Acme', 'siret' => '99988877766655']);

        OauthToken::create([
            'service_name' => 'payfit',
            'entreprise_id' => $entreprise->id,
            'access_token' => 'sirh-token',
            'company_id' => 'company-123',
            'access_token_expires_at' => now()->addHour(),
        ]);

        $result = (new SIRHService('payfit'))->getAccessToken($entreprise->id);

        $this->assertSame('sirh-token', $result['access_token']);
        $this->assertSame('company-123', $result['company_id']);
    }

    public function test_get_employees_follows_pagination(): void
    {
        $this->configurePayfit();
        $service = new SIRHService('payfit');

        $provider = Mockery::mock(GenericProvider::class);
        $provider->shouldReceive('getAuthenticatedRequest')->andReturn(new Request('GET', '/'));
        $provider->shouldReceive('getResponse')->andReturn(
            new Response(200, [], json_encode([
                'collaborators' => [['id' => 1], ['id' => 2]],
                'meta' => ['nextPageToken' => 'page-2'],
            ])),
            new Response(200, [], json_encode([
                'collaborators' => [['id' => 3]],
            ]))
        );
        $this->withMockedProvider($service, $provider);

        $employees = $service->getEmployees('token', 'company-123');

        $this->assertSame([['id' => 1], ['id' => 2], ['id' => 3]], $employees);
    }
}
