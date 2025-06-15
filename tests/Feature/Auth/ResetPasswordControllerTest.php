<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Universal Test for ResetPasswordController
 * Implements Laravel 12 testing best practices with Universal MCP patterns.
 *
 * @internal
 *
 * @coversNothing
 */
class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Universal Pattern: Create test user with appropriate permissions
        $this->user = User::factory()->create();
    }

    /**
     * Universal Pattern: Test index functionality.
     */
    public function testIndexDisplaysCorrectly(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('resetpassword.index'))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test create form display.
     */
    public function testCreateDisplaysForm(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('resetpassword.create'))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test successful store operation.
     */
    public function testStoreCreatesNewRecord(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'description' => $this->faker->sentence,
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('resetpassword.store'), $data)
        ;

        $response->assertRedirect();
        $this->assertDatabaseHas('resetpasswords', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Universal Pattern: Test validation errors.
     */
    public function testStoreValidatesRequiredFields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('resetpassword.store'), [])
        ;

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Universal Pattern: Test show functionality.
     */
    public function testShowDisplaysRecord(): void
    {
        $resetpassword = ResetPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('resetpassword.show', $resetpassword))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test edit form display.
     */
    public function testEditDisplaysForm(): void
    {
        $resetpassword = ResetPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('resetpassword.edit', $resetpassword))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test successful update operation.
     */
    public function testUpdateModifiesRecord(): void
    {
        $resetpassword = ResetPassword::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('resetpassword.update', $resetpassword), $newData)
        ;

        $response->assertRedirect();
        $this->assertDatabaseHas('resetpasswords', [
            'id' => $resetpassword->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Universal Pattern: Test successful delete operation.
     */
    public function testDestroyDeletesRecord(): void
    {
        $resetpassword = ResetPassword::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('resetpassword.destroy', $resetpassword))
        ;

        $response->assertRedirect();
        $this->assertSoftDeleted($resetpassword);
    }

    /**
     * Universal Pattern: Test authorization.
     */
    public function testUnauthorizedAccessIsPrevented(): void
    {
        $response = $this->get(route('resetpassword.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Universal Pattern: Test with invalid data.
     */
    public function testStoreWithInvalidEmail(): void
    {
        $data = [
            'name' => 'Test Name',
            'email' => 'invalid-email',
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('resetpassword.store'), $data)
        ;

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Universal Pattern: Test unique validation.
     */
    public function testStorePreventsDuplicateNames(): void
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
