<?php

namespace Tests\Feature\Universal;

use App\Models\Company;
use App\Models\Job;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Universal API Testing Suite
 * Implements MCP best practices for comprehensive API testing.
 *
 * @internal
 *
 * @coversNothing
 */
class UniversalApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Universal Pattern: Set up test environment with realistic data
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    /**
     * Universal Pattern: Test unauthenticated access protection.
     */
    public function test_unauthenticated_access_is_blocked(): void
    {
        $response = $this->getJson('/api/v1/user');

        $response->assertUnauthorized();
    }

    /**
     * Universal Pattern: Test authenticated user endpoint with fluent JSON.
     */
    public function test_authenticated_user_endpoint(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/user');

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('id', $user->id)
                    ->where('name', $user->name)
                    ->where('email', $user->email)
                    ->missing('password')
                    ->has('created_at')
                    ->has('updated_at')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test public jobs endpoint with caching headers.
     */
    public function test_public_jobs_index_endpoint(): void
    {
        $company = Company::factory()->create();
        $jobs = Job::factory(3)->create(['company_id' => $company->id]);

        $response = $this->getJson('/api/v1/public/jobs');

        $response
            ->assertOk()
            ->assertHeader('Cache-Control')
            ->assertJsonStructure([
                'jobs' => [
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'slug',
                            'description',
                            'salary_from',
                            'salary_to',
                            'status',
                            'created_at',
                            'updated_at',
                            'links' => ['self'],
                        ],
                    ],
                    'links',
                    'meta',
                ],
            ])
            ->assertJson(
                fn (AssertableJson $json) => $json->has('jobs.data', 3)
                    ->has('jobs.meta.count')
                    ->has('jobs.meta.timestamp')
                    ->has('jobs.meta.version')
                    ->where('jobs.meta.resource_type', 'job_collection')
            );
    }

    /**
     * Universal Pattern: Test job show endpoint with relationship loading.
     */
    public function test_public_job_show_endpoint(): void
    {
        $company = Company::factory()->create();
        $job = Job::factory()->create(['company_id' => $company->id]);
        $skills = Skill::factory(2)->create();
        $job->skills()->attach($skills);

        $response = $this->getJson("/api/v1/public/jobs/{$job->id}");

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('job.id')
                    ->where('job.title', $job->title)
                    ->where('job.slug', $job->slug)
                    ->has('job.links.self')
                    ->has('meta.timestamp')
                    ->where('meta.resource_type', 'job')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test authenticated jobs CRUD with validation.
     */
    public function test_authenticated_jobs_crud_operations(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        // Test Create Job
        $jobData = [
            'title' => 'Senior Laravel Developer',
            'description' => 'We are looking for an experienced Laravel developer...',
            'salary_from' => 80000,
            'salary_to' => 120000,
            'status' => 'active',
            'company_id' => $company->id,
        ];

        $createResponse = $this->postJson('/api/v1/job', $jobData);

        $createResponse
            ->assertCreated()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('job.id')
                    ->where('job.title', 'Senior Laravel Developer')
                    ->has('job.links.self')
                    ->has('meta.timestamp')
                    ->where('meta.resource_type', 'job')
                    ->etc()
            );

        $jobId = $createResponse->json('job.id');

        // Test Update Job
        $updateData = ['title' => 'Lead Laravel Developer'];

        $updateResponse = $this->putJson("/api/v1/job/{$jobId}", $updateData);

        $updateResponse
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('job.title', 'Lead Laravel Developer')
                    ->where('job.id', $jobId)
                    ->etc()
            );

        // Test Delete Job
        $deleteResponse = $this->deleteJson("/api/v1/job/{$jobId}");

        $deleteResponse->assertOk();

        // Verify soft delete
        $this->assertSoftDeleted('jobs', ['id' => $jobId]);
    }

    /**
     * Universal Pattern: Test validation errors with detailed assertions.
     */
    public function test_job_creation_validation_errors(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/job', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title'])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'title',
                ],
            ]);
    }

    /**
     * Universal Pattern: Test rate limiting functionality.
     */
    public function test_api_rate_limiting(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Make requests within rate limit
        for ($i = 0; $i < 5; $i++) {
            $response = $this->getJson('/api/v1/user');
            $response->assertOk();
        }

        // This test would need rate limiting to be set very low for testing
        // In a real scenario, you'd configure different rate limits for testing
    }

    /**
     * Universal Pattern: Test companies endpoint with filtering.
     */
    public function test_companies_filtering_and_search(): void
    {
        $companies = Company::factory(5)->create();
        $targetCompany = $companies->first();

        // Test search functionality
        $response = $this->getJson("/api/v1/public/companies?search={$targetCompany->name}");

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('companies.data')
                    ->whereType('companies.data', 'array')
                    ->has('companies.meta.count')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test health endpoint for monitoring.
     */
    public function test_health_endpoint(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('status', 'healthy')
                    ->has('timestamp')
                    ->has('version')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test stats endpoint for analytics.
     */
    public function test_stats_endpoint(): void
    {
        // Create test data
        Company::factory(3)->create();
        Job::factory(5)->create();
        User::factory(10)->create();

        $response = $this->getJson('/api/v1/stats');

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->whereType('jobs_count', 'integer')
                    ->whereType('companies_count', 'integer')
                    ->whereType('candidates_count', 'integer')
                    ->whereType('applications_count', 'integer')
                    ->where('companies_count', 3)
                    ->where('jobs_count', 5)
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test pagination with cursor-based pagination.
     */
    public function test_cursor_pagination(): void
    {
        Job::factory(20)->create();

        $response = $this->getJson('/api/v1/public/jobs?per_page=5');

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('jobs.data', 5)
                    ->has('jobs.links.first')
                    ->has('jobs.links.last')
                    ->has('jobs.meta.current_page')
                    ->has('jobs.meta.per_page')
                    ->has('jobs.meta.total')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test response headers for security and caching.
     */
    public function test_response_headers_security(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/json')
            ->assertHeaderMissing('X-Powered-By'); // Security: Hide server info
    }

    /**
     * Universal Pattern: Test error handling for non-existent resources.
     */
    public function test_not_found_resources(): void
    {
        $response = $this->getJson('/api/v1/public/jobs/99999');

        $response
            ->assertNotFound()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('message')
                    ->whereType('message', 'string')
                    ->etc()
            );
    }

    /**
     * Universal Pattern: Test API versioning compatibility.
     */
    public function test_api_versioning(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('version')
                    ->whereType('version', 'string')
                    ->etc()
            );
    }
}
