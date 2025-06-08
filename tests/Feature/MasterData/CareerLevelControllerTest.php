<?php

namespace Tests\Feature\MasterData;

use Tests\TestCase;
use App\Models\CareerLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

/**
 * Context7 Feature Test for CareerLevelController
 * Comprehensive testing for CareerLevel controller functionality
 */
class CareerLevelControllerTest extends TestCase
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
    public function admin_can_create_careerlevel(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'level_name' => 'Senior Level',
            'from_year' => 5,
            'to_year' => 10,
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/careerlevel', $data);
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['level_name', 'from_year', 'to_year', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
        
        $this->assertDatabaseHas('career_levels', [
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function employer_can_create_careerlevel(): void
    {
        Sanctum::actingAs($this->employer);
        
        $data = [
            'level_name' => 'Senior Level',
            'from_year' => 5,
            'to_year' => 10,
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/careerlevel', $data);
        
        $response->assertStatus(201);
    }
    
    /** @test */
    public function candidate_cannot_create_careerlevel(): void
    {
        Sanctum::actingAs($this->candidate);
        
        $data = [
            'level_name' => 'Senior Level',
            'from_year' => 5,
            'to_year' => 10,
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/careerlevel', $data);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function unauthenticated_user_cannot_create_careerlevel(): void
    {
        $data = [
            'level_name' => 'Senior Level',
            'from_year' => 5,
            'to_year' => 10,
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/careerlevel', $data);
        
        $response->assertStatus(401);
    }
    
    /** @test */
    public function admin_can_update_careerlevel(): void
    {
        Sanctum::actingAs($this->admin);
        
        $careerlevel = CareerLevel::factory()->create();
        $data = [
            'level_name' => 'Senior Level',
            'from_year' => 5,
            'to_year' => 10,
            'is_active' => true,
        ];
        
        $response = $this->putJson('/api/careerlevel/' . $careerlevel->id, $data);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('career_levels', [
            'id' => $careerlevel->id,
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function admin_can_delete_careerlevel(): void
    {
        Sanctum::actingAs($this->admin);
        
        $careerlevel = CareerLevel::factory()->create();
        
        $response = $this->deleteJson('/api/careerlevel/' . $careerlevel->id);
        
        $response->assertStatus(200);
        
        $this->assertSoftDeleted('career_levels', [
            'id' => $careerlevel->id
        ]);
    }
    
    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => '', // Empty name should fail
        ];
        
        $response = $this->postJson('/api/careerlevel', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function validation_fails_with_duplicate_name(): void
    {
        Sanctum::actingAs($this->admin);
        
        $existing = CareerLevel::factory()->create(['name' => 'Duplicate Name']);
        
        $data = [
            'level_name' => 'Senior Level',
            'from_year' => 5,
            'to_year' => 10,
            'is_active' => true,
        ];
        $data['name'] = 'Duplicate Name';
        
        $response = $this->postJson('/api/careerlevel', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function can_list_careerlevels(): void
    {
        Sanctum::actingAs($this->admin);
        
        CareerLevel::factory()->count(3)->create();
        
        $response = $this->getJson('/api/careerlevel');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['level_name', 'from_year', 'to_year', 'is_active', 'id', 'created_at', 'updated_at']
                    ]
                ]);
    }
    
    /** @test */
    public function can_show_single_careerlevel(): void
    {
        Sanctum::actingAs($this->admin);
        
        $careerlevel = CareerLevel::factory()->create();
        
        $response = $this->getJson('/api/careerlevel/' . $careerlevel->id);
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => ['level_name', 'from_year', 'to_year', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
    }
}