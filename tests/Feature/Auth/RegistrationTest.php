<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_is_restricted_to_guests(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect('/dashboard');
    }

    public function test_registration_screen_is_restricted_to_non_super_admins(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect('/dashboard');
    }

    public function test_super_admin_can_view_the_registration_screen(): void
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($user)->get('/register');

        $response->assertStatus(200);
    }
}
