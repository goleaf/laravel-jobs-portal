<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobShift;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $employerUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an employer user and associated company for testing
        $this->employerUser = User::factory()->create(['user_type' => User::EMPLOYER]);
        $this->company = Company::factory()->create(['user_id' => $this->employerUser->id]);

        // Link company to the user model if not automatically done by factory
        $this->employerUser->company()->save($this->company);
        $this->employerUser->load('company'); // Ensure the relation is loaded
    }

    /** @test */
    public function job_can_be_created()
    {
        $company = Company::factory()->create();
        $jobType = JobType::factory()->create();
        $jobCategory = JobCategory::factory()->create();

        $jobData = [
            'company_id' => $company->id,
            'job_title' => $this->faker->jobTitle,
            'job_type_id' => $jobType->id,
            'job_category_id' => $jobCategory->id,
            'position' => $this->faker->randomNumber(1),
            'no_preference' => true,
            'status' => Job::STATUS_OPEN,
            'description' => $this->faker->paragraph,
            'is_freelance' => false,
            'hide_salary' => false,
            'salary_from' => $this->faker->randomNumber(5),
            'salary_to' => $this->faker->randomNumber(5) + 10000,
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'is_featured' => false,
            'is_suspended' => false,
            'job_expiry_date' => now()->addDays(30),
        ];

        $job = Job::create($jobData);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($jobData['job_title'], $job->job_title);
        $this->assertEquals($jobData['company_id'], $job->company_id);
        $this->assertEquals($jobData['job_category_id'], $job->job_category_id);
        $this->assertEquals($jobData['status'], $job->status);
    }

    /** @test */
    public function job_can_be_updated()
    {
        $job = Job::factory()->create();

        $updatedData = [
            'job_title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraph,
            'status' => Job::STATUS_CLOSED,
        ];

        $job->update($updatedData);
        $job->refresh();

        $this->assertEquals($updatedData['job_title'], $job->job_title);
        $this->assertEquals($updatedData['description'], $job->description);
        $this->assertEquals($updatedData['status'], $job->status);
    }

    /** @test */
    public function job_can_be_filtered_by_status()
    {
        Job::factory()->count(3)->create(['status' => Job::STATUS_OPEN]);
        Job::factory()->count(2)->create(['status' => Job::STATUS_CLOSED]);

        $openJobs = Job::where('status', Job::STATUS_OPEN)->get();
        $closedJobs = Job::where('status', Job::STATUS_CLOSED)->get();

        $this->assertCount(3, $openJobs);
        $this->assertCount(2, $closedJobs);
    }

    /** @test */
    public function job_belongs_to_company()
    {
        $company = Company::factory()->create();
        $job = Job::factory()->create(['company_id' => $company->id]);

        $this->assertInstanceOf(Company::class, $job->company);
        $this->assertEquals($company->id, $job->company_id);
    }

    /** @test */
    public function job_belongs_to_job_type()
    {
        $jobType = JobType::factory()->create();
        $job = Job::factory()->create(['job_type_id' => $jobType->id]);

        $this->assertInstanceOf(JobType::class, $job->jobType);
        $this->assertEquals($jobType->id, $job->job_type_id);
    }

    /** @test */
    public function job_belongs_to_job_category()
    {
        $jobCategory = JobCategory::factory()->create();
        $job = Job::factory()->create(['job_category_id' => $jobCategory->id]);

        $this->assertInstanceOf(JobCategory::class, $job->jobCategory);
        $this->assertEquals($jobCategory->id, $job->job_category_id);
    }

    /** @test */
    public function job_can_be_featured()
    {
        Job::factory()->count(3)->create(['is_featured' => true]);
        Job::factory()->count(2)->create(['is_featured' => false]);

        $featuredJobs = Job::where('is_featured', true)->get();
        $nonFeaturedJobs = Job::where('is_featured', false)->get();

        $this->assertCount(3, $featuredJobs);
        $this->assertCount(2, $nonFeaturedJobs);
    }

    /** @test */
    public function jobs_can_be_filtered_by_expiry_date()
    {
        Job::factory()->count(2)->create(['job_expiry_date' => now()->subDay()]);
        Job::factory()->count(3)->create(['job_expiry_date' => now()->addDays(10)]);

        $expiredJobs = Job::where('job_expiry_date', '<', now())->get();
        $activeJobs = Job::where('job_expiry_date', '>=', now())->get();

        $this->assertCount(2, $expiredJobs);
        $this->assertCount(3, $activeJobs);
    }

    /** @test */
    public function guests_cannot_access_employer_jobs_section()
    {
        // Assuming routes are prefixed with /employer
        $this->get('/employer/jobs')->assertRedirect('/login');
        $this->get('/employer/jobs/create')->assertRedirect('/login');
        $this->post('/employer/jobs')->assertRedirect('/login');
        // Add checks for other actions (show, edit, update, destroy)
    }

    /** @test */
    public function non_employer_users_cannot_access_employer_jobs_section()
    {
        $candidateUser = User::factory()->create(['user_type' => User::CANDIDATE]);
        $adminUser = User::factory()->create(['user_type' => User::ADMIN]);

        // Test candidate access
        $this->actingAs($candidateUser)->get('/employer/jobs')->assertStatus(403); // Or appropriate redirect/error
        $this->actingAs($candidateUser)->get('/employer/jobs/create')->assertStatus(403);

        // Test admin access (assuming admins shouldn't manage employer jobs directly this way)
        $this->actingAs($adminUser)->get('/employer/jobs')->assertStatus(403); // Or appropriate redirect/error
    }

    /** @test */
    public function employer_can_view_their_jobs_list()
    {
        Job::factory()->count(3)->create(['company_id' => $this->company->id]);
        // Create a job for another company to ensure filtering
        Job::factory()->create();

        $response = $this->actingAs($this->employerUser)->get('/employer/jobs');

        $response->assertStatus(200);
        $response->assertViewIs('employer.jobs.index');
        // Optionally assert that only the employer's jobs are shown
    }

    /** @test */
    public function employer_can_view_create_job_form()
    {
        $response = $this->actingAs($this->employerUser)->get('/employer/jobs/create');

        $response->assertStatus(200);
        $response->assertViewIs('employer.jobs.create');
    }

    /** @test */
    public function employer_can_create_a_new_job()
    {
        $jobData = $this->getJobData();

        $response = $this->actingAs($this->employerUser)->post('/employer/jobs', $jobData);

        $response->assertRedirect('/employer/jobs'); // Assuming redirect to index
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('jobs', [
            'company_id' => $this->company->id,
            'job_title' => $jobData['job_title'],
            'status' => Job::STATUS_OPEN, // Assuming default is open
        ]);
    }

     /** @test */
    public function employer_can_create_a_draft_job()
    {
        $jobData = $this->getJobData();
        $jobData['saveAsDraft'] = 'true'; // Add flag to trigger draft save

        $response = $this->actingAs($this->employerUser)->post('/employer/jobs', $jobData);

        $response->assertRedirect('/employer/jobs');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('jobs', [
            'company_id' => $this->company->id,
            'job_title' => $jobData['job_title'],
            'status' => Job::STATUS_DRAFT,
        ]);
    }

    /** @test */
    public function create_job_requires_necessary_fields()
    {
        $response = $this->actingAs($this->employerUser)->post('/employer/jobs', []);

        // Assert validation errors for required fields based on CreateJobRequest
        $response->assertSessionHasErrors(['job_title', 'job_category_id', 'job_type_id', 'country_id', 'state_id', 'city_id', 'job_expiry_date', 'description']); // Add/remove fields as needed
    }

    /** @test */
    public function employer_can_view_their_job_details()
    {
        $job = Job::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->employerUser)->get("/employer/jobs/{$job->id}");

        $response->assertStatus(200);
        $response->assertViewIs('employer.jobs.show');
        $response->assertSee($job->job_title);
    }

    /** @test */
    public function employer_cannot_view_other_employers_job_details()
    {
        $otherJob = Job::factory()->create(); // Belongs to a different company

        $response = $this->actingAs($this->employerUser)->get("/employer/jobs/{$otherJob->id}");

        $response->assertStatus(404); // Or 403, depending on policy/controller logic
    }

    /** @test */
    public function employer_can_view_edit_job_form()
    {
        $job = Job::factory()->create(['company_id' => $this->company->id, 'status' => Job::STATUS_OPEN]);

        $response = $this->actingAs($this->employerUser)->get("/employer/jobs/{$job->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('employer.jobs.edit');
        $response->assertSee($job->job_title);
    }

    /** @test */
    public function employer_cannot_edit_other_employers_job()
    {
        $otherJob = Job::factory()->create(['status' => Job::STATUS_OPEN]);

        $response = $this->actingAs($this->employerUser)->get("/employer/jobs/{$otherJob->id}/edit");
        $response->assertStatus(404); // Or 403
    }

    /** @test */
    public function employer_can_update_their_job()
    {
        $job = Job::factory()->create(['company_id' => $this->company->id, 'status' => Job::STATUS_DRAFT]);
        $updateData = $this->getJobData();
        $updateData['job_title'] = 'Updated Job Title';
        $updateData['status'] = Job::STATUS_OPEN; // Update status

        $response = $this->actingAs($this->employerUser)->put("/employer/jobs/{$job->id}", $updateData);

        $response->assertRedirect('/employer/jobs');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'job_title' => 'Updated Job Title',
            'status' => Job::STATUS_OPEN,
        ]);
    }

    /** @test */
    public function employer_can_delete_their_job()
    {
        $job = Job::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->employerUser)->delete("/employer/jobs/{$job->id}");

        // Assuming JSON response based on controller method
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    /** @test */
    public function employer_cannot_delete_other_employers_job()
    {
        $otherJob = Job::factory()->create();

        $response = $this->actingAs($this->employerUser)->delete("/employer/jobs/{$otherJob->id}");

        // Assuming JSON response based on controller method
        $response->assertStatus(403); // Or 404 / error message in JSON
        $this->assertDatabaseHas('jobs', ['id' => $otherJob->id]);
    }

    /**
     * Helper function to get valid job data for tests.
     */
    protected function getJobData(): array
    {
        // Ensure related models exist
        $jobType = JobType::factory()->create();
        $jobCategory = JobCategory::factory()->create();
        $jobShift = JobShift::factory()->create();
        // Assume Country/State/City with ID 1 exist or create them
        if (!\App\Models\Country::find(1)) { \App\Models\Country::factory()->create(['id' => 1]); }
        if (!\App\Models\State::find(1)) { \App\Models\State::factory()->create(['id' => 1, 'country_id' => 1]); }
        if (!\App\Models\City::find(1)) { \App\Models\City::factory()->create(['id' => 1, 'state_id' => 1]); }

        return [
            'job_title' => $this->faker->jobTitle,
            'job_type_id' => $jobType->id,
            'job_category_id' => $jobCategory->id,
            'job_shift_id' => $jobShift->id, // Added job shift
            'currency_id' => \App\Models\SalaryCurrency::factory()->create()->id,
            'salary_period_id' => \App\Models\SalaryPeriod::factory()->create()->id,
            'functional_area_id' => \App\Models\FunctionalArea::factory()->create()->id,
            'career_level_id' => \App\Models\CareerLevel::factory()->create()->id,
            'degree_level_id' => \App\Models\RequiredDegreeLevel::factory()->create()->id,
            'position' => $this->faker->randomNumber(1),
            'description' => $this->faker->paragraph,
            'salary_from' => $this->faker->randomNumber(5),
            'salary_to' => $this->faker->randomNumber(5) + 10000,
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'job_expiry_date' => now()->addDays(30)->toDateString(),
            'no_preference' => 0, // Example default
            'hide_salary' => false,
            'is_freelance' => false,
            // Add any other required fields from CreateJobRequest/UpdateJobRequest
             'skills' => implode(',', \App\Models\Skill::factory()->count(3)->create()->pluck('name')->toArray()), // Example skills
             'tag_ids' => \App\Models\Tag::factory()->count(2)->create()->pluck('id')->toArray(), // Example tags
        ];
    }
}
