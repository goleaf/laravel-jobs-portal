<?php

namespace Tests\Feature\Job;

use Tests\TestCase;
use App\Models\JobShift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

/**
 * Enhanced Feature Test for JobShiftController
 * Comprehensive testing for JobShift controller functionality
 */
class JobShiftControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
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
    public function admin_can_create_jobshift(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'shift' => 'Day Shift',
            'description' => '9 AM to 5 PM',
            'is_active' => true,
            'size' => 'Standard',
        ];
        
        $response = $this->postJson('/api/jobshift', $data);
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['shift', 'description', 'is_active', 'size', 'id', 'created_at', 'updated_at']
                ]);
        
        $this->assertDatabaseHas('job_shifts', [
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function employer_can_create_jobshift(): void
    {
        Sanctum::actingAs($this->employer);
        
        $data = [
            'shift' => 'Day Shift',
            'description' => '9 AM to 5 PM',
            'is_active' => true,
            'size' => 'Standard',
        ];
        
        $response = $this->postJson('/api/jobshift', $data);
        
        $response->assertStatus(201);
    }
    
    /** @test */
    public function candidate_cannot_create_jobshift(): void
    {
        Sanctum::actingAs($this->candidate);
        
        $data = [
            'shift' => 'Day Shift',
            'description' => '9 AM to 5 PM',
            'is_active' => true,
            'size' => 'Standard',
        ];
        
        $response = $this->postJson('/api/jobshift', $data);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function unauthenticated_user_cannot_create_jobshift(): void
    {
        $data = [
            'shift' => 'Day Shift',
            'description' => '9 AM to 5 PM',
            'is_active' => true,
            'size' => 'Standard',
        ];
        
        $response = $this->postJson('/api/jobshift', $data);
        
        $response->assertStatus(401);
    }
    
    /** @test */
    public function admin_can_update_jobshift(): void
    {
        Sanctum::actingAs($this->admin);
        
        $jobshift = JobShift::factory()->create();
        $data = [
            'shift' => 'Day Shift',
            'description' => '9 AM to 5 PM',
            'is_active' => true,
            'size' => 'Standard',
        ];
        
        $response = $this->putJson('/api/jobshift/' . $jobshift->id, $data);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('job_shifts', [
            'id' => $jobshift->id,
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function admin_can_delete_jobshift(): void
    {
        Sanctum::actingAs($this->admin);
        
        $jobshift = JobShift::factory()->create();
        
        $response = $this->deleteJson('/api/jobshift/' . $jobshift->id);
        
        $response->assertStatus(200);
        
        $this->assertSoftDeleted('job_shifts', [
            'id' => $jobshift->id
        ]);
    }
    
    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => '', // Empty name should fail
        ];
        
        $response = $this->postJson('/api/jobshift', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function validation_fails_with_duplicate_name(): void
    {
        Sanctum::actingAs($this->admin);
        
        $existing = JobShift::factory()->create(['name' => 'Duplicate Name']);
        
        $data = [
            'shift' => 'Day Shift',
            'description' => '9 AM to 5 PM',
            'is_active' => true,
            'size' => 'Standard',
        ];
        $data['name'] = 'Duplicate Name';
        
        $response = $this->postJson('/api/jobshift', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function can_list_jobshifts(): void
    {
        Sanctum::actingAs($this->admin);
        
        JobShift::factory()->count(3)->create();
        
        $response = $this->getJson('/api/jobshift');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['shift', 'description', 'is_active', 'size', 'id', 'created_at', 'updated_at']
                    ]
                ]);
    }
    
    /** @test */
    public function can_show_single_jobshift(): void
    {
        Sanctum::actingAs($this->admin);
        
        $jobshift = JobShift::factory()->create();
        
        $response = $this->getJson('/api/jobshift/' . $jobshift->id);
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => ['shift', 'description', 'is_active', 'size', 'id', 'created_at', 'updated_at']
                ]);
    }
}