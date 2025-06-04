<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Services\JobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class JobServiceTest extends TestCase
{
    use RefreshDatabase;

    protected JobService $jobService;
    protected User $employer;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->jobService = new JobService();
        
        // Create test data
        $this->employer = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $this->employer->id]);
        $this->employer->update(['company_id' => $this->company->id]);
    }

    public function test_can_get_active_jobs(): void
    {
        // Create test jobs
        Job::factory()->count(5)->create(['status' => Job::STATUS_OPEN]);
        Job::factory()->count(3)->create(['status' => Job::STATUS_DRAFT]);

        $activeJobs = $this->jobService->getActiveJobs();

        $this->assertCount(5, $activeJobs);
        $this->assertEquals(5, $activeJobs->total());
    }

    public function test_can_get_featured_jobs(): void
    {
        // Create regular jobs
        Job::factory()->count(3)->create(['status' => Job::STATUS_OPEN]);
        
        // Create featured jobs
        $featuredJobs = Job::factory()->count(2)->create(['status' => Job::STATUS_OPEN]);
        
        foreach ($featuredJobs as $job) {
            $job->featured()->create([
                'featured_start_date' => now(),
                'featured_end_date' => now()->addDays(30),
                'is_active' => true,
            ]);
        }

        $result = $this->jobService->getFeaturedJobs();

        $this->assertCount(2, $result);
    }

    public function test_can_search_jobs_with_filters(): void
    {
        $category = JobCategory::factory()->create();
        $jobType = JobType::factory()->create();

        // Create jobs with specific attributes
        Job::factory()->create([
            'job_title' => 'PHP Developer',
            'status' => Job::STATUS_OPEN,
            'job_category_id' => $category->id,
            'job_type_id' => $jobType->id,
        ]);

        Job::factory()->create([
            'job_title' => 'Java Developer',
            'status' => Job::STATUS_OPEN,
        ]);

        $filters = [
            'keyword' => 'PHP',
            'category_id' => $category->id,
            'job_type_id' => $jobType->id,
        ];

        $results = $this->jobService->searchJobs($filters);

        $this->assertEquals(1, $results->total());
        $this->assertStringContainsString('PHP', $results->first()->job_title);
    }

    public function test_can_create_job(): void
    {
        $jobData = [
            'job_title' => 'Senior Laravel Developer',
            'description' => 'We are looking for an experienced Laravel developer...',
            'company_id' => $this->company->id,
            'job_category_id' => JobCategory::factory()->create()->id,
            'job_type_id' => JobType::factory()->create()->id,
            'status' => Job::STATUS_OPEN,
        ];

        $job = $this->jobService->createJob($jobData);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals('Senior Laravel Developer', $job->job_title);
        $this->assertEquals($this->company->id, $job->company_id);
        $this->assertDatabaseHas('jobs', [
            'job_title' => 'Senior Laravel Developer',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_update_job(): void
    {
        $job = Job::factory()->create([
            'job_title' => 'Original Title',
            'company_id' => $this->company->id,
        ]);

        $updateData = [
            'job_title' => 'Updated Title',
            'description' => 'Updated description',
        ];

        $updatedJob = $this->jobService->updateJob($job, $updateData);

        $this->assertEquals('Updated Title', $updatedJob->job_title);
        $this->assertEquals('Updated description', $updatedJob->description);
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'job_title' => 'Updated Title',
        ]);
    }

    public function test_can_delete_job(): void
    {
        $job = Job::factory()->create(['company_id' => $this->company->id]);

        $result = $this->jobService->deleteJob($job);

        $this->assertTrue($result);
        $this->assertSoftDeleted('jobs', ['id' => $job->id]);
    }

    public function test_can_get_job_statistics(): void
    {
        // Create various jobs
        Job::factory()->count(5)->create(['status' => Job::STATUS_OPEN]);
        Job::factory()->count(2)->create(['status' => Job::STATUS_DRAFT]);
        Job::factory()->count(1)->create(['status' => Job::STATUS_CLOSED]);

        $stats = $this->jobService->getJobStatistics();

        $this->assertEquals(8, $stats['total_jobs']);
        $this->assertEquals(5, $stats['active_jobs']);
        $this->assertEquals(2, $stats['draft_jobs']);
    }

    public function test_can_get_popular_jobs(): void
    {
        $popularJob = Job::factory()->create(['status' => Job::STATUS_OPEN]);
        $regularJob = Job::factory()->create(['status' => Job::STATUS_OPEN]);

        // Create applications for popular job
        for ($i = 0; $i < 15; $i++) {
            $popularJob->appliedJobs()->create([
                'candidate_id' => User::factory()->create()->id,
                'expected_salary' => 50000,
                'status' => 1,
            ]);
        }

        // Create fewer applications for regular job
        for ($i = 0; $i < 5; $i++) {
            $regularJob->appliedJobs()->create([
                'candidate_id' => User::factory()->create()->id,
                'expected_salary' => 45000,
                'status' => 1,
            ]);
        }

        $popularJobs = $this->jobService->getPopularJobs();

        $this->assertCount(1, $popularJobs);
        $this->assertEquals($popularJob->id, $popularJobs->first()->id);
    }

    public function test_can_get_similar_jobs(): void
    {
        $category = JobCategory::factory()->create();
        $jobType = JobType::factory()->create();

        $originalJob = Job::factory()->create([
            'job_category_id' => $category->id,
            'job_type_id' => $jobType->id,
            'status' => Job::STATUS_OPEN,
        ]);

        $similarJob1 = Job::factory()->create([
            'job_category_id' => $category->id,
            'status' => Job::STATUS_OPEN,
        ]);

        $similarJob2 = Job::factory()->create([
            'job_type_id' => $jobType->id,
            'status' => Job::STATUS_OPEN,
        ]);

        $unrelatedJob = Job::factory()->create(['status' => Job::STATUS_OPEN]);

        $similarJobs = $this->jobService->getSimilarJobs($originalJob);

        $this->assertCount(2, $similarJobs);
        $this->assertTrue($similarJobs->contains('id', $similarJob1->id));
        $this->assertTrue($similarJobs->contains('id', $similarJob2->id));
        $this->assertFalse($similarJobs->contains('id', $unrelatedJob->id));
    }

    public function test_can_expire_old_jobs(): void
    {
        // Create jobs with different expiry dates
        Job::factory()->create([
            'job_expiry_date' => now()->subDays(5),
            'status' => Job::STATUS_OPEN,
        ]);

        Job::factory()->create([
            'job_expiry_date' => now()->subDays(10),
            'status' => Job::STATUS_OPEN,
        ]);

        Job::factory()->create([
            'job_expiry_date' => now()->addDays(5),
            'status' => Job::STATUS_OPEN,
        ]);

        $expiredCount = $this->jobService->expireOldJobs();

        $this->assertEquals(2, $expiredCount);
        $this->assertEquals(1, Job::where('status', Job::STATUS_OPEN)->count());
        $this->assertEquals(2, Job::where('status', Job::STATUS_CLOSED)->count());
    }

    public function test_caching_works_correctly(): void
    {
        Cache::flush();

        Job::factory()->count(3)->create(['status' => Job::STATUS_OPEN]);

        // First call should cache the result
        $firstResult = $this->jobService->getActiveJobs();
        
        // Second call should use cache
        $secondResult = $this->jobService->getActiveJobs();

        $this->assertEquals($firstResult->total(), $secondResult->total());

        // Create a new job
        Job::factory()->create(['status' => Job::STATUS_OPEN]);

        // Cache should be cleared and reflect new data
        $thirdResult = $this->jobService->getActiveJobs();
        $this->assertEquals(4, $thirdResult->total());
    }

    public function test_can_get_jobs_expiring_soon(): void
    {
        // Create jobs expiring soon
        Job::factory()->create([
            'job_expiry_date' => now()->addDays(3),
            'status' => Job::STATUS_OPEN,
        ]);

        Job::factory()->create([
            'job_expiry_date' => now()->addDays(5),
            'status' => Job::STATUS_OPEN,
        ]);

        // Create job expiring later
        Job::factory()->create([
            'job_expiry_date' => now()->addDays(15),
            'status' => Job::STATUS_OPEN,
        ]);

        $jobsExpiringSoon = $this->jobService->getJobsExpiringSoon(7);

        $this->assertCount(2, $jobsExpiringSoon);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
} 