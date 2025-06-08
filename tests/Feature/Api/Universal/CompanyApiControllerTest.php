<?php

namespace Tests\Feature\Api\Universal;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

/**
 * Universal API Test for CompanyApiController
 * Implements Laravel 12 API testing best practices with Universal MCP patterns
 */
class CompanyApiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Universal Pattern: Create authenticated API user with tokens
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, [
            'company:create',
            'company:read',
            'company:update',
            'company:delete',
        ]);
    }

    /**
     * Universal Pattern: Test API index endpoint
     */
    public function test_index_returns_paginated_results(): void
    {
        Company::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/companys');

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

        $response = $this->postJson('/api/v1/companys', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Company created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email'],
                'meta'
            ]);

        $this->assertDatabaseHas('companys', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Universal Pattern: Test API validation
     */
    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/companys', []);

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
        $company = Company::factory()->create();

        $response = $this->getJson("/api/v1/companys/{$company->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                ]
            ]);
    }

    /**
     * Universal Pattern: Test API update endpoint
     */
    public function test_update_modifies_existing_resource(): void
    {
        $company = Company::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/v1/companys/{$company->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Company updated successfully',
            ]);

        $this->assertDatabaseHas('companys', [
            'id' => $company->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Universal Pattern: Test API delete endpoint
     */
    public function test_destroy_deletes_resource(): void
    {
        $company = Company::factory()->create();

        $response = $this->deleteJson("/api/v1/companys/{$company->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Company deleted successfully',
            ]);

        $this->assertSoftDeleted($company);
    }

    /**
     * Universal Pattern: Test unauthorized access
     */
    public function test_unauthorized_access_returns_401(): void
    {
        Sanctum::actingAs($this->user, []); // No abilities

        $response = $this->postJson('/api/v1/companys', [
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
            $this->getJson('/api/v1/companys');
        }

        // Next request should be rate limited
        $response = $this->getJson('/api/v1/companys');
        $response->assertStatus(429);
    }

    /**
     * Universal Pattern: Test search functionality
     */
    public function test_index_can_search_resources(): void
    {
        Company::factory()->create(['name' => 'Searchable Item']);
        Company::factory()->create(['name' => 'Other Item']);

        $response = $this->getJson('/api/v1/companys?search=Searchable');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    /**
     * Universal Pattern: Test resource not found
     */
    public function test_show_returns_404_for_nonexistent_resource(): void
    {
        $response = $this->getJson('/api/v1/companys/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Company not found',
            ]);
    }

    /**
     * Universal Pattern: Test invalid JSON
     */
    public function test_store_handles_invalid_json(): void
    {
        $response = $this->json('POST', '/api/v1/companys', 'invalid-json', [
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(400);
    }
}
