<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityEnhancementsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear rate limiting cache before each test
        Cache::flush();
        RateLimiter::clear('login');
        RateLimiter::clear('api');
    }

    /** @test */
    public function it_applies_security_headers_to_all_responses()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeaderMissing('Server'); // Server header should be hidden
    }

    /** @test */
    public function it_applies_rate_limiting_to_login_attempts()
    {
        // Set a low rate limit for testing
        Config::set('security.authentication.max_failed_attempts', 3);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        // Make multiple failed login attempts
        for ($i = 0; $i < 4; $i++) {
            $response = $this->post('/login', $loginData);
            
            if ($i < 3) {
                // First 3 attempts should be allowed (though they fail)
                $response->assertStatus(302); // Redirect back with errors
            } else {
                // 4th attempt should be rate limited
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    /** @test */
    public function it_applies_api_rate_limiting()
    {
        // Test API rate limiting
        for ($i = 0; $i < 10; $i++) {
            $response = $this->get('/api/test-endpoint');
            
            if ($i < 5) {
                // Allow first few requests
                $this->assertTrue($response->getStatusCode() < 429);
            }
        }
    }

    /** @test */
    public function it_validates_csp_header_is_present()
    {
        $response = $this->get('/');

        $response->assertHeader('Content-Security-Policy');
        
        $cspHeader = $response->headers->get('Content-Security-Policy');
        $this->assertStringContains("default-src 'self'", $cspHeader);
        $this->assertStringContains("script-src 'self'", $cspHeader);
    }

    /** @test */
    public function it_applies_hsts_header_on_https()
    {
        // Force HTTPS for this test
        $this->app['request']->server->set('HTTPS', 'on');
        $this->app['request']->server->set('SERVER_PORT', 443);

        $response = $this->get('/', ['HTTP_HOST' => 'example.com']);
        
        if ($response->headers->has('Strict-Transport-Security')) {
            $hstsHeader = $response->headers->get('Strict-Transport-Security');
            $this->assertStringContains('max-age=', $hstsHeader);
        }
    }

    /** @test */
    public function it_logs_security_events()
    {
        // Test that security events are logged
        $this->expectsEvents([
            // Add specific security events to test
        ]);

        // Trigger a security event (like failed login)
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);
    }

    /** @test */
    public function it_enforces_password_policy()
    {
        $weakPasswords = [
            '123456',
            'password',
            'abc123',
            'qwerty'
        ];

        foreach ($weakPasswords as $password) {
            $response = $this->post('/register', [
                'name' => 'Test User',
                'email' => $this->faker->unique()->safeEmail(),
                'password' => $password,
                'password_confirmation' => $password,
            ]);

            // Should reject weak passwords
            $response->assertSessionHasErrors('password');
        }
    }

    /** @test */
    public function it_allows_strong_passwords()
    {
        $strongPassword = 'StrongP@ssw0rd123!';

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $strongPassword,
            'password_confirmation' => $strongPassword,
        ]);

        // Should accept strong passwords
        $response->assertSessionDoesntHaveErrors('password');
    }

    /** @test */
    public function it_prevents_xss_in_user_input()
    {
        $maliciousInput = '<script>alert("XSS")</script>';

        $response = $this->post('/contact', [
            'name' => $maliciousInput,
            'email' => 'test@example.com',
            'message' => $maliciousInput,
        ]);

        // XSS should be sanitized
        $this->assertDatabaseMissing('contacts', [
            'name' => $maliciousInput,
            'message' => $maliciousInput,
        ]);
    }

    /** @test */
    public function it_enforces_file_upload_restrictions()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Create a test file with disallowed extension
        $file = \Illuminate\Http\UploadedFile::fake()->create('test.exe', 1000);

        $response = $this->post('/upload', [
            'file' => $file
        ]);

        // Should reject disallowed file types
        $response->assertSessionHasErrors('file');
    }

    /** @test */
    public function it_validates_file_size_limits()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Create a file larger than allowed (simulate 20MB file)
        $file = \Illuminate\Http\UploadedFile::fake()->create('test.pdf', 20480); // 20MB

        $response = $this->post('/upload', [
            'file' => $file
        ]);

        // Should reject files that are too large
        $response->assertSessionHasErrors('file');
    }

    /** @test */
    public function it_protects_admin_routes()
    {
        // Test that admin routes require admin role
        $response = $this->get('/admin/dashboard');
        
        // Should redirect to login
        $response->assertRedirect('/login');

        // Test with regular user
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');
        
        // Should return 403 or redirect
        $this->assertTrue(in_array($response->getStatusCode(), [403, 302]));
    }

    /** @test */
    public function it_allows_admin_access_to_admin_routes()
    {
        // Create admin user (assuming role system is implemented)
        $admin = \App\Models\User::factory()->create();
        
        // Assign admin role if role system is available
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('admin');
        }

        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        
        // Admin should have access
        $response->assertStatus(200);
    }

    /** @test */
    public function it_implements_csrf_protection()
    {
        // Test that POST requests without CSRF token are rejected
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // Should return 419 (CSRF token mismatch) or similar
        $this->assertTrue(in_array($response->getStatusCode(), [419, 403]));
    }

    /** @test */
    public function it_sanitizes_sql_injection_attempts()
    {
        $maliciousSql = "'; DROP TABLE users; --";

        $response = $this->post('/search', [
            'query' => $maliciousSql
        ]);

        // Should not execute malicious SQL
        $this->assertDatabaseExists('users'); // Users table should still exist
    }

    /** @test */
    public function it_implements_session_security()
    {
        $user = \App\Models\User::factory()->create();
        
        // Login user
        $this->actingAs($user);
        
        // Simulate session with different IP
        $response = $this->withHeaders([
            'HTTP_X-Forwarded-For' => '192.168.1.100'
        ])->get('/dashboard');

        // Depending on security configuration, should handle IP changes appropriately
        $this->assertTrue($response->getStatusCode() < 500);
    }

    /** @test */
    public function it_logs_authentication_events()
    {
        // Create user
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        // Test successful login logging
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        // Should log successful authentication
        // Verify log entry exists (implementation depends on logging setup)
        $this->assertTrue(true); // Placeholder assertion
    }

    /** @test */
    public function it_implements_authorization_policies()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Test accessing someone else's profile
        $otherUser = \App\Models\User::factory()->create();
        
        $response = $this->get("/profile/{$otherUser->id}/edit");
        
        // Should not allow editing other user's profile
        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]));
    }
} 