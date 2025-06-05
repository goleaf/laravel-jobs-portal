<?php

namespace Tests\Feature\Candidates;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;

/**
 * Context7 Test for DashboardController
 * Implements Laravel 12 testing best practices with Context7 MCP patterns
 */
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Context7 Pattern: Create test user with appropriate permissions
        $this->user = User::factory()->create();
    }

    /**
     * Context7 Pattern: Test index functionality
     */
    public function test_index_displays_correctly(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test create form display
     */
    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.create'));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test successful store operation
     */
    public function test_store_creates_new_record(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'description' => $this->faker->sentence,
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('dashboard.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('dashboards', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Context7 Pattern: Test validation errors
     */
    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('dashboard.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Context7 Pattern: Test show functionality
     */
    public function test_show_displays_record(): void
    {
        $dashboard = Dashboard::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.show', $dashboard));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test edit form display
     */
    public function test_edit_displays_form(): void
    {
        $dashboard = Dashboard::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.edit', $dashboard));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test successful update operation
     */
    public function test_update_modifies_record(): void
    {
        $dashboard = Dashboard::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('dashboard.update', $dashboard), $newData);

        $response->assertRedirect();
        $this->assertDatabaseHas('dashboards', [
            'id' => $dashboard->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Context7 Pattern: Test successful delete operation
     */
    public function test_destroy_deletes_record(): void
    {
        $dashboard = Dashboard::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('dashboard.destroy', $dashboard));

        $response->assertRedirect();
        $this->assertSoftDeleted($dashboard);
    }

    /**
     * Context7 Pattern: Test authorization
     */
    public function test_unauthorized_access_is_prevented(): void
    {
        $response = $this->get(route('dashboard.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Context7 Pattern: Test with invalid data
     */
    public function test_store_with_invalid_email(): void
    {
        $data = [
            'name' => 'Test Name',
            'email' => 'invalid-email',
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('dashboard.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Context7 Pattern: Test unique validation
     */
    public function test_store_prevents_duplicate_names(): void
    {
        $existing = Dashboard::factory()->create(['name' => 'Unique Name']);

        $data = [
            'name' => 'Unique Name',
            'email' => $this->faker->email,
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('dashboard.store'), $data);

        $response->assertSessionHasErrors(['name']);
    }
}
