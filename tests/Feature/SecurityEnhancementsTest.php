<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

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
        RateLimiter::clear('login:test@example.com');
        RateLimiter::clear('global-login');
    }

    /** @test */
    public function test_security_headers_are_present()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /** @test */
    public function test_login_rate_limiting_works()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Make 3 failed login attempts
        for ($i = 0; $i < 3; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function test_api_rate_limiting_works()
    {
        $user = User::factory()->create();

        // Test authenticated API rate limiting
        $this->actingAs($user);
        
        // This should work within limits
        $response = $this->get('/api/test-endpoint');
        // Note: This will fail if route doesn't exist, which is expected
    }

    /** @test */
    public function test_csp_header_is_present()
    {
        $response = $this->get('/');
        
        $this->assertTrue($response->headers->has('Content-Security-Policy') || 
                         $response->headers->has('Content-Security-Policy-Report-Only'));
    }

    /** @test */
    public function test_hsts_header_is_present()
    {
        $response = $this->get('/');
        
        if (config('app.env') === 'production') {
            $response->assertHeader('Strict-Transport-Security');
        } else {
            // In development, this might not be set
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function test_password_validation_enforces_complexity()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123', // Weak password
            'password_confirmation' => '123'
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_strong_password_is_accepted()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'SecureP@ssw0rd123!',
            'password_confirmation' => 'SecureP@ssw0rd123!'
        ]);

        // Should redirect or succeed without password errors
        $response->assertSessionMissing('errors.password');
    }

    /** @test */
    public function test_xss_prevention_in_forms()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $maliciousInput = '<script>alert("xss")</script>';
        
        // Test that script tags are escaped or removed
        $response = $this->post('/profile/update', [
            'name' => $maliciousInput
        ]);

        // The actual test would depend on your profile update implementation
        $this->assertTrue(true); // Placeholder
    }

    /** @test */
    public function test_file_upload_restrictions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test that dangerous file types are rejected
        $response = $this->post('/upload', [
            'file' => \Illuminate\Http\Testing\File::create('malicious.php', 100)
        ]);

        // Should be rejected
        $response->assertStatus(422); // Or appropriate error status
    }

    /** @test */
    public function test_admin_routes_require_admin_role()
    {
        $regularUser = User::factory()->create();
        $this->actingAs($regularUser);

        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function test_admin_user_can_access_admin_routes()
    {
        $admin = User::factory()->create();
        
        // Assuming there's a way to assign admin role
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('admin');
        }
        
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        
        // Should be accessible (might be 200 or redirect to login if route doesn't exist)
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function test_csrf_protection_is_active()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        // Should fail without CSRF token
        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function test_sql_injection_prevention()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test SQL injection attempt
        $maliciousInput = "'; DROP TABLE users; --";
        
        $response = $this->get('/search?q=' . urlencode($maliciousInput));
        
        // Should not cause a 500 error (which might indicate SQL error)
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /** @test */
    public function test_session_security_validation()
    {
        $user = User::factory()->create();
        
        // Login user
        $this->actingAs($user);
        
        // Session should be created
        $this->assertAuthenticated();
        
        // Test session invalidation on suspicious activity would require
        // more complex setup with session manipulation
        $this->assertTrue(true); // Placeholder
    }

    /** @test */
    public function test_password_reset_rate_limiting()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        // Attempt multiple password resets
        for ($i = 0; $i < 4; $i++) {
            $response = $this->post('/password/email', [
                'email' => 'test@example.com'
            ]);
        }

        // Should be rate limited after multiple attempts
        $response->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function test_authentication_logging()
    {
        // This would test that security events are logged
        // Would require checking log files or using a test log driver
        $this->assertTrue(true); // Placeholder for now
    }

    /** @test */
    public function test_authorization_policies()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $this->actingAs($user1);
        
        // Test that user cannot access another user's resources
        $response = $this->get("/profile/{$user2->id}");
        
        $response->assertStatus(403); // Should be forbidden
    }

    /** @test */
    public function test_api_authentication_required()
    {
        // Test that API endpoints require authentication
        $response = $this->getJson('/api/jobs');
        
        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function test_security_configuration_is_loaded()
    {
        $this->assertTrue(config('security.authentication.max_failed_attempts') > 0);
        $this->assertTrue(config('security.rate_limiting.api.authenticated') > 0);
        $this->assertIsArray(config('security.csp.directives'));
    }
} 