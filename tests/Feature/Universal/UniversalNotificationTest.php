    /**
     * Test notification index page loads successfully
     */
    public function test_notification_index_loads_successfully()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('notifications.index'));
        
        $response->assertStatus(200)
                ->assertViewIs('notifications.index')
                ->assertViewHas(['notifications', 'counts', 'filter', 'sort']);
    }

    /**
     * Test notification filtering by type
     */
    public function test_notification_filtering_by_type()
    {
        $user = User::factory()->create();
        
        // Create test notifications
        $user->notify(new \App\Notifications\JobApplicationNotification(['title' => 'Test Job Application']));
        $user->notify(new \App\Notifications\MessageNotification(['title' => 'Test Message']));
        
        // Test filtering by job applications
        $response = $this->actingAs($user)
            ->get(route('notifications.index', ['filter' => 'job_applications']));
        
        $response->assertStatus(200);
        
        // Test AJAX filtering
        $response = $this->actingAs($user)
            ->getJson(route('notifications.index', ['filter' => 'messages']));
        
        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'notifications',
                        'counts',
                        'current_filter',
                        'current_sort'
                    ]
                ]);
    }

    /**
     * Test marking notification as read
     */
    public function test_mark_notification_as_read()
    {
        $user = User::factory()->create();
        
        // Create unread notification
        $user->notify(new \App\Notifications\TestNotification(['title' => 'Test']));
        $notification = $user->unreadNotifications->first();
        
        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);
        
        // Mark as read
        $response = $this->actingAs($user)
            ->postJson(route('notifications.mark-read', $notification->id));
        
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => __('Notification marked as read')
                ])
                ->assertJsonStructure(['unread_count']);
        
        // Verify notification is marked as read
        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    /**
     * Test marking all notifications as read
     */
    public function test_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        
        // Create multiple unread notifications
        for ($i = 0; $i < 5; $i++) {
            $user->notify(new \App\Notifications\TestNotification(['title' => "Test $i"]));
        }
        
        $this->assertEquals(5, $user->unreadNotifications->count());
        
        // Mark all as read
        $response = $this->actingAs($user)
            ->postJson(route('notifications.mark-all-read'));
        
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => __('All notifications marked as read'),
                    'marked_count' => 5,
                    'unread_count' => 0
                ]);
        
        // Verify all notifications are marked as read
        $this->assertEquals(0, $user->unreadNotifications->count());
    }

    /**
     * Test deleting notification
     */
    public function test_delete_notification()
    {
        $user = User::factory()->create();
        
        // Create notification
        $user->notify(new \App\Notifications\TestNotification(['title' => 'Test']));
        $notification = $user->notifications->first();
        
        $this->assertNotNull($notification);
        
        // Delete notification
        $response = $this->actingAs($user)
            ->deleteJson(route('notifications.destroy', $notification->id));
        
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => __('Notification deleted')
                ]);
        
        // Verify notification is deleted
        $this->assertEquals(0, $user->notifications->count());
    }

    /**
     * Test notification settings retrieval
     */
    public function test_get_notification_settings()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->getJson(route('notifications.settings'));
        
        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'email_notifications',
                        'push_notifications',
                        'frequency',
                        'quiet_hours'
                    ]
                ]);
    }

    /**
     * Test updating notification settings
     */
    public function test_update_notification_settings()
    {
        $user = User::factory()->create();
        
        $settings = [
            'email_notifications' => [
                'job_applications' => true,
                'messages' => false,
                'system_updates' => true,
                'marketing' => false
            ],
            'push_notifications' => [
                'instant_messages' => true,
                'daily_digest' => false,
                'weekly_summary' => true
            ],
            'frequency' => 'daily',
            'quiet_hours' => [
                'enabled' => true,
                'start' => '22:00',
                'end' => '08:00'
            ]
        ];
        
        $response = $this->actingAs($user)
            ->putJson(route('notifications.settings.update'), $settings);
        
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => __('Notification settings updated successfully')
                ]);
    }

    /**
     * Test getting unread notifications count
     */
    public function test_get_unread_count()
    {
        $user = User::factory()->create();
        
        // Create unread notifications
        for ($i = 0; $i < 3; $i++) {
            $user->notify(new \App\Notifications\TestNotification(['title' => "Test $i"]));
        }
        
        $response = $this->actingAs($user)
            ->getJson(route('notifications.unread-count'));
        
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'count' => 3
                ]);
    }

    /**
     * Test sending test notification
     */
    public function test_send_test_notification()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->postJson(route('notifications.test'));
        
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => __('Test notification sent successfully')
                ]);
        
        // Verify test notification was created
        $this->assertEquals(1, $user->notifications->count());
    }

    /**
     * Test unauthorized access to other user's notifications
     */
    public function test_unauthorized_notification_access()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // Create notification for user1
        $user1->notify(new \App\Notifications\TestNotification(['title' => 'Test']));
        $notification = $user1->notifications->first();
        
        // Try to access user1's notification as user2
        $response = $this->actingAs($user2)
            ->postJson(route('notifications.mark-read', $notification->id));
        
        $response->assertStatus(404);
    }

    /**
     * Test notification pagination
     */
    public function test_notification_pagination()
    {
        $user = User::factory()->create();
        
        // Create many notifications
        for ($i = 0; $i < 25; $i++) {
            $user->notify(new \App\Notifications\TestNotification(['title' => "Test $i"]));
        }
        
        // Test first page
        $response = $this->actingAs($user)
            ->getJson(route('notifications.index', ['per_page' => 10]));
        
        $response->assertStatus(200)
                ->assertJson(['success' => true]);
        
        $data = $response->json('data');
        $this->assertEquals(10, count($data['notifications']['data']));
        $this->assertEquals(25, $data['notifications']['total']);
    }

    /**
     * Test notification sorting
     */
    public function test_notification_sorting()
    {
        $user = User::factory()->create();
        
        // Create notifications with different timestamps
        $user->notify(new \App\Notifications\TestNotification(['title' => 'Old']));
        sleep(1);
        $user->notify(new \App\Notifications\TestNotification(['title' => 'New']));
        
        // Test newest first (default)
        $response = $this->actingAs($user)
            ->getJson(route('notifications.index', ['sort' => 'newest']));
        
        $response->assertStatus(200);
        $notifications = $response->json('data.notifications.data');
        $this->assertEquals('New', $notifications[0]['data']['title']);
        
        // Test oldest first
        $response = $this->actingAs($user)
            ->getJson(route('notifications.index', ['sort' => 'oldest']));
        
        $response->assertStatus(200);
        $notifications = $response->json('data.notifications.data');
        $this->assertEquals('Old', $notifications[0]['data']['title']);
    }

    /**
     * Test notification settings validation
     */
    public function test_notification_settings_validation()
    {
        $user = User::factory()->create();
        
        // Test invalid frequency
        $response = $this->actingAs($user)
            ->putJson(route('notifications.settings.update'), [
                'frequency' => 'invalid'
            ]);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['frequency']);
        
        // Test invalid time format
        $response = $this->actingAs($user)
            ->putJson(route('notifications.settings.update'), [
                'quiet_hours' => [
                    'enabled' => true,
                    'start' => 'invalid-time',
                    'end' => '08:00'
                ]
            ]);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['quiet_hours.start']);
    }

    /**
     * Test error handling for invalid notification ID
     */
    public function test_invalid_notification_id_handling()
    {
        $user = User::factory()->create();
        
        // Try to mark non-existent notification as read
        $response = $this->actingAs($user)
            ->postJson(route('notifications.mark-read', 'invalid-id'));
        
        $response->assertStatus(404);
        
        // Try to delete non-existent notification
        $response = $this->actingAs($user)
            ->deleteJson(route('notifications.destroy', 'invalid-id'));
        
        $response->assertStatus(404);
    }

    /**
     * Test notification real-time updates simulation
     */
    public function test_notification_real_time_updates()
    {
        $user = User::factory()->create();
        
        // Get initial count
        $response = $this->actingAs($user)
            ->getJson(route('notifications.unread-count'));
        
        $initialCount = $response->json('count');
        
        // Create new notification
        $user->notify(new \App\Notifications\TestNotification(['title' => 'Real-time test']));
        
        // Get updated count
        $response = $this->actingAs($user)
            ->getJson(route('notifications.unread-count'));
        
        $newCount = $response->json('count');
        
        $this->assertEquals($initialCount + 1, $newCount);
    }

    /**
     * Test notification system performance with bulk operations
     */
    public function test_notification_bulk_operations_performance()
    {
        $user = User::factory()->create();
        
        // Create many notifications efficiently
        $start = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $user->notify(new \App\Notifications\TestNotification(['title' => "Bulk test $i"]));
        }
        
        $creationTime = microtime(true) - $start;
        
        // Test bulk mark as read performance
        $start = microtime(true);
        
        $response = $this->actingAs($user)
            ->postJson(route('notifications.mark-all-read'));
        
        $markReadTime = microtime(true) - $start;
        
        $response->assertStatus(200);
        
        // Assert reasonable performance (adjust thresholds as needed)
        $this->assertLessThan(10, $creationTime, 'Notification creation took too long');
        $this->assertLessThan(2, $markReadTime, 'Bulk mark as read took too long');
    } 