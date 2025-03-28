<?php

namespace Tests\Unit\Models;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_notification()
    {
        $user = User::factory()->create();
        
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'type' => 'job_application',
            'read_at' => null,
        ]);
        
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'title' => 'Test Notification',
        ]);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'type' => 'job_application',
            'read_at' => null,
        ]);
        
        $this->assertEquals($user->id, $notification->user->id);
    }

    /** @test */
    public function it_can_mark_notification_as_read()
    {
        $user = User::factory()->create();
        
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'type' => 'job_application',
            'read_at' => null,
        ]);
        
        $this->assertNull($notification->read_at);
        
        $notification->markAsRead();
        
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function it_can_determine_if_notification_is_read()
    {
        $user = User::factory()->create();
        
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'type' => 'job_application',
            'read_at' => null,
        ]);
        
        $this->assertFalse($notification->isRead());
        
        $notification->markAsRead();
        
        $this->assertTrue($notification->fresh()->isRead());
    }

    /** @test */
    public function it_can_scope_unread_notifications()
    {
        $user = User::factory()->create();
        
        // Create unread notification
        $unreadNotification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Unread Notification',
            'type' => 'job_application',
            'read_at' => null,
        ]);
        
        // Create read notification
        $readNotification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Read Notification',
            'type' => 'job_application',
            'read_at' => now(),
        ]);
        
        $unreadNotifications = Notification::unread()->get();
        
        $this->assertCount(1, $unreadNotifications);
        $this->assertEquals($unreadNotification->id, $unreadNotifications->first()->id);
    }
} 