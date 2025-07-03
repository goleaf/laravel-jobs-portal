<?php

namespace Tests\Feature\MasterData;

use App\Models\Industry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Enhanced Feature Test for IndustryController
 * Comprehensive testing for Industry controller functionality.
 *
 * @internal
 *
 * @coversNothing
 */
class IndustryControllerTest extends TestCase
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
    public function admin_can_create_industry(): void
    {
        Sanctum::actingAs($this->admin);

        $data = [
            'name' => 'Technology',
            'description' => 'Software and IT services',
            'is_active' => true,
            'size' => 'Large',
        ];

        $response = $this->postJson('/api/industry', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['name', 'description', 'is_active', 'size', 'id', 'created_at', 'updated_at'],
            ]);

        $this->assertDatabaseHas('industries', [
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function employer_can_create_industry(): void
    {
        Sanctum::actingAs($this->employer);

        $data = [
            'name' => 'Technology',
            'description' => 'Software and IT services',
            'is_active' => true,
            'size' => 'Large',
        ];

        $response = $this->postJson('/api/industry', $data);

        $response->assertStatus(201);
    }

    /** @test */
    public function candidate_cannot_create_industry(): void
    {
        Sanctum::actingAs($this->candidate);

        $data = [
            'name' => 'Technology',
            'description' => 'Software and IT services',
            'is_active' => true,
            'size' => 'Large',
        ];

        $response = $this->postJson('/api/industry', $data);

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_industry(): void
    {
        $data = [
            'name' => 'Technology',
            'description' => 'Software and IT services',
            'is_active' => true,
            'size' => 'Large',
        ];

        $response = $this->postJson('/api/industry', $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_update_industry(): void
    {
        Sanctum::actingAs($this->admin);

        $industry = Industry::factory()->create();
        $data = [
            'name' => 'Updated Technology',
            'description' => 'Software and IT services',
            'is_active' => true,
            'size' => 'Large',
        ];

        $response = $this->putJson('/api/industry/'.$industry->id, $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('industries', [
            'id' => $industry->id,
            'name' => $data['name'] ?? $data[array_key_first($data)],
        ]);
    }

    /** @test */
    public function admin_can_delete_industry(): void
    {
        Sanctum::actingAs($this->admin);

        $industry = Industry::factory()->create();

        $response = $this->deleteJson('/api/industry/'.$industry->id);

        $response->assertStatus(200);

        $this->assertSoftDeleted('industries', [
            'id' => $industry->id,
        ]);
    }

    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs($this->admin);

        $data = [
            'name' => '', // Empty name should fail
        ];

        $response = $this->postJson('/api/industry', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function validation_fails_with_duplicate_name(): void
    {
        Sanctum::actingAs($this->admin);

        $existing = Industry::factory()->create(['name' => 'Duplicate Name']);

        $data = [
            'name' => 'Technology',
            'description' => 'Software and IT services',
            'is_active' => true,
            'size' => 'Large',
        ];
        $data['name'] = 'Duplicate Name';

        $response = $this->postJson('/api/industry', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function can_list_industrys(): void
    {
        Sanctum::actingAs($this->admin);

        Industry::factory()->count(3)->create();

        $response = $this->getJson('/api/industry');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'description', 'is_active', 'size', 'id', 'created_at', 'updated_at'],
                ],
            ]);
    }

    /** @test */
    public function can_show_single_industry(): void
    {
        Sanctum::actingAs($this->admin);

        $industry = Industry::factory()->create();

        $response = $this->getJson('/api/industry/'.$industry->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['name', 'description', 'is_active', 'size', 'id', 'created_at', 'updated_at'],
            ]);
    }
}
