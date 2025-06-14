<?php

namespace Tests\Feature\Financial;

use Tests\TestCase;
use App\Models\SalaryCurrency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

/**
 * Enhanced Feature Test for SalaryCurrencyController
 * Comprehensive testing for SalaryCurrency controller functionality
 */
class SalaryCurrencyControllerTest extends TestCase
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
    public function admin_can_create_salarycurrency(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'currency_name' => 'US Dollar',
            'currency_code' => 'USD',
            'currency_icon' => '$',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/salarycurrency', $data);
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['currency_name', 'currency_code', 'currency_icon', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
        
        $this->assertDatabaseHas('salary_currencies', [
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function employer_can_create_salarycurrency(): void
    {
        Sanctum::actingAs($this->employer);
        
        $data = [
            'currency_name' => 'US Dollar',
            'currency_code' => 'USD',
            'currency_icon' => '$',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/salarycurrency', $data);
        
        $response->assertStatus(201);
    }
    
    /** @test */
    public function candidate_cannot_create_salarycurrency(): void
    {
        Sanctum::actingAs($this->candidate);
        
        $data = [
            'currency_name' => 'US Dollar',
            'currency_code' => 'USD',
            'currency_icon' => '$',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/salarycurrency', $data);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function unauthenticated_user_cannot_create_salarycurrency(): void
    {
        $data = [
            'currency_name' => 'US Dollar',
            'currency_code' => 'USD',
            'currency_icon' => '$',
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/salarycurrency', $data);
        
        $response->assertStatus(401);
    }
    
    /** @test */
    public function admin_can_update_salarycurrency(): void
    {
        Sanctum::actingAs($this->admin);
        
        $salarycurrency = SalaryCurrency::factory()->create();
        $data = [
            'currency_name' => 'US Dollar',
            'currency_code' => 'USD',
            'currency_icon' => '$',
            'is_active' => true,
        ];
        
        $response = $this->putJson('/api/salarycurrency/' . $salarycurrency->id, $data);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('salary_currencies', [
            'id' => $salarycurrency->id,
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function admin_can_delete_salarycurrency(): void
    {
        Sanctum::actingAs($this->admin);
        
        $salarycurrency = SalaryCurrency::factory()->create();
        
        $response = $this->deleteJson('/api/salarycurrency/' . $salarycurrency->id);
        
        $response->assertStatus(200);
        
        $this->assertSoftDeleted('salary_currencies', [
            'id' => $salarycurrency->id
        ]);
    }
    
    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => '', // Empty name should fail
        ];
        
        $response = $this->postJson('/api/salarycurrency', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function validation_fails_with_duplicate_name(): void
    {
        Sanctum::actingAs($this->admin);
        
        $existing = SalaryCurrency::factory()->create(['name' => 'Duplicate Name']);
        
        $data = [
            'currency_name' => 'US Dollar',
            'currency_code' => 'USD',
            'currency_icon' => '$',
            'is_active' => true,
        ];
        $data['name'] = 'Duplicate Name';
        
        $response = $this->postJson('/api/salarycurrency', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function can_list_salarycurrencys(): void
    {
        Sanctum::actingAs($this->admin);
        
        SalaryCurrency::factory()->count(3)->create();
        
        $response = $this->getJson('/api/salarycurrency');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['currency_name', 'currency_code', 'currency_icon', 'is_active', 'id', 'created_at', 'updated_at']
                    ]
                ]);
    }
    
    /** @test */
    public function can_show_single_salarycurrency(): void
    {
        Sanctum::actingAs($this->admin);
        
        $salarycurrency = SalaryCurrency::factory()->create();
        
        $response = $this->getJson('/api/salarycurrency/' . $salarycurrency->id);
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => ['currency_name', 'currency_code', 'currency_icon', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
    }
}