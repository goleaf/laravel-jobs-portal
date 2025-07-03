<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CandidateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_display_index_page()
    {
        $response = $this->actingAs($this->user)->get('/candidate');

        $response->assertStatus(200);
        $response->assertViewIs('candidate.index');
    }

    /** @test */
    public function it_can_create_candidate()
    {
        $data = Candidate::factory()->make()->toArray();

        $response = $this->actingAs($this->user)->post('/candidate', $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('candidates', array_slice($data, 0, 3));
    }

    /** @test */
    public function it_can_update_candidate()
    {
        $candidate = Candidate::factory()->create();
        $data = Candidate::factory()->make()->toArray();

        $response = $this->actingAs($this->user)->put('/candidate/{$candidate->id}', $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('candidates', ['id' => $candidate->id]);
    }

    /** @test */
    public function it_can_delete_candidate()
    {
        $candidate = Candidate::factory()->create();

        $response = $this->actingAs($this->user)->delete('/candidate/{$candidate->id}');

        $response->assertStatus(302);
        $this->assertSoftDeleted($candidate);
    }
}
