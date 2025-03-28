<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Company;
use App\Models\JobType;
use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
} 