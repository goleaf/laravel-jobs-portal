<?php

namespace Tests\Feature\Universal;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Universal Sanctum Authentication Tests
 * Comprehensive testing of Sanctum integration with Universal patterns
 */
class UniversalSanctumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'email' => 'test@universal.dev',
            'password' => bcrypt('password123')
        ]);
    }

    /**
     * Universal Pattern: Test successful login with token issuance
     */
    public function test_universal_login_issues_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@universal.dev',
            'password' => 'password123',
            'device_name' => 'test-device'
        ]);

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('user')
                    ->has('token')
                    ->has('abilities')
                    ->where('user.email', 'test@universal.dev')
                    ->whereType('token', 'string')
                    ->whereType('abilities', 'array')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test invalid login credentials
     */
    public function test_universal_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@universal.dev',
            'password' => 'wrongpassword',
            'device_name' => 'test-device'
        ]);

        $response
            ->assertUnauthorized()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('message')
                    ->has('errors')
                    ->where('message', 'Invalid credentials')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test authenticated user endpoint
     */
    public function test_universal_authenticated_user_endpoint(): void
    {
        Sanctum::actingAs($this->user, ['user:read']);

        $response = $this->getJson('/api/auth/user');

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('user')
                    ->has('token_abilities')
                    ->where('user.id', $this->user->id)
                    ->where('user.email', $this->user->email)
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test unauthenticated access protection
     */
    public function test_universal_unauthenticated_access_blocked(): void
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertUnauthorized();
    }

    /**
     * Universal Pattern: Test token logout
     */
    public function test_universal_token_logout(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertOk();
        
        // Verify token was revoked
        $this->assertEquals(0, $this->user->tokens()->count());
    }

    /**
     * Universal Pattern: Test logout all tokens
     */
    public function test_universal_logout_all_tokens(): void
    {
        // Create multiple tokens
        $token1 = $this->user->createToken('device1');
        $token2 = $this->user->createToken('device2');
        
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/auth/logout-all');

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('revoked_tokens', 2)
                    ->etc()
            );
        
        // Verify all tokens were revoked
        $this->assertEquals(0, $this->user->fresh()->tokens()->count());
    }

    /**
     * Universal Pattern: Test token abilities
     */
    public function test_universal_token_abilities(): void
    {
        Sanctum::actingAs($this->user, ['jobs:create', 'user:read']);

        $response = $this->getJson('/api/test/abilities');

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('user')
                    ->has('token_abilities')
                    ->where('can_create_jobs', true)
                    ->whereType('token_abilities', 'array')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test insufficient token abilities
     */
    public function test_universal_insufficient_token_abilities(): void
    {
        Sanctum::actingAs($this->user, ['user:read']); // Missing jobs:create

        $response = $this->getJson('/api/test/abilities');

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('can_create_jobs', false)
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test rate limiting on login
     */
    public function test_universal_login_rate_limiting(): void
    {
        // Make multiple failed login attempts
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@universal.dev',
                'password' => 'wrongpassword',
                'device_name' => 'test-device'
            ]);
        }

        // The 6th attempt should be rate limited (limit is 5 per minute)
        $response->assertStatus(429); // Too Many Requests
    }

    /**
     * Universal Pattern: Test Universal API routes with authentication
     */
    public function test_universal_api_routes_require_authentication(): void
    {
        $protectedRoutes = [
            'GET:/api/v1/user',
            'GET:/api/v1/job',
            'POST:/api/v1/job'
        ];

        foreach ($protectedRoutes as $route) {
            [$method, $uri] = explode(':', $route);
            
            $response = $this->json($method, $uri);
            
            $this->assertTrue(
                in_array($response->status(), [401, 405]), // 401 Unauthorized or 405 Method Not Allowed
                "Route {$route} should require authentication"
            );
        }
    }

    /**
     * Universal Pattern: Test user tokens listing
     */
    public function test_universal_user_tokens_listing(): void
    {
        // Create tokens
        $this->user->createToken('mobile-app', ['user:read', 'jobs:read']);
        $this->user->createToken('web-browser', ['*']);
        
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/auth/tokens');

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('tokens', 2)
                    ->has('tokens.0.id')
                    ->has('tokens.0.name')
                    ->has('tokens.0.abilities')
                    ->has('tokens.0.created_at')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test API versioning headers
     */
    public function test_universal_api_versioning_headers(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/auth/user');

        $response
            ->assertOk()
            ->assertHeader('X-API-Version', '1.0.0');
    }

    /**
     * Universal Pattern: Test security headers
     */
    public function test_universal_security_headers(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/auth/user');

        $response
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-XSS-Protection', '1; mode=block');
    }
}