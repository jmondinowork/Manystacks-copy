<?php

namespace Tests\Unit\Models;

use App\Models\OauthToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OauthTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_token_is_expired_when_expiry_is_in_the_past(): void
    {
        $token = OauthToken::create([
            'service_name' => 'google',
            'access_token_expires_at' => now()->subMinute(),
        ]);

        $this->assertTrue($token->isAccessTokenExpired());
    }

    public function test_access_token_is_not_expired_when_expiry_is_in_the_future(): void
    {
        $token = OauthToken::create([
            'service_name' => 'google',
            'access_token_expires_at' => now()->addHour(),
        ]);

        $this->assertFalse($token->isAccessTokenExpired());
    }

    public function test_access_token_is_not_expired_when_no_expiry_is_set(): void
    {
        $token = OauthToken::create(['service_name' => 'google']);

        $this->assertFalse($token->isAccessTokenExpired());
    }

    public function test_refresh_token_expiry_is_evaluated(): void
    {
        $expired = OauthToken::create([
            'service_name' => 'ION',
            'refresh_token_expires_at' => now()->subDay(),
        ]);

        $valid = OauthToken::create([
            'service_name' => 'ION',
            'refresh_token_expires_at' => now()->addDay(),
        ]);

        $this->assertTrue($expired->isRefreshTokenExpired());
        $this->assertFalse($valid->isRefreshTokenExpired());
    }

    public function test_tokens_are_encrypted_at_rest_and_decrypted_on_read(): void
    {
        $token = OauthToken::create([
            'service_name' => 'microsoft',
            'access_token' => 'plain-access',
            'refresh_token' => 'plain-refresh',
        ]);

        $this->assertSame('plain-access', $token->fresh()->access_token);

        $raw = DB::table('oauth_tokens')->where('id', $token->id)->first();
        $this->assertNotSame('plain-access', $raw->access_token);
    }
}
