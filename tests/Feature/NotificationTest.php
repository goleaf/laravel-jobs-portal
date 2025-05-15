<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for testing
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_mark_notification_as_read()
    {
        // Create an unread notification for the user
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => null,
        ]);
        
        $response = $this->actingAs($this->user)
                         ->postJson("/notification/{$notification->id}/read");
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read_at' => now()->format('Y-m-d H:i'),
        ]);
    }

    /** @test */
    public function user_cannot_mark_another_users_notification_as_read()
    {
        // Create another user
        $anotherUser = User::factory()->create();
        
        // Create an unread notification for another user
        $notification = Notification::factory()->create([
            'user_id' => $anotherUser->id,
            'read_at' => null,
        ]);
        
        $response = $this->actingAs($this->user)
                         ->postJson("/notification/{$notification->id}/read");
        
        // Depending on how authorization is implemented, this could:
        // 1. Return 403 for forbidden
        // 2. Return 404 if the notification is not found in the scope of the current user
        // 3. Still mark it as read if there's no such restriction (in which case this test should be updated)
        
        $response->assertStatus(403); // Assuming proper authorization is implemented
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read_at' => null, // Should still be unread
        ]);
    }

    /** @test */
    public function user_can_mark_all_notifications_as_read()
    {
        // Create multiple unread notifications for the user
        Notification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'read_at' => null,
        ]);
        
        // Create one read notification to ensure it doesn't get updated again
        $readNotification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => now()->subDay(),
        ]);
        
        // Create notification for another user to ensure it doesn't get updated
        $anotherUser = User::factory()->create();
        $otherUserNotification = Notification::factory()->create([
            'user_id' => $anotherUser->id,
            'read_at' => null,
        ]);
        
        $response = $this->actingAs($this->user)
                         ->postJson('/read-all-notification');
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        // Check that all user's unread notifications are now read
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $this->user->id,
            'read_at' => null,
        ]);
        
        // Check that the other user's notification is still unread
        $this->assertDatabaseHas('notifications', [
            'id' => $otherUserNotification->id,
            'read_at' => null,
        ]);
    }
} 