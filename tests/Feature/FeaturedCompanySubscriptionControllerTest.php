<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;

/**
 * Context7 Test for FeaturedCompanySubscriptionController
 * Implements Laravel 12 testing best practices with Context7 MCP patterns
 */
class FeaturedCompanySubscriptionControllerTest extends TestCase
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
            ->get(route('featuredcompanysubscription.index'));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test create form display
     */
    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('featuredcompanysubscription.create'));

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
            ->post(route('featuredcompanysubscription.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('featuredcompanysubscriptions', [
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
            ->post(route('featuredcompanysubscription.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Context7 Pattern: Test show functionality
     */
    public function test_show_displays_record(): void
    {
        $featuredcompanysubscription = FeaturedCompanySubscription::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('featuredcompanysubscription.show', $featuredcompanysubscription));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test edit form display
     */
    public function test_edit_displays_form(): void
    {
        $featuredcompanysubscription = FeaturedCompanySubscription::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('featuredcompanysubscription.edit', $featuredcompanysubscription));

        $response->assertStatus(200);
    }

    /**
     * Context7 Pattern: Test successful update operation
     */
    public function test_update_modifies_record(): void
    {
        $featuredcompanysubscription = FeaturedCompanySubscription::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('featuredcompanysubscription.update', $featuredcompanysubscription), $newData);

        $response->assertRedirect();
        $this->assertDatabaseHas('featuredcompanysubscriptions', [
            'id' => $featuredcompanysubscription->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Context7 Pattern: Test successful delete operation
     */
    public function test_destroy_deletes_record(): void
    {
        $featuredcompanysubscription = FeaturedCompanySubscription::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('featuredcompanysubscription.destroy', $featuredcompanysubscription));

        $response->assertRedirect();
        $this->assertSoftDeleted($featuredcompanysubscription);
    }

    /**
     * Context7 Pattern: Test authorization
     */
    public function test_unauthorized_access_is_prevented(): void
    {
        $response = $this->get(route('featuredcompanysubscription.index'));

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
            ->post(route('featuredcompanysubscription.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Context7 Pattern: Test unique validation
     */
    public function test_store_prevents_duplicate_names(): void
    {
        $existing = FeaturedCompanySubscription::factory()->create(['name' => 'Unique Name']);

        $data = [
            'name' => 'Unique Name',
            'email' => $this->faker->email,
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('featuredcompanysubscription.store'), $data);

        $response->assertSessionHasErrors(['name']);
    }
}
