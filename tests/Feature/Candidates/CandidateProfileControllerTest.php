<?php

namespace Tests\Feature\Candidates;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Universal Test for CandidateProfileController
 * Implements Laravel 12 testing best practices with Universal MCP patterns.
 *
 * @internal
 *
 * @coversNothing
 */
class CandidateProfileControllerTest extends TestCase
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
            ->get(route('candidateprofile.index'))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test create form display.
     */
    public function testCreateDisplaysForm(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('candidateprofile.create'))
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
            ->post(route('candidateprofile.store'), $data)
        ;

        $response->assertRedirect();
        $this->assertDatabaseHas('candidateprofiles', [
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
            ->post(route('candidateprofile.store'), [])
        ;

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Universal Pattern: Test show functionality.
     */
    public function testShowDisplaysRecord(): void
    {
        $candidateprofile = CandidateProfile::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('candidateprofile.show', $candidateprofile))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test edit form display.
     */
    public function testEditDisplaysForm(): void
    {
        $candidateprofile = CandidateProfile::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('candidateprofile.edit', $candidateprofile))
        ;

        $response->assertStatus(200);
    }

    /**
     * Universal Pattern: Test successful update operation.
     */
    public function testUpdateModifiesRecord(): void
    {
        $candidateprofile = CandidateProfile::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('candidateprofile.update', $candidateprofile), $newData)
        ;

        $response->assertRedirect();
        $this->assertDatabaseHas('candidateprofiles', [
            'id' => $candidateprofile->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Universal Pattern: Test successful delete operation.
     */
    public function testDestroyDeletesRecord(): void
    {
        $candidateprofile = CandidateProfile::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('candidateprofile.destroy', $candidateprofile))
        ;

        $response->assertRedirect();
        $this->assertSoftDeleted($candidateprofile);
    }

    /**
     * Universal Pattern: Test authorization.
     */
    public function testUnauthorizedAccessIsPrevented(): void
    {
        $response = $this->get(route('candidateprofile.index'));

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
            ->post(route('candidateprofile.store'), $data)
        ;

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Universal Pattern: Test unique validation.
     */
    public function testStorePreventsDuplicateNames(): void
    {
        $existing = CandidateProfile::factory()->create(['name' => 'Unique Name']);

        $data = [
            'name' => 'Unique Name',
            'email' => $this->faker->email,
            'status' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('candidateprofile.store'), $data);

        $response->assertSessionHasErrors(['name']);
    }
}
