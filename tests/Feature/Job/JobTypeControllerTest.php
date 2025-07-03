<?php

namespace Tests\Feature\Job;

use App\Models\JobType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Enhanced Feature Test for JobTypeController
 * Comprehensive testing for JobType controller functionality.
 *
 * @internal
 *
 * @coversNothing
 */
class JobTypeControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $admin;
    protected User $employer;
    protected User $candidate;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users with roles
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->employer = User::factory()->create();
        $this->employer->assignRole('Employer');

        $this->candidate = User::factory()->create();
        $this->candidate->assignRole('Candidate');
    }

    /** @test */
    public function admin_can_create_jobtype(): void
    {
        Sanctum::actingAs($this->admin);

        $data = [
            'name' => 'Full Time',
            'description' => 'Full-time employment',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/jobtype', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['name', 'description', 'is_active', 'id', 'created_at', 'updated_at'],
            ]);

        $this->assertDatabaseHas('job_types', [
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function employer_can_create_jobtype(): void
    {
        Sanctum::actingAs($this->employer);

        $data = [
            'name' => 'Full Time',
            'description' => 'Full-time employment',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/jobtype', $data);

        $response->assertStatus(201);
    }

    /** @test */
    public function candidate_cannot_create_jobtype(): void
    {
        Sanctum::actingAs($this->candidate);

        $data = [
            'name' => 'Full Time',
            'description' => 'Full-time employment',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/jobtype', $data);

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_jobtype(): void
    {
        $data = [
            'name' => 'Full Time',
            'description' => 'Full-time employment',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/jobtype', $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_update_jobtype(): void
    {
        Sanctum::actingAs($this->admin);

        $jobtype = JobType::factory()->create();
        $data = [
            'name' => 'Updated Full Time',
            'description' => 'Full-time employment',
            'is_active' => true,
        ];

        $response = $this->putJson('/api/jobtype/'.$jobtype->id, $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('job_types', [
            'id' => $jobtype->id,
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function admin_can_delete_jobtype(): void
    {
        Sanctum::actingAs($this->admin);

        $jobtype = JobType::factory()->create();

        $response = $this->deleteJson('/api/jobtype/'.$jobtype->id);

        $response->assertStatus(200);

        $this->assertSoftDeleted('job_types', [
            'id' => $jobtype->id,
        ]);
    }

    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs($this->admin);

        $data = [
            'name' => '', // Empty name should fail
        ];

        $response = $this->postJson('/api/jobtype', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function validation_fails_with_duplicate_name(): void
    {
        Sanctum::actingAs($this->admin);

        $existing = JobType::factory()->create(['name' => 'Duplicate Name']);

        $data = [
            'name' => 'Full Time',
            'description' => 'Full-time employment',
            'is_active' => true,
        ];
        $data['name'] = 'Duplicate Name';

        $response = $this->postJson('/api/jobtype', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function can_list_jobtypes(): void
    {
        Sanctum::actingAs($this->admin);

        JobType::factory()->count(3)->create();

        $response = $this->getJson('/api/jobtype');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'description', 'is_active', 'id', 'created_at', 'updated_at'],
                ],
            ]);
    }

    /** @test */
    public function can_show_single_jobtype(): void
    {
        Sanctum::actingAs($this->admin);

        $jobtype = JobType::factory()->create();

        $response = $this->getJson('/api/jobtype/'.$jobtype->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['name', 'description', 'is_active', 'id', 'created_at', 'updated_at'],
            ]);
    }
}
