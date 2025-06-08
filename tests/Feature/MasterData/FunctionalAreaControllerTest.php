<?php

namespace Tests\Feature\MasterData;

use Tests\TestCase;
use App\Models\FunctionalArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

/**
 * Context7 Feature Test for FunctionalAreaController
 * Comprehensive testing for FunctionalArea controller functionality
 */
class FunctionalAreaControllerTest extends TestCase
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
    public function admin_can_create_functionalarea(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => 'Software Development',
            'description' => 'Programming and software engineering',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/functionalarea', $data);
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['name', 'description', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
        
        $this->assertDatabaseHas('functional_areas', [
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function employer_can_create_functionalarea(): void
    {
        Sanctum::actingAs($this->employer);
        
        $data = [
            'name' => 'Software Development',
            'description' => 'Programming and software engineering',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/functionalarea', $data);
        
        $response->assertStatus(201);
    }
    
    /** @test */
    public function candidate_cannot_create_functionalarea(): void
    {
        Sanctum::actingAs($this->candidate);
        
        $data = [
            'name' => 'Software Development',
            'description' => 'Programming and software engineering',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/functionalarea', $data);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function unauthenticated_user_cannot_create_functionalarea(): void
    {
        $data = [
            'name' => 'Software Development',
            'description' => 'Programming and software engineering',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/functionalarea', $data);
        
        $response->assertStatus(401);
    }
    
    /** @test */
    public function admin_can_update_functionalarea(): void
    {
        Sanctum::actingAs($this->admin);
        
        $functionalarea = FunctionalArea::factory()->create();
        $data = [
            'name' => 'Updated Software Development',
            'description' => 'Programming and software engineering',
            'is_active' => true,
        ];
        
        $response = $this->putJson('/api/functionalarea/' . $functionalarea->id, $data);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('functional_areas', [
            'id' => $functionalarea->id,
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function admin_can_delete_functionalarea(): void
    {
        Sanctum::actingAs($this->admin);
        
        $functionalarea = FunctionalArea::factory()->create();
        
        $response = $this->deleteJson('/api/functionalarea/' . $functionalarea->id);
        
        $response->assertStatus(200);
        
        $this->assertSoftDeleted('functional_areas', [
            'id' => $functionalarea->id
        ]);
    }
    
    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => '', // Empty name should fail
        ];
        
        $response = $this->postJson('/api/functionalarea', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function validation_fails_with_duplicate_name(): void
    {
        Sanctum::actingAs($this->admin);
        
        $existing = FunctionalArea::factory()->create(['name' => 'Duplicate Name']);
        
        $data = [
            'name' => 'Software Development',
            'description' => 'Programming and software engineering',
            'is_active' => true,
        ];
        $data['name'] = 'Duplicate Name';
        
        $response = $this->postJson('/api/functionalarea', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function can_list_functionalareas(): void
    {
        Sanctum::actingAs($this->admin);
        
        FunctionalArea::factory()->count(3)->create();
        
        $response = $this->getJson('/api/functionalarea');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['name', 'description', 'is_active', 'id', 'created_at', 'updated_at']
                    ]
                ]);
    }
    
    /** @test */
    public function can_show_single_functionalarea(): void
    {
        Sanctum::actingAs($this->admin);
        
        $functionalarea = FunctionalArea::factory()->create();
        
        $response = $this->getJson('/api/functionalarea/' . $functionalarea->id);
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => ['name', 'description', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
    }
}