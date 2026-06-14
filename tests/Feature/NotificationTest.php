<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\NewSupportMessage;
use App\Notifications\LicenseAssigned;
use App\Notifications\EquipmentEnrolled;
use App\Notifications\TeamMemberOnboarded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * Test fetching empty notifications for a user.
     */
    public function test_can_fetch_notifications(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJson([
                'notifications' => [],
                'unread_count' => 0
            ]);
    }

    /**
     * Test database serialization for all created notification types.
     */
    public function test_can_dispatch_and_fetch_all_notification_types(): void
    {
        $user = User::factory()->create();

        // 1. Order Status
        $user->notify(new OrderStatusUpdated('MS-123', 'validation', 'Votre commande MS-123 a été validée.'));
        
        // 2. Support
        $user->notify(new NewSupportMessage(42, 'Problème de connexion', 'Un agent a répondu à votre ticket.'));
        
        // 3. License
        $user->notify(new LicenseAssigned('Office 365', 'assigned', 'Une licence Office 365 vous a été assignée.'));
        
        // 4. Equipment
        $user->notify(new EquipmentEnrolled(99, 'MacBook Pro', 'Nouvel équipement en service.'));
        
        // 5. Team Member
        $user->notify(new TeamMemberOnboarded('Alice Martin', 'onboarded', 'Alice Martin a rejoint votre équipe.'));

        $response = $this
            ->actingAs($user)
            ->getJson('/api/notifications');

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertEquals(5, $data['unread_count']);
        $this->assertCount(5, $data['notifications']);

        // Verify some content structure
        $types = array_column(array_column($data['notifications'], 'data'), 'type');
        $this->assertContains('order', $types);
        $this->assertContains('support', $types);
        $this->assertContains('license', $types);
        $this->assertContains('equipment', $types);
        $this->assertContains('team', $types);
    }

    /**
     * Test marking a single notification as read.
     */
    public function test_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new OrderStatusUpdated('MS-123', 'validation', 'Votre commande MS-123 a été validée.'));

        $notificationId = $user->unreadNotifications->first()->id;

        $response = $this
            ->actingAs($user)
            ->postJson("/api/notifications/mark-as-read/{$notificationId}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 0
            ]);

        $this->assertEquals(0, $user->unreadNotifications()->count());
        $this->assertEquals(1, $user->readNotifications()->count());
    }

    /**
     * Test marking all notifications as read.
     */
    public function test_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new OrderStatusUpdated('MS-123', 'validation', 'Message 1'));
        $user->notify(new OrderStatusUpdated('MS-124', 'validation', 'Message 2'));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this
            ->actingAs($user)
            ->postJson('/api/notifications/mark-as-read');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 0
            ]);

        $this->assertEquals(0, $user->unreadNotifications()->count());
        $this->assertEquals(2, $user->readNotifications()->count());
    }

    /**
     * Test deleting/destroying a notification.
     */
    public function test_can_delete_notification(): void
    {
        $user = User::factory()->create();
        $user->notify(new OrderStatusUpdated('MS-123', 'validation', 'Votre commande MS-123 a été validée.'));

        $notificationId = $user->notifications->first()->id;

        $response = $this
            ->actingAs($user)
            ->deleteJson("/api/notifications/{$notificationId}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 0
            ]);

        $this->assertEquals(0, $user->notifications()->count());
    }
}
