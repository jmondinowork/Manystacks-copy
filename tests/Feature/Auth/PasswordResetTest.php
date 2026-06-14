<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\EmailService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        $mock = $this->mock(EmailService::class);
        $mock->shouldReceive('sendEmail')->once();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_reset_password_link_request_fails_for_unknown_email(): void
    {
        $response = $this->post('/forgot-password', ['email' => 'unknown@example.com']);

        $response->assertStatus(404);
        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $token = Password::getRepository()->create($user);

        $response = $this->get('/reset-password/'.$token);

        $response->assertStatus(200);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $token = Password::getRepository()->create($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('login'));
        Event::assertDispatched(PasswordReset::class);
    }
}
