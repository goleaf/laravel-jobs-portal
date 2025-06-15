<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanySize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CompanySizeControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        // Assuming roles are properly set up, make this user an admin
        $this->admin->assignRole('admin'); // This may need adjustment based on your role system
    }

    /** @test */
    public function itCanDisplayCompanySizesIndex()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('companySize.index'));

        $response->assertStatus(200);
        $response->assertViewIs('company_sizes.index');
    }

    /** @test */
    public function itCanStoreACompanySize()
    {
        $this->actingAs($this->admin);

        $companySize = [
            'size' => '50-100 employees',
        ];

        $response = $this->postJson(route('companySize.store'), $companySize);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('company_sizes', $companySize);
    }

    /** @test */
    public function itValidatesRequiredFieldsWhenStoring()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('companySize.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['size']);
    }

    /** @test */
    public function itValidatesUniqueSizeWhenStoring()
    {
        $this->actingAs($this->admin);

        $existingSize = CompanySize::factory()->create(['size' => 'Small Company']);

        $response = $this->postJson(route('companySize.store'), [
            'size' => 'Small Company',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['size']);
    }

    /** @test */
    public function itCanEditACompanySize()
    {
        $this->actingAs($this->admin);

        $companySize = CompanySize::factory()->create();

        $response = $this->getJson(route('companySize.edit', $companySize));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $companySize->id,
                'size' => $companySize->size,
            ],
        ]);
    }

    /** @test */
    public function itCanUpdateACompanySize()
    {
        $this->actingAs($this->admin);

        $companySize = CompanySize::factory()->create();
        $updatedData = ['size' => 'Updated Company Size'];

        $response = $this->putJson(route('companySize.update', $companySize), $updatedData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('company_sizes', [
            'id' => $companySize->id,
            'size' => 'Updated Company Size',
        ]);
    }

    /** @test */
    public function itValidatesUniqueSizeWhenUpdatingExcludingSelf()
    {
        $this->actingAs($this->admin);

        $companySize1 = CompanySize::factory()->create(['size' => 'Small Company']);
        $companySize2 = CompanySize::factory()->create(['size' => 'Medium Company']);

        // Should fail - trying to update to existing name
        $response = $this->putJson(route('companySize.update', $companySize2), [
            'size' => 'Small Company',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['size']);

        // Should succeed - updating to same name (no change)
        $response = $this->putJson(route('companySize.update', $companySize1), [
            'size' => 'Small Company',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function itCanDeleteACompanySize()
    {
        $this->actingAs($this->admin);

        $companySize = CompanySize::factory()->create();

        $response = $this->deleteJson(route('companySize.destroy', $companySize));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('company_sizes', ['id' => $companySize->id]);
    }

    /** @test */
    public function itPreventsDeletionWhenCompanySizeIsInUse()
    {
        $this->actingAs($this->admin);

        $companySize = CompanySize::factory()->create();

        // Create a company that uses this size (assuming Company model exists)
        // This test may need adjustment based on your actual model relationships
        Company::factory()->create(['company_size_id' => $companySize->id]);

        $response = $this->deleteJson(route('companySize.destroy', $companySize));

        $response->assertStatus(422); // or whatever error status you return
        $this->assertDatabaseHas('company_sizes', ['id' => $companySize->id]);
    }

    /** @test */
    public function unauthorizedUsersCannotAccessCompanySizeRoutes()
    {
        $user = User::factory()->create(); // Regular user without admin role
        $this->actingAs($user);

        $companySize = CompanySize::factory()->create();

        $this->postJson(route('companySize.store'), ['size' => 'Test'])
            ->assertStatus(403)
        ;

        $this->getJson(route('companySize.edit', $companySize))
            ->assertStatus(403)
        ;

        $this->putJson(route('companySize.update', $companySize), ['size' => 'Test'])
            ->assertStatus(403)
        ;

        $this->deleteJson(route('companySize.destroy', $companySize))
            ->assertStatus(403)
        ;
    }

    /** @test */
    public function guestsCannotAccessCompanySizeRoutes()
    {
        $companySize = CompanySize::factory()->create();

        $this->postJson(route('companySize.store'), ['size' => 'Test'])
            ->assertStatus(401)
        ;

        $this->getJson(route('companySize.edit', $companySize))
            ->assertStatus(401)
        ;

        $this->putJson(route('companySize.update', $companySize), ['size' => 'Test'])
            ->assertStatus(401)
        ;

        $this->deleteJson(route('companySize.destroy', $companySize))
            ->assertStatus(401);
    }
}
