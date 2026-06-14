<?php

namespace Tests\Unit\Services;

use App\Exceptions\RecreateTenantAccessException;
use App\Services\MicrosoftService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Provider\GenericProvider;
use Mockery;
use Tests\TestCase;

class MicrosoftServiceTest extends TestCase
{
    private function serviceWithProvider($provider): MicrosoftService
    {
        $service = new MicrosoftService();
        $property = new \ReflectionProperty($service, 'provider');
        $property->setAccessible(true);
        $property->setValue($service, $provider);

        return $service;
    }

    public function test_get_users_returns_the_value_collection(): void
    {
        $provider = Mockery::mock(GenericProvider::class);
        $provider->shouldReceive('getAuthenticatedRequest')->andReturn(new Request('GET', '/'));
        $provider->shouldReceive('getResponse')
            ->andReturn(new Response(200, [], json_encode(['value' => [['id' => 'a'], ['id' => 'b']]])));

        $service = $this->serviceWithProvider($provider);

        $this->assertSame([['id' => 'a'], ['id' => 'b']], $service->getUsers('token'));
    }

    public function test_authentication_methods_are_summarised(): void
    {
        $provider = Mockery::mock(GenericProvider::class);
        $provider->shouldReceive('getAuthenticatedRequest')->andReturn(new Request('GET', '/'));
        $provider->shouldReceive('getResponse')
            ->andReturn(new Response(200, [], json_encode(['value' => [
                ['@odata.type' => '#microsoft.graph.phoneAuthenticationMethod', 'phoneNumber' => '+33600000000'],
                ['@odata.type' => '#microsoft.graph.passwordAuthenticationMethod'],
            ]])));

        $service = $this->serviceWithProvider($provider);

        $result = $service->getAutheticationMethods('token', 'user-1');

        $this->assertTrue($result['hasMfa']);
        $this->assertCount(1, $result['mfaMethods']);
        $this->assertSame('Téléphone', $result['mfaMethods'][0]['type']);
    }

    public function test_app_role_assignments_are_mapped(): void
    {
        $provider = Mockery::mock(GenericProvider::class);
        $provider->shouldReceive('getAuthenticatedRequest')->andReturn(new Request('GET', '/'));
        $provider->shouldReceive('getResponse')
            ->andReturn(new Response(200, [], json_encode(['value' => [
                ['resourceDisplayName' => 'Teams', 'createdDateTime' => '2024-01-01'],
            ]])));

        $service = $this->serviceWithProvider($provider);

        $this->assertSame([
            ['title' => 'Teams', 'created_at' => '2024-01-01'],
        ], $service->getAppRoleAssignments('token', 'user-1'));
    }

    public function test_a_403_response_triggers_a_tenant_recreate_exception(): void
    {
        $provider = Mockery::mock(GenericProvider::class);
        $provider->shouldReceive('getAuthenticatedRequest')->andReturn(new Request('GET', '/'));
        $provider->shouldReceive('getResponse')->andThrow(
            new ClientException('Forbidden', new Request('GET', '/'), new Response(403))
        );

        $service = $this->serviceWithProvider($provider);

        $this->expectException(RecreateTenantAccessException::class);

        $service->getUsers('token');
    }
}
