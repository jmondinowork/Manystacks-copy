<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile/mon-compte');

        $response->assertOk();
    }

    public function test_account_information_can_be_updated(): void
    {
        $user = User::factory()->create(['type' => 'Personne']);

        $response = $this
            ->actingAs($user)
            ->post('/api/editAccount', [
                'id' => $user->id,
                'type' => 'Personne',
                'fname' => 'Jean',
                'lname' => 'Test',
            ]);

        $response->assertOk();
        $this->assertSame('Jean Test', $user->fresh()->name);
    }

    public function test_account_update_requires_an_id(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/api/editAccount', []);

        $response->assertSessionHasErrors('id');
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/api/editPassword', [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_password_update_requires_confirmation(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/api/editPassword', [
                'password' => 'new-password',
                'password_confirmation' => 'does-not-match',
            ]);

        $response->assertSessionHasErrors('password');
    }
}
