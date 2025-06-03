<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\CareerLevel;
use App\Models\FunctionalArea;
use App\Models\SalaryCurrency;
use App\Models\SalaryPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user with company
        $this->user = User::factory()->create([
            'email' => 'employer@example.com',
            'owner_type' => 'Company',
            'owner_id' => 1,
        ]);
        
        $this->company = Company::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->user->update(['owner_id' => $this->company->id]);
    }

    /** @test */
    public function authenticated_employer_can_view_jobs_index()
    {
        $this->actingAs($this->user)
            ->get(route('employer.jobs.index'))
            ->assertStatus(200)
            ->assertViewIs('employer.jobs.index');
    }

    /** @test */
    public function authenticated_employer_can_create_job()
    {
        $jobCategory = JobCategory::factory()->create();
        $jobType = JobType::factory()->create();
        $careerLevel = CareerLevel::factory()->create();
        $functionalArea = FunctionalArea::factory()->create();
        $currency = SalaryCurrency::factory()->create();
        $salaryPeriod = SalaryPeriod::factory()->create();

        $jobData = [
            'job_title' => 'Senior Developer',
            'description' => 'Looking for a senior developer with 5+ years experience',
            'job_category_id' => $jobCategory->id,
            'job_type_id' => $jobType->id,
            'career_level_id' => $careerLevel->id,
            'functional_area_id' => $functionalArea->id,
            'currency_id' => $currency->id,
            'salary_period_id' => $salaryPeriod->id,
            'salary_from' => '5000',
            'salary_to' => '8000',
            'country' => 'United States',
            'state' => 'California',
            'city' => 'San Francisco',
            'job_expiry_date' => now()->addDays(30)->format('Y-m-d'),
            'no_preference' => 2,
            'hide_salary' => 0,
            'is_freelance' => 0,
            'experience' => 5,
        ];

        $this->actingAs($this->user)
            ->post(route('employer.jobs.store'), $jobData)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('jobs', [
            'job_title' => 'Senior Developer',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function authenticated_employer_can_view_own_job()
    {
        $job = Job::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('employer.jobs.show', $job))
            ->assertStatus(200)
            ->assertViewIs('employer.jobs.show')
            ->assertViewHas('job', $job);
    }

    /** @test */
    public function authenticated_employer_can_edit_own_job()
    {
        $job = Job::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('employer.jobs.edit', $job))
            ->assertStatus(200)
            ->assertViewIs('employer.jobs.edit')
            ->assertViewHas('job', $job);
    }

    /** @test */
    public function authenticated_employer_can_update_own_job()
    {
        $job = Job::factory()->create([
            'company_id' => $this->company->id,
            'job_title' => 'Original Title',
        ]);

        $updateData = [
            'job_title' => 'Updated Job Title',
            'description' => $job->description,
            'job_category_id' => $job->job_category_id,
            'job_type_id' => $job->job_type_id,
            'career_level_id' => $job->career_level_id,
            'functional_area_id' => $job->functional_area_id,
            'currency_id' => $job->currency_id,
            'salary_period_id' => $job->salary_period_id,
            'salary_from' => $job->salary_from,
            'salary_to' => $job->salary_to,
            'country' => $job->country,
            'state' => $job->state,
            'city' => $job->city,
            'job_expiry_date' => $job->job_expiry_date,
            'no_preference' => $job->no_preference,
            'hide_salary' => $job->hide_salary,
            'is_freelance' => $job->is_freelance,
            'experience' => $job->experience,
        ];

        $this->actingAs($this->user)
            ->put(route('employer.jobs.update', $job), $updateData)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'job_title' => 'Updated Job Title',
        ]);
    }

    /** @test */
    public function authenticated_employer_can_delete_own_job()
    {
        $job = Job::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAs($this->user)
            ->delete(route('employer.jobs.destroy', $job))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('jobs', [
            'id' => $job->id,
        ]);
    }

    /** @test */
    public function employer_cannot_access_other_company_jobs()
    {
        $otherUser = User::factory()->create();
        $otherCompany = Company::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        
        $otherJob = Job::factory()->create([
            'company_id' => $otherCompany->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('employer.jobs.edit', $otherJob))
            ->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_job_management()
    {
        $this->get(route('employer.jobs.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function job_creation_requires_valid_data()
    {
        $this->actingAs($this->user)
            ->post(route('employer.jobs.store'), [])
            ->assertSessionHasErrors([
                'job_title',
                'description',
                'job_category_id',
                'job_type_id',
            ]);
    }
} 