<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_job()
    {
        $job = Job::factory()->create();
        $this->assertDatabaseHas('jobs', ['id' => $job->id]);
    }

    /** @test */
    public function a_job_belongs_to_a_company()
    {
        $company = Company::factory()->create();
        $job = Job::factory()->create(['company_id' => $company->id]);
        
        $this->assertInstanceOf(Company::class, $job->company);
        $this->assertEquals($company->id, $job->company->id);
    }

    /** @test */
    public function a_job_belongs_to_a_job_category()
    {
        $category = JobCategory::factory()->create();
        $job = Job::factory()->create(['job_category_id' => $category->id]);
        
        $this->assertInstanceOf(JobCategory::class, $job->jobCategory);
        $this->assertEquals($category->id, $job->jobCategory->id);
    }

    /** @test */
    public function it_can_filter_active_jobs()
    {
        $activeJob = Job::factory()->create(['status' => Job::STATUS_ACTIVE]);
        $draftJob = Job::factory()->create(['status' => Job::STATUS_DRAFT]);
        
        $activeJobs = Job::active()->get();
        
        $this->assertTrue($activeJobs->contains($activeJob));
        $this->assertFalse($activeJobs->contains($draftJob));
    }

    /** @test */
    public function it_can_filter_by_featured_jobs()
    {
        $featuredJob = Job::factory()->create(['is_featured' => true]);
        $regularJob = Job::factory()->create(['is_featured' => false]);
        
        $featuredJobs = Job::featured()->get();
        
        $this->assertTrue($featuredJobs->contains($featuredJob));
        $this->assertFalse($featuredJobs->contains($regularJob));
    }
    
    /** @test */
    public function it_can_calculate_remaining_days_for_a_job()
    {
        $expiryDate = now()->addDays(10);
        $job = Job::factory()->create(['job_expiry_date' => $expiryDate]);
        
        $this->assertEquals(10, $job->remainingDays());
    }
} 