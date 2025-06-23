<?php

namespace Tests\Feature\MasterData;

use App\Models\CareerLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Enhanced Feature Test for CareerLevelController
 * Comprehensive testing for CareerLevel controller functionality.
 *
 * @internal
 *
 * @coversNothing
 */
class CareerLevelControllerTest extends TestCase
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
    public function adminCanCreateCareerlevel(): void
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
                'data' => ['level_name', 'from_year', 'to_year', 'is_active', 'id', 'created_at', 'updated_at'],
            ])
        ;

        $this->assertDatabaseHas('career_levels', [
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function employerCanCreateCareerlevel(): void
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
    public function candidateCannotCreateCareerlevel(): void
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
    public function unauthenticatedUserCannotCreateCareerlevel(): void
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
    public function adminCanUpdateCareerlevel(): void
    {
        Sanctum::actingAs($this->admin);

        $careerlevel = CareerLevel::factory()->create();
        $data = [
            'level_name' => 'Senior Level',
            'from_year' => 5,
            'to_year' => 10,
            'is_active' => true,
        ];

        $response = $this->putJson('/api/careerlevel/'.$careerlevel->id, $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('career_levels', [
            'id' => $careerlevel->id,
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function adminCanDeleteCareerlevel(): void
    {
        Sanctum::actingAs($this->admin);

        $careerlevel = CareerLevel::factory()->create();

        $response = $this->deleteJson('/api/careerlevel/'.$careerlevel->id);

        $response->assertStatus(200);

        $this->assertSoftDeleted('career_levels', [
            'id' => $careerlevel->id,
        ]);
    }

    /** @test */
    public function validationFailsWithInvalidData(): void
    {
        Sanctum::actingAs($this->admin);

        $data = [
            'name' => '', // Empty name should fail
        ];

        $response = $this->postJson('/api/careerlevel', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
        ;
    }

    /** @test */
    public function validationFailsWithDuplicateName(): void
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
            ->assertJsonValidationErrors(['name'])
        ;
    }

    /** @test */
    public function canListCareerlevels(): void
    {
        Sanctum::actingAs($this->admin);

        CareerLevel::factory()->count(3)->create();

        $response = $this->getJson('/api/careerlevel');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['level_name', 'from_year', 'to_year', 'is_active', 'id', 'created_at', 'updated_at'],
                ],
            ])
        ;
    }

    /** @test */
    public function canShowSingleCareerlevel(): void
    {
        Sanctum::actingAs($this->admin);

        $careerlevel = CareerLevel::factory()->create();

        $response = $this->getJson('/api/careerlevel/'.$careerlevel->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['level_name', 'from_year', 'to_year', 'is_active', 'id', 'created_at', 'updated_at'],
            ]);
    }
}
