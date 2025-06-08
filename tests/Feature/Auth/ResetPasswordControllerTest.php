<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;

/**
 * Universal Test for ResetPasswordController
 * Implements Laravel 12 testing best practices with Universal MCP patterns
 */
class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Universal Pattern: Create test user with appropriate permissions
        $this->user = User::factory()->create();
    }

    /**
     * Universal Pattern: Test index functionality
     */
    public function test_index_displays_correctly(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('resetpassword.index'));

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test create form display
     */
    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('resetpassword.create'));

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test successful store operation
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
            ->post(route('resetpassword.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('resetpasswords', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Universal Pattern: Test validation errors
     */
    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('resetpassword.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Universal Pattern: Test show functionality
     */
    public function test_show_displays_record(): void
    {
        $resetpassword = ResetPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('resetpassword.show', $resetpassword));

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test edit form display
     */
    public function test_edit_displays_form(): void
    {
        $resetpassword = ResetPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('resetpassword.edit', $resetpassword));

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test successful update operation
     */
    public function test_update_modifies_record(): void
    {
        $resetpassword = ResetPassword::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('resetpassword.update', $resetpassword), $newData);

        $response->assertRedirect();
        $this->assertDatabaseHas('resetpasswords', [
            'id' => $resetpassword->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Universal Pattern: Test successful delete operation
     */
    public function test_destroy_deletes_record(): void
    {
        $resetpassword = ResetPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('resetpassword.destroy', $resetpassword));

        $response->assertRedirect();
        $this->assertSoftDeleted($resetpassword);
    }

    /**
     * Universal Pattern: Test authorization
     */
    public function test_unauthorized_access_is_prevented(): void
    {
        $response = $this->get(route('resetpassword.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Universal Pattern: Test with invalid data
     */
    public function test_store_with_invalid_email(): void
    {
        $data = [
            'name' => 'Test Name',
            'email' => 'invalid-email',
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('resetpassword.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Universal Pattern: Test unique validation
     */
    public function test_store_prevents_duplicate_names(): void
    {
        $existing = ResetPassword::factory()->create(['name' => 'Unique Name']);

        $data = [
            'name' => 'Unique Name',
            'email' => $this->faker->email,
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('resetpassword.store'), $data);

        $response->assertSessionHasErrors(['name']);
    }
}
