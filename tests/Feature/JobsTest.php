<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function jobs_list_page_can_be_rendered()
    {
        $response = $this->get(route('front.job.index'));

        $response->assertStatus(200);
        $response->assertViewIs('front_web.jobs.index');
    }

    /** @test */
    public function job_details_page_can_be_viewed()
    {
        $job = Job::factory()->create(['status' => Job::STATUS_ACTIVE]);

        $response = $this->get(route('front.job.details', $job->id));

        $response->assertStatus(200);
        $response->assertViewIs('front_web.jobs.job_details');
        $response->assertSee($job->job_title);
    }

    /** @test */
    public function inactive_job_cannot_be_viewed()
    {
        $job = Job::factory()->create(['status' => Job::STATUS_DRAFT]);

        $response = $this->get(route('front.job.details', $job->id));

        $response->assertStatus(404);
    }

    /** @test */
    public function jobs_can_be_filtered_by_search_term()
    {
        $job1 = Job::factory()->create([
            'job_title' => 'Senior PHP Developer',
            'status' => Job::STATUS_ACTIVE
        ]);
        
        $job2 = Job::factory()->create([
            'job_title' => 'Junior JavaScript Developer',
            'status' => Job::STATUS_ACTIVE
        ]);

        $response = $this->get(route('front.job.index', ['search' => 'PHP']));

        $response->assertStatus(200);
        $response->assertViewIs('front_web.jobs.index');
        $response->assertSee($job1->job_title);
        $response->assertDontSee($job2->job_title);
    }

    /** @test */
    public function company_owner_can_access_job_create_page()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)
            ->get(route('job.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function company_owner_can_create_a_job()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        
        $jobData = [
            'company_id' => $company->id,
            'job_title' => 'Test Job',
            'job_description' => 'This is a test job description',
            'job_type_id' => 1,
            'job_category_id' => 1,
            'currency_id' => 1,
            'salary_period_id' => 1,
            'job_expiry_date' => now()->addDays(30)->format('Y-m-d'),
            'no_preference' => '1'
        ];

        $response = $this->actingAs($user)
            ->post(route('job.store'), $jobData);

        $response->assertRedirect();
        $this->assertDatabaseHas('jobs', ['job_title' => 'Test Job']);
    }
} 