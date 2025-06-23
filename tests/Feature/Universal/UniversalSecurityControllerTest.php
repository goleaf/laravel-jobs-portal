<?php

namespace Tests\Feature\Universal;

use Tests\TestCase;
use App\Models\User;
use App\Models\SecurityEvent;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;

class UniversalSecurityControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $google2fa;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'security@test.com',
            'password' => Hash::make('password123')
        ]);
        
        $this->google2fa = new Google2FA();
        $this->actingAs($this->user);
    }

    /** @test */
    public function test_can_view_security_dashboard()
    {
        $response = $this->get('/security');

        $response->assertStatus(200)
                ->assertViewIs('security.index')
                ->assertViewHas(['securityMetrics', 'recentEvents', 'activeSessions']);
    }

    /** @test */
    public function test_can_get_security_overview()
    {
        // Create some security events
        SecurityEvent::factory(5)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/security/overview');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'security_score',
                        'threat_level',
                        'recent_events',
                        'active_sessions',
                        'failed_login_attempts',
                        'security_recommendations'
                    ]
                ]);
    }

    /** @test */
    public function test_can_enable_two_factor_authentication()
    {
        $response = $this->postJson('/api/security/2fa/enable');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'qr_code',
                        'secret_key',
                        'backup_codes'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'two_factor_enabled' => true
        ]);

        $this->assertNotNull($this->user->fresh()->two_factor_secret);
    }

    /** @test */
    public function test_can_verify_two_factor_code()
    {
        // Enable 2FA first
        $secret = $this->google2fa->generateSecretKey();
        $this->user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => true
        ]);

        $validCode = $this->google2fa->getCurrentOtp($secret);

        $response = $this->postJson('/api/security/2fa/verify', [
            'code' => $validCode
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => '2FA verification successful'
                ]);
    }

    /** @test */
    public function test_rejects_invalid_two_factor_code()
    {
        // Enable 2FA first
        $secret = $this->google2fa->generateSecretKey();
        $this->user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => true
        ]);

        $response = $this->postJson('/api/security/2fa/verify', [
            'code' => '123456' // Invalid code
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid 2FA code'
                ]);
    }

    /** @test */
    public function test_can_disable_two_factor_authentication()
    {
        // Enable 2FA first
        $this->user->update([
            'two_factor_secret' => encrypt('secret'),
            'two_factor_enabled' => true
        ]);

        $response = $this->postJson('/api/security/2fa/disable', [
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => '2FA disabled successfully'
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'two_factor_enabled' => false
        ]);
    }

    /** @test */
    public function test_can_regenerate_backup_codes()
    {
        // Enable 2FA first
        $this->user->update([
            'two_factor_enabled' => true,
            'two_factor_backup_codes' => json_encode(['old-code-1', 'old-code-2'])
        ]);

        $response = $this->postJson('/api/security/2fa/regenerate-backup-codes');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'backup_codes'
                    ]
                ]);

        $newCodes = $response->json('data.backup_codes');
        $oldCodes = ['old-code-1', 'old-code-2'];

        $this->assertNotEquals($oldCodes, $newCodes);
        $this->assertCount(8, $newCodes); // Standard number of backup codes
    }

    /** @test */
    public function test_can_get_active_sessions()
    {
        // Create some user sessions
        UserSession::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/security/sessions');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'ip_address',
                            'user_agent',
                            'location',
                            'last_activity',
                            'is_current_session'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function test_can_revoke_session()
    {
        $session = UserSession::factory()->create([
            'user_id' => $this->user->id,
            'session_id' => 'test-session-id'
        ]);

        $response = $this->deleteJson("/api/security/sessions/{$session->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Session revoked successfully'
                ]);

        $this->assertDatabaseMissing('user_sessions', [
            'id' => $session->id
        ]);
    }

    /** @test */
    public function test_can_revoke_all_other_sessions()
    {
        // Create multiple sessions
        UserSession::factory(3)->create(['user_id' => $this->user->id]);
        $currentSession = UserSession::factory()->create([
            'user_id' => $this->user->id,
            'session_id' => session()->getId()
        ]);

        $response = $this->postJson('/api/security/sessions/revoke-all-others');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'All other sessions revoked successfully'
                ]);

        // Only current session should remain
        $this->assertDatabaseCount('user_sessions', 1);
        $this->assertDatabaseHas('user_sessions', [
            'id' => $currentSession->id
        ]);
    }

    /** @test */
    public function test_can_change_password()
    {
        $response = $this->putJson('/api/security/password', [
            'current_password' => 'password123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Password changed successfully'
                ]);

        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    /** @test */
    public function test_password_change_validates_current_password()
    {
        $response = $this->putJson('/api/security/password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['current_password']);
    }

    /** @test */
    public function test_can_get_security_events()
    {
        // Create security events
        SecurityEvent::factory(10)->create([
            'user_id' => $this->user->id,
            'event_type' => 'login_success'
        ]);

        SecurityEvent::factory(5)->create([
            'user_id' => $this->user->id,
            'event_type' => 'password_change'
        ]);

        $response = $this->getJson('/api/security/events');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'event_type',
                            'severity',
                            'ip_address',
                            'user_agent',
                            'event_data',
                            'created_at'
                        ]
                    ],
                    'pagination'
                ]);
    }

    /** @test */
    public function test_can_filter_security_events_by_type()
    {
        SecurityEvent::factory()->create([
            'user_id' => $this->user->id,
            'event_type' => 'login_success'
        ]);

        SecurityEvent::factory()->create([
            'user_id' => $this->user->id,
            'event_type' => 'suspicious_activity'
        ]);

        $response = $this->getJson('/api/security/events?event_type=login_success');

        $response->assertStatus(200);
        
        $events = $response->json('data');
        foreach ($events as $event) {
            $this->assertEquals('login_success', $event['event_type']);
        }
    }

    /** @test */
    public function test_can_get_security_recommendations()
    {
        $response = $this->getJson('/api/security/recommendations');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'recommendations' => [
                            '*' => [
                                'type',
                                'priority',
                                'title',
                                'description',
                                'action_url'
                            ]
                        ],
                        'security_score',
                        'completed_actions'
                    ]
                ]);
    }

    /** @test */
    public function test_can_run_security_scan()
    {
        $response = $this->postJson('/api/security/scan');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'scan_id',
                        'vulnerabilities_found',
                        'security_score',
                        'recommendations',
                        'scan_date'
                    ]
                ]);
    }

    /** @test */
    public function test_can_report_security_incident()
    {
        $incidentData = [
            'incident_type' => 'suspicious_activity',
            'description' => 'Unusual login pattern detected',
            'severity' => 'medium',
            'affected_resources' => ['user_account']
        ];

        $response = $this->postJson('/api/security/incidents', $incidentData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'incident_id',
                        'status',
                        'priority',
                        'assigned_to',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('security_incidents', [
            'incident_type' => 'suspicious_activity',
            'severity' => 'medium',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function test_can_get_threat_intelligence()
    {
        $response = $this->getJson('/api/security/threat-intelligence');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'current_threat_level',
                        'active_threats',
                        'global_incidents',
                        'protection_status',
                        'last_updated'
                    ]
                ]);
    }

    /** @test */
    public function test_security_middleware_logs_events()
    {
        Event::fake();

        // Trigger a suspicious request
        $this->postJson('/api/security/test-suspicious', [
            'malicious_payload' => '<script>alert("xss")</script>'
        ]);

        Event::assertDispatched(\App\Events\SecurityThreatDetected::class);
    }

    /** @test */
    public function test_can_configure_security_settings()
    {
        $settings = [
            'session_timeout' => 3600,
            'max_login_attempts' => 5,
            'password_expiry_days' => 90,
            'require_2fa' => true,
            'ip_whitelist_enabled' => false
        ];

        $response = $this->putJson('/api/security/settings', $settings);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Security settings updated successfully'
                ]);

        foreach ($settings as $key => $value) {
            $this->assertEquals($value, Cache::get("security.{$key}"));
        }
    }

    /** @test */
    public function test_can_export_security_audit_log()
    {
        // Create audit events
        SecurityEvent::factory(20)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/security/audit/export?format=json');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'application/json')
                ->assertJsonStructure([
                    'export_info',
                    'events' => [
                        '*' => [
                            'timestamp',
                            'event_type',
                            'user_id',
                            'ip_address',
                            'details'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function test_rate_limiting_on_security_endpoints()
    {
        // Attempt to hit the 2FA verification endpoint many times
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/security/2fa/verify', [
                'code' => '123456'
            ]);
        }

        // Should be rate limited
        $response->assertStatus(429);
    }

    /** @test */
    public function test_unauthorized_access_to_other_user_security_data()
    {
        $otherUser = User::factory()->create();
        $otherUserEvent = SecurityEvent::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->getJson("/api/security/events/{$otherUserEvent->id}");

        $response->assertStatus(403)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
    }

    /** @test */
    public function test_security_dashboard_shows_correct_metrics()
    {
        // Create test data
        SecurityEvent::factory(5)->create([
            'user_id' => $this->user->id,
            'event_type' => 'login_success'
        ]);

        SecurityEvent::factory(2)->create([
            'user_id' => $this->user->id,
            'event_type' => 'login_failed'
        ]);

        $response = $this->getJson('/api/security/dashboard-metrics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total_events',
                        'failed_logins',
                        'successful_logins',
                        'security_score',
                        'last_login',
                        'account_age_days'
                    ]
                ]);

        $data = $response->json('data');
        $this->assertEquals(7, $data['total_events']);
        $this->assertEquals(2, $data['failed_logins']);
        $this->assertEquals(5, $data['successful_logins']);
    }
} 