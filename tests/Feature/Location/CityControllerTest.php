<?php

namespace Tests\Feature\Location;

use Tests\TestCase;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

/**
 * Context7 Feature Test for CityController
 * Comprehensive testing for City controller functionality
 */
class CityControllerTest extends TestCase
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
    public function admin_can_create_city(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => 'Los Angeles',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/city', $data);
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['name', 'state_id', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
        
        $this->assertDatabaseHas('cities', [
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function employer_can_create_city(): void
    {
        Sanctum::actingAs($this->employer);
        
        $data = [
            'name' => 'Los Angeles',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/city', $data);
        
        $response->assertStatus(201);
    }
    
    /** @test */
    public function candidate_cannot_create_city(): void
    {
        Sanctum::actingAs($this->candidate);
        
        $data = [
            'name' => 'Los Angeles',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/city', $data);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function unauthenticated_user_cannot_create_city(): void
    {
        $data = [
            'name' => 'Los Angeles',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/city', $data);
        
        $response->assertStatus(401);
    }
    
    /** @test */
    public function admin_can_update_city(): void
    {
        Sanctum::actingAs($this->admin);
        
        $city = City::factory()->create();
        $data = [
            'name' => 'Updated Los Angeles',
            'is_active' => true,
        ];
        
        $response = $this->putJson('/api/city/' . $city->id, $data);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function admin_can_delete_city(): void
    {
        Sanctum::actingAs($this->admin);
        
        $city = City::factory()->create();
        
        $response = $this->deleteJson('/api/city/' . $city->id);
        
        $response->assertStatus(200);
        
        $this->assertSoftDeleted('cities', [
            'id' => $city->id
        ]);
    }
    
    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => '', // Empty name should fail
        ];
        
        $response = $this->postJson('/api/city', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function validation_fails_with_duplicate_name(): void
    {
        Sanctum::actingAs($this->admin);
        
        $existing = City::factory()->create(['name' => 'Duplicate Name']);
        
        $data = [
            'name' => 'Los Angeles',
            'is_active' => true,
        ];
        $data['name'] = 'Duplicate Name';
        
        $response = $this->postJson('/api/city', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function can_list_citys(): void
    {
        Sanctum::actingAs($this->admin);
        
        City::factory()->count(3)->create();
        
        $response = $this->getJson('/api/city');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['name', 'state_id', 'is_active', 'id', 'created_at', 'updated_at']
                    ]
                ]);
    }
    
    /** @test */
    public function can_show_single_city(): void
    {
        Sanctum::actingAs($this->admin);
        
        $city = City::factory()->create();
        
        $response = $this->getJson('/api/city/' . $city->id);
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => ['name', 'state_id', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
    }
}