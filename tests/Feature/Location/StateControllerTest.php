<?php

namespace Tests\Feature\Location;

use Tests\TestCase;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

/**
 * Enhanced Feature Test for StateController
 * Comprehensive testing for State controller functionality
 */
class StateControllerTest extends TestCase
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
    public function admin_can_create_state(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => 'California',
            'code' => 'CA',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/state', $data);
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['name', 'country_id', 'code', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
        
        $this->assertDatabaseHas('states', [
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function employer_can_create_state(): void
    {
        Sanctum::actingAs($this->employer);
        
        $data = [
            'name' => 'California',
            'code' => 'CA',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/state', $data);
        
        $response->assertStatus(201);
    }
    
    /** @test */
    public function candidate_cannot_create_state(): void
    {
        Sanctum::actingAs($this->candidate);
        
        $data = [
            'name' => 'California',
            'code' => 'CA',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/state', $data);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function unauthenticated_user_cannot_create_state(): void
    {
        $data = [
            'name' => 'California',
            'code' => 'CA',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/state', $data);
        
        $response->assertStatus(401);
    }
    
    /** @test */
    public function admin_can_update_state(): void
    {
        Sanctum::actingAs($this->admin);
        
        $state = State::factory()->create();
        $data = [
            'name' => 'Updated California',
            'code' => 'CA',
            'is_active' => true,
        ];
        
        $response = $this->putJson('/api/state/' . $state->id, $data);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('states', [
            'id' => $state->id,
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function admin_can_delete_state(): void
    {
        Sanctum::actingAs($this->admin);
        
        $state = State::factory()->create();
        
        $response = $this->deleteJson('/api/state/' . $state->id);
        
        $response->assertStatus(200);
        
        $this->assertSoftDeleted('states', [
            'id' => $state->id
        ]);
    }
    
    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => '', // Empty name should fail
        ];
        
        $response = $this->postJson('/api/state', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function validation_fails_with_duplicate_name(): void
    {
        Sanctum::actingAs($this->admin);
        
        $existing = State::factory()->create(['name' => 'Duplicate Name']);
        
        $data = [
            'name' => 'California',
            'code' => 'CA',
            'is_active' => true,
        ];
        $data['name'] = 'Duplicate Name';
        
        $response = $this->postJson('/api/state', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function can_list_states(): void
    {
        Sanctum::actingAs($this->admin);
        
        State::factory()->count(3)->create();
        
        $response = $this->getJson('/api/state');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['name', 'country_id', 'code', 'is_active', 'id', 'created_at', 'updated_at']
                    ]
                ]);
    }
    
    /** @test */
    public function can_show_single_state(): void
    {
        Sanctum::actingAs($this->admin);
        
        $state = State::factory()->create();
        
        $response = $this->getJson('/api/state/' . $state->id);
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => ['name', 'country_id', 'code', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
    }
}