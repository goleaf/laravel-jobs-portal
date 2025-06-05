<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;

/**
 * Context7 Test for ConfirmPasswordController
 * Implements Laravel 12 testing best practices with Context7 MCP patterns
 */
class ConfirmPasswordControllerTest extends TestCase
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
            ->get(route('confirmpassword.index'));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test create form display
     */
    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('confirmpassword.create'));

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
            ->post(route('confirmpassword.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('confirmpasswords', [
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
            ->post(route('confirmpassword.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Context7 Pattern: Test show functionality
     */
    public function test_show_displays_record(): void
    {
        $confirmpassword = ConfirmPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('confirmpassword.show', $confirmpassword));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test edit form display
     */
    public function test_edit_displays_form(): void
    {
        $confirmpassword = ConfirmPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('confirmpassword.edit', $confirmpassword));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test successful update operation
     */
    public function test_update_modifies_record(): void
    {
        $confirmpassword = ConfirmPassword::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('confirmpassword.update', $confirmpassword), $newData);

        $response->assertRedirect();
        $this->assertDatabaseHas('confirmpasswords', [
            'id' => $confirmpassword->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Context7 Pattern: Test successful delete operation
     */
    public function test_destroy_deletes_record(): void
    {
        $confirmpassword = ConfirmPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('confirmpassword.destroy', $confirmpassword));

        $response->assertRedirect();
        $this->assertSoftDeleted($confirmpassword);
    }

    /**
     * Context7 Pattern: Test authorization
     */
    public function test_unauthorized_access_is_prevented(): void
    {
        $response = $this->get(route('confirmpassword.index'));

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
            ->post(route('confirmpassword.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Context7 Pattern: Test unique validation
     */
    public function test_store_prevents_duplicate_names(): void
    {
        $existing = ConfirmPassword::factory()->create(['name' => 'Unique Name']);

        $data = [
            'name' => 'Unique Name',
            'email' => $this->faker->email,
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('confirmpassword.store'), $data);

        $response->assertSessionHasErrors(['name']);
    }
}
