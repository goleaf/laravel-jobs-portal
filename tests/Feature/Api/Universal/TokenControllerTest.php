<?php

namespace Tests\Feature\Api\Universal;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

/**
 * Universal API Test for TokenController
 * Implements Laravel 12 API testing best practices with Universal MCP patterns
 */
class TokenControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Universal Pattern: Create authenticated API user with tokens
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, [
            'token:create',
            'token:read',
            'token:update',
            'token:delete',
        ]);
    }

    /**
     * Universal Pattern: Test API index endpoint
     */
    public function test_index_returns_paginated_results(): void
    {
        Token::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/tokens');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => ['id', 'name', 'created_at', 'updated_at']
                    ],
                    'current_page',
                    'per_page',
                    'total'
                ],
                'meta'
            ]);
    }

    /**
     * Universal Pattern: Test API store endpoint
     */
    public function test_store_creates_new_resource(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'description' => $this->faker->sentence,
            'status' => 'active',
            'tags' => ['tag1', 'tag2'],
        ];

        $response = $this->postJson('/api/v1/tokens', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Token created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email'],
                'meta'
            ]);

        $this->assertDatabaseHas('tokens', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Universal Pattern: Test API validation
     */
    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/tokens', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Universal Pattern: Test API show endpoint
     */
    public function test_show_returns_single_resource(): void
    {
        $token = Token::factory()->create();

        $response = $this->getJson("/api/v1/tokens/{$token->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $token->id,
                    'name' => $token->name,
                ]
            ]);
    }

    /**
     * Universal Pattern: Test API update endpoint
     */
    public function test_update_modifies_existing_resource(): void
    {
        $token = Token::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/v1/tokens/{$token->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Token updated successfully',
            ]);

        $this->assertDatabaseHas('tokens', [
            'id' => $token->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Universal Pattern: Test API delete endpoint
     */
    public function test_destroy_deletes_resource(): void
    {
        $token = Token::factory()->create();

        $response = $this->deleteJson("/api/v1/tokens/{$token->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Token deleted successfully',
            ]);

        $this->assertSoftDeleted($token);
    }

    /**
     * Universal Pattern: Test unauthorized access
     */
    public function test_unauthorized_access_returns_401(): void
    {
        Sanctum::actingAs($this->user, []); // No abilities

        $response = $this->postJson('/api/v1/tokens', [
            'name' => 'Test Name',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Universal Pattern: Test rate limiting
     */
    public function test_rate_limiting_prevents_excessive_requests(): void
    {
        // Make requests up to the limit
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/v1/tokens');
        }

        // Next request should be rate limited
        $response = $this->getJson('/api/v1/tokens');
        $response->assertStatus(429);
    }

    /**
     * Universal Pattern: Test search functionality
     */
    public function test_index_can_search_resources(): void
    {
        Token::factory()->create(['name' => 'Searchable Item']);
        Token::factory()->create(['name' => 'Other Item']);

        $response = $this->getJson('/api/v1/tokens?search=Searchable');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    /**
     * Universal Pattern: Test resource not found
     */
    public function test_show_returns_404_for_nonexistent_resource(): void
    {
        $response = $this->getJson('/api/v1/tokens/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Token not found',
            ]);
    }

    /**
     * Universal Pattern: Test invalid JSON
     */
    public function test_store_handles_invalid_json(): void
    {
        $response = $this->json('POST', '/api/v1/tokens', 'invalid-json', [
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function it_can_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ],
                    'token' => [
                        'access_token',
                        'token_type',
                        'abilities'
                    ]
                ],
                'meta'
            ])
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'Test Device',
            'tokenable_id' => $this->user->id,
            'tokenable_type' => User::class,
        ]);
    }

    /** @test */
    public function it_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
    }

    /** @test */
    public function it_validates_login_request_data()
    {
        // Test missing email
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test invalid email format
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test short password
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => '123',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function it_can_get_authenticated_user_details()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at'
                    ],
                    'authentication' => [
                        'token_abilities'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $this->user->id,
                        'email' => $this->user->email
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_cannot_get_user_details_without_authentication()
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_logout_authenticated_user()
    {
        $token = $this->user->createToken('Test Token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);

        // Verify token was deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'name' => 'Test Token'
        ]);
    }

    /** @test */
    public function it_cannot_logout_without_authentication()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_logout_all_tokens()
    {
        // Create multiple tokens
        $token1 = $this->user->createToken('Token 1');
        $token2 = $this->user->createToken('Token 2');
        $token3 = $this->user->createToken('Token 3');

        Sanctum::actingAs($this->user, ['*'], $token1->accessToken);

        $response = $this->postJson('/api/auth/logout-all');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'revoked_tokens'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'revoked_tokens' => 3
                ]
            ]);

        // Verify all tokens were deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_list_user_tokens()
    {
        // Create multiple tokens
        $token1 = $this->user->createToken('Token 1');
        $token2 = $this->user->createToken('Token 2');

        Sanctum::actingAs($this->user, ['*'], $token1->accessToken);

        $response = $this->getJson('/api/auth/tokens');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'tokens' => [
                        '*' => [
                            'id',
                            'name',
                            'abilities',
                            'created_at'
                        ]
                    ],
                    'summary' => [
                        'total_tokens'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'summary' => [
                        'total_tokens' => 2
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_filter_tokens_with_query_parameters()
    {
        $token1 = $this->user->createToken('Token 1');
        $token2 = $this->user->createToken('Token 2');

        Sanctum::actingAs($this->user, ['*'], $token1->accessToken);

        $response = $this->getJson('/api/auth/tokens?limit=1&sort_by=created_at&sort_direction=desc');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => [
                    'pagination' => [
                        'limit'
                    ]
                ]
            ])
            ->assertJson([
                'meta' => [
                    'pagination' => [
                        'limit' => 1
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_validates_tokens_request_parameters()
    {
        Sanctum::actingAs($this->user);

        // Test invalid sort_by
        $response = $this->getJson('/api/auth/tokens?sort_by=invalid_field');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sort_by']);

        // Test invalid limit
        $response = $this->getJson('/api/auth/tokens?limit=101');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    /** @test */
    public function it_cannot_access_tokens_without_authentication()
    {
        $response = $this->getJson('/api/auth/tokens');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_handles_rate_limiting_on_login()
    {
        // Make multiple failed login attempts
        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
                'device_name' => 'Test Device'
            ]);
        }

        // The 6th attempt should be rate limited
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function it_sets_default_device_name_when_not_provided()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'API Client',
            'tokenable_id' => $this->user->id,
        ]);
    }
}
