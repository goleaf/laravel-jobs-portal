<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function itCanDisplayIndexPage()
    {
        $response = $this->actingAs($this->user)->get('/job');

        $response->assertStatus(200);
        $response->assertViewIs('job.index');
    }

    /** @test */
    public function itCanCreateJob()
    {
        $data = Job::factory()->make()->toArray();

        $response = $this->actingAs($this->user)->post('/job', $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('jobs', array_slice($data, 0, 3));
    }

    /** @test */
    public function itCanUpdateJob()
    {
        $job = Job::factory()->create();
        $data = Job::factory()->make()->toArray();

        $response = $this->actingAs($this->user)->put('/job/{$job->id}', $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('jobs', ['id' => $job->id]);
    }

    /** @test */
    public function itCanDeleteJob()
    {
        $job = Job::factory()->create();

        $response = $this->actingAs($this->user)->delete('/job/{$job->id}');

        $response->assertStatus(302);
        $this->assertSoftDeleted($job);
    }
}
