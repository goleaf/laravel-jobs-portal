<?php

namespace Tests\Feature\Api;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function itCanListJobsViaApi()
    {
        Job::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/jobs')
        ;

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'created_at'],
                ],
            ])
        ;
    }

    /** @test */
    public function itCanCreateJobViaApi()
    {
        $jobData = [
            'title' => 'API Test Job',
            'description' => 'Job created via API',
            'location' => 'Remote',
            'salary_min' => 40000,
            'salary_max' => 60000,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/jobs', $jobData)
        ;

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'API Test Job'])
        ;

        $this->assertDatabaseHas('jobs', ['title' => 'API Test Job']);
    }

    /** @test */
    public function itCanShowJobViaApi()
    {
        $job = Job::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/jobs/{$job->id}")
        ;

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $job->id])
        ;
    }

    /** @test */
    public function itRequiresAuthenticationForProtectedEndpoints()
    {
        $response = $this->postJson('/api/jobs', []);

        $response->assertStatus(401);
    }

    /** @test */
    public function itValidatesRequiredFields()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/jobs', [])
        ;

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description']);
    }
}
