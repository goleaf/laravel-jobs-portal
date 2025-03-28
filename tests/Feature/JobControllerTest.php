<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $company;
    protected $user;
    protected $jobCategory;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create([
            'is_active' => true,
        ]);
        
        // Create a company
        $this->company = Company::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);
        
        // Create a job category
        $this->jobCategory = JobCategory::factory()->create();
    }

    /** @test */
    public function an_authenticated_employer_can_view_create_job_form()
    {
        $response = $this->actingAs($this->user)
                        ->get(route('jobs.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('jobs.create');
    }

    /** @test */
    public function an_authenticated_employer_can_create_a_job()
    {
        $jobData = [
            'title' => 'Senior PHP Developer',
            'company_id' => $this->company->id,
            'job_category_id' => $this->jobCategory->id,
            'description' => 'This is a test job description',
            'requirements' => 'PHP, Laravel, MySQL',
            'location' => 'Remote',
            'salary_min' => 80000,
            'salary_max' => 120000,
            'job_type' => 'Full-time',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($this->user)
                        ->post(route('jobs.store'), $jobData);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('jobs', [
            'title' => 'Senior PHP Developer',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function an_authenticated_employer_can_update_their_job()
    {
        // Create a job
        $job = Job::factory()->create([
            'company_id' => $this->company->id,
            'title' => 'Original Job Title',
            'status' => 'active',
        ]);
        
        $updateData = [
            'title' => 'Updated Job Title',
            'company_id' => $this->company->id,
            'job_category_id' => $this->jobCategory->id,
            'description' => 'This is an updated job description',
            'requirements' => 'PHP, Laravel, MySQL, Vue.js',
            'location' => 'New York',
            'salary_min' => 90000,
            'salary_max' => 130000,
            'job_type' => 'Full-time',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($this->user)
                        ->put(route('jobs.update', $job->id), $updateData);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Updated Job Title',
            'location' => 'New York',
        ]);
    }

    /** @test */
    public function an_authenticated_employer_can_delete_their_job()
    {
        // Create a job
        $job = Job::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);
        
        $response = $this->actingAs($this->user)
                        ->delete(route('jobs.destroy', $job->id));
        
        $response->assertRedirect();
        
        $this->assertDatabaseMissing('jobs', [
            'id' => $job->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function an_authenticated_employer_cannot_update_jobs_they_dont_own()
    {
        // Create another user and company
        $anotherUser = User::factory()->create();
        $anotherCompany = Company::factory()->create([
            'user_id' => $anotherUser->id,
        ]);
        
        // Create a job for the other company
        $job = Job::factory()->create([
            'company_id' => $anotherCompany->id,
            'title' => 'Job From Another Company',
            'status' => 'active',
        ]);
        
        $updateData = [
            'title' => 'Trying to Update Another Company Job',
            'company_id' => $this->company->id,
            'job_category_id' => $this->jobCategory->id,
        ];
        
        $response = $this->actingAs($this->user)
                        ->put(route('jobs.update', $job->id), $updateData);
        
        // Should get a 403 forbidden or redirect with error
        $this->assertTrue(
            $response->status() === 403 || 
            $response->isRedirect() && $response->baseResponse->getSession()->has('error')
        );
        
        // Original data should remain unchanged
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Job From Another Company',
        ]);
    }

    /** @test */
    public function guests_can_view_active_jobs()
    {
        // Create active job
        $activeJob = Job::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
            'title' => 'Active Public Job'
        ]);
        
        // Create inactive job
        $inactiveJob = Job::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'inactive',
            'title' => 'Inactive Private Job'
        ]);
        
        $response = $this->get(route('jobs.index'));
        
        $response->assertStatus(200);
        $response->assertSee($activeJob->title);
        $response->assertDontSee($inactiveJob->title);
    }

    /** @test */
    public function guests_can_view_job_details()
    {
        $job = Job::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
            'title' => 'Public Job Details',
            'description' => 'This job description should be visible to everyone',
        ]);
        
        $response = $this->get(route('jobs.show', $job->id));
        
        $response->assertStatus(200);
        $response->assertSee($job->title);
        $response->assertSee($job->description);
    }

    /** @test */
    public function users_can_search_for_jobs()
    {
        // Create jobs with different titles
        Job::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
            'title' => 'Frontend Developer',
            'location' => 'New York'
        ]);
        
        Job::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
            'title' => 'Backend Developer',
            'location' => 'Remote'
        ]);
        
        Job::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
            'title' => 'Full Stack Developer',
            'location' => 'San Francisco'
        ]);
        
        // Search by title
        $response = $this->get(route('jobs.index', ['search' => 'frontend']));
        $response->assertStatus(200);
        $response->assertSee('Frontend Developer');
        $response->assertDontSee('Backend Developer');
        
        // Search by location
        $response = $this->get(route('jobs.index', ['location' => 'Remote']));
        $response->assertStatus(200);
        $response->assertSee('Backend Developer');
        $response->assertDontSee('Frontend Developer');
    }
} 