<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;

/**
 * Universal Test for ForgotPasswordController
 * Implements Laravel 12 testing best practices with Universal MCP patterns
 */
class ForgotPasswordControllerTest extends TestCase
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
            ->get(route('forgotpassword.index'));

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test create form display
     */
    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('forgotpassword.create'));

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
            ->post(route('forgotpassword.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('forgotpasswords', [
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
            ->post(route('forgotpassword.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Universal Pattern: Test show functionality
     */
    public function test_show_displays_record(): void
    {
        $forgotpassword = ForgotPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('forgotpassword.show', $forgotpassword));

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test edit form display
     */
    public function test_edit_displays_form(): void
    {
        $forgotpassword = ForgotPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('forgotpassword.edit', $forgotpassword));

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test successful update operation
     */
    public function test_update_modifies_record(): void
    {
        $forgotpassword = ForgotPassword::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('forgotpassword.update', $forgotpassword), $newData);

        $response->assertRedirect();
        $this->assertDatabaseHas('forgotpasswords', [
            'id' => $forgotpassword->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Universal Pattern: Test successful delete operation
     */
    public function test_destroy_deletes_record(): void
    {
        $forgotpassword = ForgotPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('forgotpassword.destroy', $forgotpassword));

        $response->assertRedirect();
        $this->assertSoftDeleted($forgotpassword);
    }

    /**
     * Universal Pattern: Test authorization
     */
    public function test_unauthorized_access_is_prevented(): void
    {
        $response = $this->get(route('forgotpassword.index'));

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
            ->post(route('forgotpassword.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Universal Pattern: Test unique validation
     */
    public function test_store_prevents_duplicate_names(): void
    {
        $existing = ForgotPassword::factory()->create(['name' => 'Unique Name']);

        $data = [
            'name' => 'Unique Name',
            'email' => $this->faker->email,
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('forgotpassword.store'), $data);

        $response->assertSessionHasErrors(['name']);
    }
}
