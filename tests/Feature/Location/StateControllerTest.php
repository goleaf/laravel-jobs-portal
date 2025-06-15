<?php

namespace Tests\Feature\Location;

use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Enhanced Feature Test for StateController
 * Comprehensive testing for State controller functionality.
 *
 * @internal
 *
 * @coversNothing
 */
class StateControllerTest extends TestCase
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
    public function adminCanCreateState(): void
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
                'data' => ['name', 'country_id', 'code', 'is_active', 'id', 'created_at', 'updated_at'],
            ])
        ;

        $this->assertDatabaseHas('states', [
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function employerCanCreateState(): void
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
    public function candidateCannotCreateState(): void
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
    public function unauthenticatedUserCannotCreateState(): void
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
    public function adminCanUpdateState(): void
    {
        Sanctum::actingAs($this->admin);

        $state = State::factory()->create();
        $data = [
            'name' => 'Updated California',
            'code' => 'CA',
            'is_active' => true,
        ];

        $response = $this->putJson('/api/state/'.$state->id, $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('states', [
            'id' => $state->id,
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function adminCanDeleteState(): void
    {
        Sanctum::actingAs($this->admin);

        $state = State::factory()->create();

        $response = $this->deleteJson('/api/state/'.$state->id);

        $response->assertStatus(200);

        $this->assertSoftDeleted('states', [
            'id' => $state->id,
        ]);
    }

    /** @test */
    public function validationFailsWithInvalidData(): void
    {
        Sanctum::actingAs($this->admin);

        $data = [
            'name' => '', // Empty name should fail
        ];

        $response = $this->postJson('/api/state', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
        ;
    }

    /** @test */
    public function validationFailsWithDuplicateName(): void
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
            ->assertJsonValidationErrors(['name'])
        ;
    }

    /** @test */
    public function canListStates(): void
    {
        Sanctum::actingAs($this->admin);

        State::factory()->count(3)->create();

        $response = $this->getJson('/api/state');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'country_id', 'code', 'is_active', 'id', 'created_at', 'updated_at'],
                ],
            ])
        ;
    }

    /** @test */
    public function canShowSingleState(): void
    {
        Sanctum::actingAs($this->admin);

        $state = State::factory()->create();

        $response = $this->getJson('/api/state/'.$state->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['name', 'country_id', 'code', 'is_active', 'id', 'created_at', 'updated_at'],
            ]);
    }
}
