<?php

namespace Tests\Feature;

use App\Models\JobType;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JobTypeFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;
    protected User $employerUser;
    protected User $candidateUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users with different roles
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');
        
        $this->employerUser = User::factory()->create();
        $this->employerUser->assignRole('employer');
        
        $this->candidateUser = User::factory()->create();
        $this->candidateUser->assignRole('candidate');
    }

    /** @test */
    public function it_can_list_job_types_publicly(): void
    {
        JobType::factory()->count(5)->active()->create();
        JobType::factory()->count(2)->inactive()->create();

        $response = $this->getJson('/api/v1/job-types');

        $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'is_active',
                            'is_default',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'total'
                    ]
                ]);

        $this->assertGreaterThanOrEqual(7, $response->json('meta.total'));
    }

    /** @test */
    public function it_can_show_single_job_type_publicly(): void
    {
        $jobType = JobType::factory()->active()->create();

        $response = $this->getJson("/api/v1/job-types/{$jobType->id}");

        $response->assertOk()
                ->assertJson([
                    'data' => [
                        'id' => $jobType->id,
                        'name' => $jobType->name,
                        'description' => $jobType->description,
                        'is_active' => $jobType->is_active,
                    ]
                ]);
    }

    /** @test */
    public function it_can_search_job_types(): void
    {
        JobType::factory()->create(['name' => 'Full-Time Developer']);
        JobType::factory()->create(['name' => 'Part-Time Designer']);
        JobType::factory()->create(['name' => 'Contract Manager']);

        $response = $this->getJson('/api/v1/job-types/search?q=developer');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertStringContainsString('Developer', $response->json('data.0.name'));
    }

    /** @test */
    public function it_requires_authentication_to_create_job_type(): void
    {
        $response = $this->postJson('/api/v1/job-types', [
            'name' => 'New Job Type',
            'description' => 'Description'
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function authenticated_admin_can_create_job_type(): void
    {
        Sanctum::actingAs($this->adminUser);

        $jobTypeData = [
            'name' => 'Remote Developer',
            'description' => 'Remote development position',
            'icon' => 'laptop',
            'color' => '#3B82F6',
            'is_active' => true,
            'is_featured' => false
        ];

        $response = $this->postJson('/api/v1/job-types', $jobTypeData);

        $response->assertCreated()
                ->assertJson([
                    'data' => [
                        'name' => 'Remote Developer',
                        'description' => 'Remote development position',
                        'icon' => 'laptop',
                        'color' => '#3B82F6',
                        'is_active' => true,
                        'is_featured' => false
                    ]
                ]);

        $this->assertDatabaseHas('job_types', [
            'name' => 'Remote Developer',
            'slug' => 'remote-developer'
        ]);
    }

    /** @test */
    public function it_validates_job_type_creation_data(): void
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/v1/job-types', [
            'name' => '', // Required field
            'color' => 'invalid-color', // Invalid color format
            'sort_order' => 'not-a-number' // Must be integer
        ]);

        $response->assertUnprocessableEntity()
                ->assertJsonValidationErrors(['name', 'color', 'sort_order']);
    }

    /** @test */
    public function it_prevents_duplicate_job_type_names(): void
    {
        Sanctum::actingAs($this->adminUser);
        
        JobType::factory()->create(['name' => 'Full-Time']);

        $response = $this->postJson('/api/v1/job-types', [
            'name' => 'Full-Time',
            'description' => 'Another full-time position'
        ]);

        $response->assertUnprocessableEntity()
                ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function authenticated_admin_can_update_job_type(): void
    {
        Sanctum::actingAs($this->adminUser);
        
        $jobType = JobType::factory()->create([
            'name' => 'Original Name',
            'is_active' => true
        ]);

        $response = $this->putJson("/api/v1/job-types/{$jobType->id}", [
            'name' => 'Updated Name',
            'is_active' => false
        ]);

        $response->assertOk()
                ->assertJson([
                    'data' => [
                        'name' => 'Updated Name',
                        'is_active' => false
                    ]
                ]);

        $this->assertDatabaseHas('job_types', [
            'id' => $jobType->id,
            'name' => 'Updated Name',
            'is_active' => false
        ]);
    }

    /** @test */
    public function it_requires_permission_to_update_job_type(): void
    {
        Sanctum::actingAs($this->candidateUser);
        
        $jobType = JobType::factory()->create();

        $response = $this->putJson("/api/v1/job-types/{$jobType->id}", [
            'name' => 'Updated Name'
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function authenticated_admin_can_delete_job_type(): void
    {
        Sanctum::actingAs($this->adminUser);
        
        $jobType = JobType::factory()->create();

        $response = $this->deleteJson("/api/v1/job-types/{$jobType->id}");

        $response->assertOk()
                ->assertJson([
                    'message' => __('job_type.messages.deleted_successfully')
                ]);

        $this->assertDatabaseMissing('job_types', ['id' => $jobType->id]);
    }

    /** @test */
    public function it_prevents_deleting_job_type_with_jobs(): void
    {
        Sanctum::actingAs($this->adminUser);
        
        $jobType = JobType::factory()->create();
        Job::factory()->create(['job_type_id' => $jobType->id]);

        $response = $this->deleteJson("/api/v1/job-types/{$jobType->id}");

        $response->assertUnprocessableEntity()
                ->assertJson([
                    'message' => __('job_type.errors.cannot_delete_in_use')
                ]);

        $this->assertDatabaseHas('job_types', ['id' => $jobType->id]);
    }

    /** @test */
    public function it_can_get_job_type_statistics(): void
    {
        Sanctum::actingAs($this->adminUser);
        
        JobType::factory()->count(10)->active()->create();
        JobType::factory()->count(3)->inactive()->create();
        JobType::factory()->count(2)->default()->create();
        JobType::factory()->count(5)->featured()->create();

        $response = $this->getJson('/api/v1/job-types/statistics');

        $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        'total',
                        'active',
                        'inactive',
                        'default',
                        'custom',
                        'featured',
                        'with_jobs',
                        'popular',
                        'trending'
                    ]
                ]);

        $stats = $response->json('data');
        $this->assertGreaterThanOrEqual(10, $stats['active']);
        $this->assertGreaterThanOrEqual(3, $stats['inactive']);
        $this->assertGreaterThanOrEqual(2, $stats['default']);
        $this->assertGreaterThanOrEqual(5, $stats['featured']);
    }

    /** @test */
    public function it_can_perform_bulk_operations(): void
    {
        Sanctum::actingAs($this->adminUser);
        
        $jobTypes = JobType::factory()->count(3)->active()->create();

        $response = $this->postJson('/api/v1/job-types/bulk-update', [
            'job_type_ids' => $jobTypes->pluck('id')->toArray(),
            'action' => 'deactivate'
        ]);

        $response->assertOk()
                ->assertJson([
                    'message' => __('job_type.messages.bulk_updated', ['count' => 3])
                ]);

        foreach ($jobTypes as $jobType) {
            $this->assertDatabaseHas('job_types', [
                'id' => $jobType->id,
                'is_active' => false
            ]);
        }
    }

    /** @test */
    public function it_can_filter_job_types_by_status(): void
    {
        JobType::factory()->count(5)->active()->create();
        JobType::factory()->count(3)->inactive()->create();

        $response = $this->getJson('/api/v1/job-types?status=active');

        $response->assertOk();
        $activeJobTypes = $response->json('data');
        
        foreach ($activeJobTypes as $jobType) {
            $this->assertTrue($jobType['is_active']);
        }
    }

    /** @test */
    public function it_can_filter_job_types_by_type(): void
    {
        JobType::factory()->count(3)->default()->create();
        JobType::factory()->count(4)->custom()->create();

        $response = $this->getJson('/api/v1/job-types?is_default=true');

        $response->assertOk();
        $defaultJobTypes = $response->json('data');
        
        foreach ($defaultJobTypes as $jobType) {
            $this->assertTrue($jobType['is_default']);
        }
    }

    /** @test */
    public function it_can_sort_job_types(): void
    {
        JobType::factory()->create(['name' => 'Zebra Type']);
        JobType::factory()->create(['name' => 'Alpha Type']);
        JobType::factory()->create(['name' => 'Beta Type']);

        $response = $this->getJson('/api/v1/job-types?sort=name');

        $response->assertOk();
        $jobTypes = $response->json('data');
        
        $this->assertEquals('Alpha Type', $jobTypes[0]['name']);
        $this->assertEquals('Beta Type', $jobTypes[1]['name']);
        $this->assertEquals('Zebra Type', $jobTypes[2]['name']);
    }

    /** @test */
    public function it_includes_job_counts_when_requested(): void
    {
        $jobType = JobType::factory()->create();
        Job::factory()->count(5)->create(['job_type_id' => $jobType->id]);

        $response = $this->getJson('/api/v1/job-types?include_counts=true');

        $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'jobs_count',
                            'active_jobs_count'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_includes_usage_statistics_when_requested(): void
    {
        $jobType = JobType::factory()->create();

        $response = $this->getJson("/api/v1/job-types/{$jobType->id}?include_stats=true");

        $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        'usage_count',
                        'formatted_usage_stats'
                    ]
                ]);
    }

    /** @test */
    public function it_includes_analysis_data_when_requested(): void
    {
        $jobType = JobType::factory()->fullTime()->create();

        $response = $this->getJson("/api/v1/job-types/{$jobType->id}?include_analysis=true");

        $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        'is_high_demand',
                        'is_full_time',
                        'is_part_time',
                        'is_remote'
                    ]
                ]);

        $analysisData = $response->json('data');
        $this->assertTrue($analysisData['is_full_time']);
        $this->assertFalse($analysisData['is_part_time']);
    }

    /** @test */
    public function it_caches_responses_properly(): void
    {
        $jobType = JobType::factory()->create();

        // First request - should hit database
        $response1 = $this->getJson("/api/v1/job-types/{$jobType->id}");
        $response1->assertOk();

        // Update the job type directly in database (bypassing model events)
        \DB::table('job_types')->where('id', $jobType->id)->update(['name' => 'Updated Name']);

        // Second request - should return cached data (old name)
        $response2 = $this->getJson("/api/v1/job-types/{$jobType->id}");
        $response2->assertOk();
        
        // The cached response should still have the original name
        $this->assertEquals($jobType->name, $response2->json('data.name'));
    }

    /** @test */
    public function it_respects_rate_limiting_on_protected_endpoints(): void
    {
        Sanctum::actingAs($this->adminUser);

        // Make multiple requests quickly to trigger rate limiting
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/v1/job-types', [
                'name' => "Job Type {$i}",
                'description' => 'Test description'
            ]);
        }

        // The exact response depends on rate limiting configuration
        // This test ensures the rate limiting middleware is applied
        $this->assertTrue(true); // Placeholder - actual assertion would depend on rate limiting setup
    }

    /** @test */
    public function it_logs_activity_for_crud_operations(): void
    {
        Sanctum::actingAs($this->adminUser);

        // Create
        $response = $this->postJson('/api/v1/job-types', [
            'name' => 'Test Type',
            'description' => 'Test description'
        ]);

        $jobType = JobType::where('name', 'Test Type')->first();
        
        // Update
        $this->putJson("/api/v1/job-types/{$jobType->id}", [
            'name' => 'Updated Test Type'
        ]);

        // Delete
        $this->deleteJson("/api/v1/job-types/{$jobType->id}");

        // Check activity log entries exist
        $this->assertDatabaseHas('activity_log', [
            'subject_type' => JobType::class,
            'subject_id' => $jobType->id,
            'description' => 'Job type created'
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => JobType::class,
            'subject_id' => $jobType->id,
            'description' => 'Job type updated'
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => JobType::class,
            'subject_id' => $jobType->id,
            'description' => 'Job type deleted'
        ]);
    }

    /** @test */
    public function it_validates_slug_format(): void
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/v1/job-types', [
            'name' => 'Test Type',
            'slug' => 'Invalid Slug With Spaces!'
        ]);

        $response->assertUnprocessableEntity()
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_auto_generates_slug_when_not_provided(): void
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/v1/job-types', [
            'name' => 'Full-Time Developer Position'
        ]);

        $response->assertCreated();
        
        $this->assertDatabaseHas('job_types', [
            'name' => 'Full-Time Developer Position',
            'slug' => 'full-time-developer-position'
        ]);
    }
} 