<?php

namespace Tests\Feature\Api\Universal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Universal API Test for JobApiController
 * Implements Laravel 12 API testing best practices with Universal MCP patterns.
 *
 * @internal
 *
 * @coversNothing
 */
class JobApiControllerTest extends TestCase
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
            'job:create',
            'job:read',
            'job:update',
            'job:delete',
        ]);
    }

    /**
     * Universal Pattern: Test API index endpoint.
     */
    public function testIndexReturnsPaginatedResults(): void
    {
        Job::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/jobs');

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
            ])
        ;
    }

    /**
     * Universal Pattern: Test API store endpoint.
     */
    public function testStoreCreatesNewResource(): void
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
                'meta',
            ])
        ;

        $this->assertDatabaseHas('jobs', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Universal Pattern: Test API validation.
     */
    public function testStoreValidatesRequiredFields(): void
    {
        $response = $this->postJson('/api/v1/jobs', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonValidationErrors(['name'])
        ;
    }

    /**
     * Universal Pattern: Test API show endpoint.
     */
    public function testShowReturnsSingleResource(): void
    {
        $job = Job::factory()->create();

        $response = $this->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $job->id,
                    'name' => $job->name,
                ],
            ])
        ;
    }

    /**
     * Universal Pattern: Test API update endpoint.
     */
    public function testUpdateModifiesExistingResource(): void
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
            ])
        ;

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Universal Pattern: Test API delete endpoint.
     */
    public function testDestroyDeletesResource(): void
    {
        $job = Job::factory()->create();

        $response = $this->deleteJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Job deleted successfully',
            ])
        ;

        $this->assertSoftDeleted($job);
    }

    /**
     * Universal Pattern: Test unauthorized access.
     */
    public function testUnauthorizedAccessReturns401(): void
    {
        Sanctum::actingAs($this->user, []); // No abilities

        $response = $this->postJson('/api/v1/jobs', [
            'name' => 'Test Name',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Universal Pattern: Test rate limiting.
     */
    public function testRateLimitingPreventsExcessiveRequests(): void
    {
        // Make requests up to the limit
        for ($i = 0; $i < 60; ++$i) {
            $this->getJson('/api/v1/jobs');
        }

        // Next request should be rate limited
        $response = $this->getJson('/api/v1/jobs');
        $response->assertStatus(429);
    }

    /**
     * Universal Pattern: Test search functionality.
     */
    public function testIndexCanSearchResources(): void
    {
        Job::factory()->create(['name' => 'Searchable Item']);
        Job::factory()->create(['name' => 'Other Item']);

        $response = $this->getJson('/api/v1/jobs?search=Searchable');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
        ;
    }

    /**
     * Universal Pattern: Test resource not found.
     */
    public function testShowReturns404ForNonexistentResource(): void
    {
        $response = $this->getJson('/api/v1/jobs/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Job not found',
            ])
        ;
    }

    /**
     * Universal Pattern: Test invalid JSON.
     */
    public function testStoreHandlesInvalidJson(): void
    {
        $response = $this->json('POST', '/api/v1/jobs', 'invalid-json', [
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(400);
    }
}
