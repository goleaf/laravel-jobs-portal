<?php

namespace Tests\Feature\MasterData;

use Tests\TestCase;
use App\Models\CompanySize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

/**
 * Context7 Feature Test for CompanySizeController
 * Comprehensive testing for CompanySize controller functionality
 */
class CompanySizeControllerTest extends TestCase
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
    public function admin_can_create_companysize(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'size' => 'Medium',
            'from_range' => 51,
            'to_range' => 200,
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/companysize', $data);
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => ['size', 'from_range', 'to_range', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
        
        $this->assertDatabaseHas('company_sizes', [
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function employer_can_create_companysize(): void
    {
        Sanctum::actingAs($this->employer);
        
        $data = [
            'size' => 'Medium',
            'from_range' => 51,
            'to_range' => 200,
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/companysize', $data);
        
        $response->assertStatus(201);
    }
    
    /** @test */
    public function candidate_cannot_create_companysize(): void
    {
        Sanctum::actingAs($this->candidate);
        
        $data = [
            'size' => 'Medium',
            'from_range' => 51,
            'to_range' => 200,
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/companysize', $data);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function unauthenticated_user_cannot_create_companysize(): void
    {
        $data = [
            'size' => 'Medium',
            'from_range' => 51,
            'to_range' => 200,
            'is_active' => true,
        ];
        
        $response = $this->postJson('/api/companysize', $data);
        
        $response->assertStatus(401);
    }
    
    /** @test */
    public function admin_can_update_companysize(): void
    {
        Sanctum::actingAs($this->admin);
        
        $companysize = CompanySize::factory()->create();
        $data = [
            'size' => 'Medium',
            'from_range' => 51,
            'to_range' => 200,
            'is_active' => true,
        ];
        
        $response = $this->putJson('/api/companysize/' . $companysize->id, $data);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('company_sizes', [
            'id' => $companysize->id,
            'name' => $data['name'] ?? $data[array_key_first($data)]
        ]);
    }
    
    /** @test */
    public function admin_can_delete_companysize(): void
    {
        Sanctum::actingAs($this->admin);
        
        $companysize = CompanySize::factory()->create();
        
        $response = $this->deleteJson('/api/companysize/' . $companysize->id);
        
        $response->assertStatus(200);
        
        $this->assertSoftDeleted('company_sizes', [
            'id' => $companysize->id
        ]);
    }
    
    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs($this->admin);
        
        $data = [
            'name' => '', // Empty name should fail
        ];
        
        $response = $this->postJson('/api/companysize', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function validation_fails_with_duplicate_name(): void
    {
        Sanctum::actingAs($this->admin);
        
        $existing = CompanySize::factory()->create(['name' => 'Duplicate Name']);
        
        $data = [
            'size' => 'Medium',
            'from_range' => 51,
            'to_range' => 200,
            'is_active' => true,
        ];
        $data['name'] = 'Duplicate Name';
        
        $response = $this->postJson('/api/companysize', $data);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }
    
    /** @test */
    public function can_list_companysizes(): void
    {
        Sanctum::actingAs($this->admin);
        
        CompanySize::factory()->count(3)->create();
        
        $response = $this->getJson('/api/companysize');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['size', 'from_range', 'to_range', 'is_active', 'id', 'created_at', 'updated_at']
                    ]
                ]);
    }
    
    /** @test */
    public function can_show_single_companysize(): void
    {
        Sanctum::actingAs($this->admin);
        
        $companysize = CompanySize::factory()->create();
        
        $response = $this->getJson('/api/companysize/' . $companysize->id);
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => ['size', 'from_range', 'to_range', 'is_active', 'id', 'created_at', 'updated_at']
                ]);
    }
}