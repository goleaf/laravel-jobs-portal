<?php

namespace Tests\Feature;

use App\Models\CompanySize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanySizeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        // Assuming roles are properly set up, make this user an admin
        $this->admin->assignRole('admin'); // This may need adjustment based on your role system
    }

    /** @test */
    public function it_can_display_company_sizes_index()
    {
        $this->actingAs($this->admin);
        
        $response = $this->get(route('companySize.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('company_sizes.index');
    }

    /** @test */
    public function it_can_store_a_company_size()
    {
        $this->actingAs($this->admin);
        
        $companySize = [
            'size' => '50-100 employees'
        ];
        
        $response = $this->postJson(route('companySize.store'), $companySize);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('company_sizes', $companySize);
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $this->actingAs($this->admin);
        
        $response = $this->postJson(route('companySize.store'), []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['size']);
    }

    /** @test */
    public function it_validates_unique_size_when_storing()
    {
        $this->actingAs($this->admin);
        
        $existingSize = CompanySize::factory()->create(['size' => 'Small Company']);
        
        $response = $this->postJson(route('companySize.store'), [
            'size' => 'Small Company'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['size']);
    }

    /** @test */
    public function it_can_edit_a_company_size()
    {
        $this->actingAs($this->admin);
        
        $companySize = CompanySize::factory()->create();
        
        $response = $this->getJson(route('companySize.edit', $companySize));
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $companySize->id,
                'size' => $companySize->size
            ]
        ]);
    }

    /** @test */
    public function it_can_update_a_company_size()
    {
        $this->actingAs($this->admin);
        
        $companySize = CompanySize::factory()->create();
        $updatedData = ['size' => 'Updated Company Size'];
        
        $response = $this->putJson(route('companySize.update', $companySize), $updatedData);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('company_sizes', [
            'id' => $companySize->id,
            'size' => 'Updated Company Size'
        ]);
    }

    /** @test */
    public function it_validates_unique_size_when_updating_excluding_self()
    {
        $this->actingAs($this->admin);
        
        $companySize1 = CompanySize::factory()->create(['size' => 'Small Company']);
        $companySize2 = CompanySize::factory()->create(['size' => 'Medium Company']);
        
        // Should fail - trying to update to existing name
        $response = $this->putJson(route('companySize.update', $companySize2), [
            'size' => 'Small Company'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['size']);
        
        // Should succeed - updating to same name (no change)
        $response = $this->putJson(route('companySize.update', $companySize1), [
            'size' => 'Small Company'
        ]);
        
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_delete_a_company_size()
    {
        $this->actingAs($this->admin);
        
        $companySize = CompanySize::factory()->create();
        
        $response = $this->deleteJson(route('companySize.destroy', $companySize));
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('company_sizes', ['id' => $companySize->id]);
    }

    /** @test */
    public function it_prevents_deletion_when_company_size_is_in_use()
    {
        $this->actingAs($this->admin);
        
        $companySize = CompanySize::factory()->create();
        
        // Create a company that uses this size (assuming Company model exists)
        // This test may need adjustment based on your actual model relationships
        \App\Models\Company::factory()->create(['company_size_id' => $companySize->id]);
        
        $response = $this->deleteJson(route('companySize.destroy', $companySize));
        
        $response->assertStatus(422); // or whatever error status you return
        $this->assertDatabaseHas('company_sizes', ['id' => $companySize->id]);
    }

    /** @test */
    public function unauthorized_users_cannot_access_company_size_routes()
    {
        $user = User::factory()->create(); // Regular user without admin role
        $this->actingAs($user);
        
        $companySize = CompanySize::factory()->create();
        
        $this->postJson(route('companySize.store'), ['size' => 'Test'])
             ->assertStatus(403);
             
        $this->getJson(route('companySize.edit', $companySize))
             ->assertStatus(403);
             
        $this->putJson(route('companySize.update', $companySize), ['size' => 'Test'])
             ->assertStatus(403);
             
        $this->deleteJson(route('companySize.destroy', $companySize))
             ->assertStatus(403);
    }

    /** @test */
    public function guests_cannot_access_company_size_routes()
    {
        $companySize = CompanySize::factory()->create();
        
        $this->postJson(route('companySize.store'), ['size' => 'Test'])
             ->assertStatus(401);
             
        $this->getJson(route('companySize.edit', $companySize))
             ->assertStatus(401);
             
        $this->putJson(route('companySize.update', $companySize), ['size' => 'Test'])
             ->assertStatus(401);
             
        $this->deleteJson(route('companySize.destroy', $companySize))
             ->assertStatus(401);
    }
} 