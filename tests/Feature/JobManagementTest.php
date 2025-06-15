<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $employer;
    protected $candidate;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $this->employer = User::factory()->create(['is_employer' => true]);
        $this->candidate = User::factory()->create(['is_candidate' => true]);
    }

    /** @test */
    public function employerCanCreateJob()
    {
        $category = Category::factory()->create();

        $jobData = [
            'title' => 'Software Developer',
            'description' => 'Looking for a skilled developer',
            'category_id' => $category->id,
            'company_id' => $this->company->id,
            'salary_min' => 50000,
            'salary_max' => 80000,
            'location' => 'New York',
            'job_type' => 'full-time',
            'experience_level' => 'mid-level',
        ];

        $response = $this->actingAs($this->employer)->post('/jobs', $jobData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('jobs', ['title' => 'Software Developer']);
    }

    /** @test */
    public function candidateCanApplyForJob()
    {
        $job = Job::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->candidate)->post("/jobs/{$job->id}/apply");

        $response->assertStatus(302);
        $this->assertDatabaseHas('job_applications', [
            'job_id' => $job->id,
            'user_id' => $this->candidate->id,
        ]);
    }

    /** @test */
    public function jobsCanBeSearched()
    {
        Job::factory()->create(['title' => 'PHP Developer']);
        Job::factory()->create(['title' => 'JavaScript Developer']);

        $response = $this->get('/jobs?search=PHP');

        $response->assertStatus(200);
        $response->assertSee('PHP Developer');
        $response->assertDontSee('JavaScript Developer');
    }

    /** @test */
    public function jobsCanBeFilteredByCategory()
    {
        $techCategory = Category::factory()->create(['name' => 'Technology']);
        $marketingCategory = Category::factory()->create(['name' => 'Marketing']);

        Job::factory()->create(['category_id' => $techCategory->id]);
        Job::factory()->create(['category_id' => $marketingCategory->id]);

        $response = $this->get("/jobs?category={$techCategory->id}");

        $response->assertStatus(200);
    }
}
