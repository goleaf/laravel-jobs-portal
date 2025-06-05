<?php

namespace Tests\Feature\Api\Context7;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

/**
 * Context7 API Test for JobApiController
 * Implements Laravel 12 API testing best practices with Context7 MCP patterns
 */
class JobApiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Context7 Pattern: Create authenticated API user with tokens
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, [
            'job:create',
            'job:read',
            'job:update',
            'job:delete',
        ]);
    }

    /**
     * Context7 Pattern: Test API index endpoint
     */
    public function test_index_returns_paginated_results(): void
    {
        Job::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/jobs');

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
     * Context7 Pattern: Test API store endpoint
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

        $response = $this->postJson('/api/v1/jobs', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Job created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email'],
                'meta'
            ]);

        $this->assertDatabaseHas('jobs', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Context7 Pattern: Test API validation
     */
    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/jobs', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Context7 Pattern: Test API show endpoint
     */
    public function test_show_returns_single_resource(): void
    {
        $job = Job::factory()->create();

        $response = $this->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $job->id,
                    'name' => $job->name,
                ]
            ]);
    }

    /**
     * Context7 Pattern: Test API update endpoint
     */
    public function test_update_modifies_existing_resource(): void
    {
        $job = Job::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/v1/jobs/{$job->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Job updated successfully',
            ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Context7 Pattern: Test API delete endpoint
     */
    public function test_destroy_deletes_resource(): void
    {
        $job = Job::factory()->create();

        $response = $this->deleteJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Job deleted successfully',
            ]);

        $this->assertSoftDeleted($job);
    }

    /**
     * Context7 Pattern: Test unauthorized access
     */
    public function test_unauthorized_access_returns_401(): void
    {
        Sanctum::actingAs($this->user, []); // No abilities

        $response = $this->postJson('/api/v1/jobs', [
            'name' => 'Test Name',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Context7 Pattern: Test rate limiting
     */
    public function test_rate_limiting_prevents_excessive_requests(): void
    {
        // Make requests up to the limit
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/v1/jobs');
        }

        // Next request should be rate limited
        $response = $this->getJson('/api/v1/jobs');
        $response->assertStatus(429);
    }

    /**
     * Context7 Pattern: Test search functionality
     */
    public function test_index_can_search_resources(): void
    {
        Job::factory()->create(['name' => 'Searchable Item']);
        Job::factory()->create(['name' => 'Other Item']);

        $response = $this->getJson('/api/v1/jobs?search=Searchable');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    /**
     * Context7 Pattern: Test resource not found
     */
    public function test_show_returns_404_for_nonexistent_resource(): void
    {
        $response = $this->getJson('/api/v1/jobs/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Job not found',
            ]);
    }

    /**
     * Context7 Pattern: Test invalid JSON
     */
    public function test_store_handles_invalid_json(): void
    {
        $response = $this->json('POST', '/api/v1/jobs', 'invalid-json', [
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(400);
    }
}
