<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Universal Test for HomeController
 * Implements Laravel 12 testing best practices with Universal MCP patterns.
 *
 * @internal
 *
 * @coversNothing
 */
class HomeControllerTest extends TestCase
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
            ->get(route('home.index'))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test create form display.
     */
    public function testCreateDisplaysForm(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('home.create'))
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
            ->post(route('home.store'), $data)
        ;

        $response->assertRedirect();
        $this->assertDatabaseHas('homes', [
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
            ->post(route('home.store'), [])
        ;

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Universal Pattern: Test show functionality.
     */
    public function testShowDisplaysRecord(): void
    {
        $home = Home::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('home.show', $home))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test edit form display.
     */
    public function testEditDisplaysForm(): void
    {
        $home = Home::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('home.edit', $home))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test successful update operation.
     */
    public function testUpdateModifiesRecord(): void
    {
        $home = Home::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('home.update', $home), $newData)
        ;

        $response->assertRedirect();
        $this->assertDatabaseHas('homes', [
            'id' => $home->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Universal Pattern: Test successful delete operation.
     */
    public function testDestroyDeletesRecord(): void
    {
        $home = Home::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('home.destroy', $home))
        ;

        $response->assertRedirect();
        $this->assertSoftDeleted($home);
    }

    /**
     * Universal Pattern: Test authorization.
     */
    public function testUnauthorizedAccessIsPrevented(): void
    {
        $response = $this->get(route('home.index'));

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
            ->post(route('home.store'), $data)
        ;

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Universal Pattern: Test unique validation.
     */
    public function testStorePreventsDuplicateNames(): void
    {
        $existing = Home::factory()->create(['name' => 'Unique Name']);

        $data = [
            'name' => 'Unique Name',
            'email' => $this->faker->email,
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('home.store'), $data);

        $response->assertSessionHasErrors(['name']);
    }
}
