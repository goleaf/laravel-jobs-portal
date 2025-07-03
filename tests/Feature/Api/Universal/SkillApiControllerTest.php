<?php

namespace Tests\Feature\Api\Universal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Universal API Test for SkillApiController
 * Implements Laravel 12 API testing best practices with Universal MCP patterns.
 *
 * @internal
 *
 * @coversNothing
 */
class SkillApiControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Universal Pattern: Create authenticated API user with tokens
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, [
            'skill:create',
            'skill:read',
            'skill:update',
            'skill:delete',
        ]);
    }

    /**
     * Universal Pattern: Test API index endpoint.
     */
    public function test_index_returns_paginated_results(): void
    {
        Skill::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/skills');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => ['id', 'name', 'created_at', 'updated_at'],
                    ],
                    'current_page',
                    'per_page',
                    'total',
                ],
                'meta',
            ]);
    }

    /**
     * Universal Pattern: Test API store endpoint.
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

        $response = $this->postJson('/api/v1/skills', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Skill created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email'],
                'meta',
            ]);

        $this->assertDatabaseHas('skills', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Universal Pattern: Test API validation.
     */
    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/skills', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Universal Pattern: Test API show endpoint.
     */
    public function test_show_returns_single_resource(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->getJson("/api/v1/skills/{$skill->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $skill->id,
                    'name' => $skill->name,
                ],
            ]);
    }

    /**
     * Universal Pattern: Test API update endpoint.
     */
    public function test_update_modifies_existing_resource(): void
    {
        $skill = Skill::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/v1/skills/{$skill->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Skill updated successfully',
            ]);

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Universal Pattern: Test API delete endpoint.
     */
    public function test_destroy_deletes_resource(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->deleteJson("/api/v1/skills/{$skill->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Skill deleted successfully',
            ]);

        $this->assertSoftDeleted($skill);
    }

    /**
     * Universal Pattern: Test unauthorized access.
     */
    public function test_unauthorized_access_returns401(): void
    {
        Sanctum::actingAs($this->user, []); // No abilities

        $response = $this->postJson('/api/v1/skills', [
            'name' => 'Test Name',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Universal Pattern: Test rate limiting.
     */
    public function test_rate_limiting_prevents_excessive_requests(): void
    {
        // Make requests up to the limit
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/v1/skills');
        }

        // Next request should be rate limited
        $response = $this->getJson('/api/v1/skills');
        $response->assertStatus(429);
    }

    /**
     * Universal Pattern: Test search functionality.
     */
    public function test_index_can_search_resources(): void
    {
        Skill::factory()->create(['name' => 'Searchable Item']);
        Skill::factory()->create(['name' => 'Other Item']);

        $response = $this->getJson('/api/v1/skills?search=Searchable');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    /**
     * Universal Pattern: Test resource not found.
     */
    public function test_show_returns404_for_nonexistent_resource(): void
    {
        $response = $this->getJson('/api/v1/skills/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Skill not found',
            ]);
    }

    /**
     * Universal Pattern: Test invalid JSON.
     */
    public function test_store_handles_invalid_json(): void
    {
        $response = $this->json('POST', '/api/v1/skills', 'invalid-json', [
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(400);
    }
}
