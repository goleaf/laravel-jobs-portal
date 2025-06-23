<?php

namespace Tests\Feature\Location;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Enhanced Feature Test for CountryController
 * Comprehensive testing for Country controller functionality.
 *
 * @internal
 *
 * @coversNothing
 */
class CountryControllerTest extends TestCase
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
    public function adminCanCreateCountry(): void
    {
        Sanctum::actingAs($this->admin);

        $data = [
            'name' => 'United States',
            'code' => 'US',
            'phone_code' => '+1',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/country', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['name', 'code', 'phone_code', 'is_active', 'id', 'created_at', 'updated_at'],
            ])
        ;

        $this->assertDatabaseHas('countries', [
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function employerCanCreateCountry(): void
    {
        Sanctum::actingAs($this->employer);

        $data = [
            'name' => 'United States',
            'code' => 'US',
            'phone_code' => '+1',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/country', $data);

        $response->assertStatus(201);
    }

    /** @test */
    public function candidateCannotCreateCountry(): void
    {
        Sanctum::actingAs($this->candidate);

        $data = [
            'name' => 'United States',
            'code' => 'US',
            'phone_code' => '+1',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/country', $data);

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticatedUserCannotCreateCountry(): void
    {
        $data = [
            'name' => 'United States',
            'code' => 'US',
            'phone_code' => '+1',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/country', $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function adminCanUpdateCountry(): void
    {
        Sanctum::actingAs($this->admin);

        $country = Country::factory()->create();
        $data = [
            'name' => 'Updated United States',
            'code' => 'US',
            'phone_code' => '+1',
            'is_active' => true,
        ];

        $response = $this->putJson('/api/country/'.$country->id, $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function adminCanDeleteCountry(): void
    {
        Sanctum::actingAs($this->admin);

        $country = Country::factory()->create();

        $response = $this->deleteJson('/api/country/'.$country->id);

        $response->assertStatus(200);

        $this->assertSoftDeleted('countries', [
            'id' => $country->id,
        ]);
    }

    /** @test */
    public function validationFailsWithInvalidData(): void
    {
        Sanctum::actingAs($this->admin);

        $data = [
            'name' => '', // Empty name should fail
        ];

        $response = $this->postJson('/api/country', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
        ;
    }

    /** @test */
    public function validationFailsWithDuplicateName(): void
    {
        Sanctum::actingAs($this->admin);

        $existing = Country::factory()->create(['name' => 'Duplicate Name']);

        $data = [
            'name' => 'United States',
            'code' => 'US',
            'phone_code' => '+1',
            'is_active' => true,
        ];
        $data['name'] = 'Duplicate Name';

        $response = $this->postJson('/api/country', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
        ;
    }

    /** @test */
    public function canListCountrys(): void
    {
        Sanctum::actingAs($this->admin);

        Country::factory()->count(3)->create();

        $response = $this->getJson('/api/country');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'phone_code', 'is_active', 'id', 'created_at', 'updated_at'],
                ],
            ])
        ;
    }

    /** @test */
    public function canShowSingleCountry(): void
    {
        Sanctum::actingAs($this->admin);

        $country = Country::factory()->create();

        $response = $this->getJson('/api/country/'.$country->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['name', 'code', 'phone_code', 'is_active', 'id', 'created_at', 'updated_at'],
            ]);
    }
}
