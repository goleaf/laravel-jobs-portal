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
}
