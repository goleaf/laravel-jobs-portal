<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobApplication;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * @internal
 *
 * @coversNothing
 */
class JobModelTest extends TestCase
{
    use RefreshDatabase;

    protected $job;
    protected $company;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $user = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $user->id]);
        $this->category = JobCategory::factory()->create();
        
        $this->job = Job::factory()->create([
            'company_id' => $this->company->id,
            'job_category_id' => $this->category->id
        ]);
    }

    /** @test */
    public function itCanBeCreated()
    {
        $Job = Job::factory()->create();

        $this->assertInstanceOf(Job::class, $Job);
        $this->assertModelExists($Job);
    }

    /** @test */
    public function itHasRequiredFillableFields()
    {
        $Job = new Job();
        $fillable = $Job->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itCanBeSoftDeleted()
    {
        $Job = Job::factory()->create();
        $Job->delete();

        $this->assertSoftDeleted($Job);
    }

    /** @test */
    public function test_job_belongs_to_company()
    {
        $this->assertInstanceOf(Company::class, $this->job->company);
        $this->assertEquals($this->company->id, $this->job->company->id);
    }

    /** @test */
    public function test_job_belongs_to_category()
    {
        $category = JobCategory::factory()->create();
        $job = Job::factory()->create(['job_category_id' => $category->id]);
        $this->assertInstanceOf(JobCategory::class, $job->category);
        $this->assertEquals($category->id, $job->category->id);
    }

    /** @test */
    public function test_job_has_many_applications()
    {
        $user = User::factory()->create();
        JobApplication::factory(3)->create([
            'job_id' => $this->job->id,
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(Collection::class, $this->job->applications);
        $this->assertCount(3, $this->job->applications);
        $this->assertInstanceOf(JobApplication::class, $this->job->applications->first());
    }

    /** @test */
    public function test_job_belongs_to_many_skills()
    {
        $skills = Skill::factory(4)->create();
        $this->job->skills()->attach($skills->pluck('id'));

        $this->assertInstanceOf(Collection::class, $this->job->skills);
        $this->assertCount(4, $this->job->skills);
        $this->assertInstanceOf(Skill::class, $this->job->skills->first());
    }

    /** @test */
    public function test_active_scope()
    {
        Job::factory()->create(['status' => 'active']);
        Job::factory()->create(['status' => 'inactive']);
        Job::factory()->create(['status' => 'draft']);

        $activeJobs = Job::active()->get();
        
        $this->assertCount(1, $activeJobs);
        $this->assertEquals('active', $activeJobs->first()->status);
    }

    /** @test */
    public function test_featured_scope()
    {
        Job::factory(2)->create(['is_featured' => true]);
        Job::factory(3)->create(['is_featured' => false]);

        $featuredJobs = Job::featured()->get();
        
        $this->assertCount(2, $featuredJobs);
        $featuredJobs->each(function ($job) {
            $this->assertTrue($job->is_featured);
        });
    }

    /** @test */
    public function test_recent_scope()
    {
        Job::factory()->create(['created_at' => now()->subDays(8)]); // Old job
        Job::factory(2)->create(['created_at' => now()->subDays(3)]); // Recent jobs

        $recentJobs = Job::recent()->get();
        
        $this->assertCount(2, $recentJobs);
    }

    /** @test */
    public function test_by_category_scope()
    {
        $category = JobCategory::factory()->create();
        Job::factory(3)->create(['category_id' => $category->id]);

        $jobsByCategory = Job::byCategory($category->id)->get();
        
        $this->assertCount(3, $jobsByCategory);
        $jobsByCategory->each(function ($job) use ($category) {
            $this->assertEquals($category->id, $job->category_id);
        });
    }

    /** @test */
    public function test_by_company_scope()
    {
        $company = Company::factory()->create();
        Job::factory(2)->create(['company_id' => $company->id]);

        $jobsByCompany = Job::byCompany($company->id)->get();
        
        $this->assertCount(2, $jobsByCompany);
        $jobsByCompany->each(function ($job) use ($company) {
            $this->assertEquals($company->id, $job->company_id);
        });
    }

    /** @test */
    public function test_by_location_scope()
    {
        Job::factory(2)->create(['location' => 'New York, NY']);
        Job::factory()->create(['location' => 'Los Angeles, CA']);

        $jobsByLocation = Job::byLocation('New York')->get();
        
        $this->assertCount(2, $jobsByLocation);
    }

    /** @test */
    public function test_by_employment_type_scope()
    {
        Job::factory(3)->create(['employment_type' => 'full_time']);
        Job::factory()->create(['employment_type' => 'part_time']);

        $fullTimeJobs = Job::byEmploymentType('full_time')->get();
        
        $this->assertCount(3, $fullTimeJobs);
        $fullTimeJobs->each(function ($job) {
            $this->assertEquals('full_time', $job->employment_type);
        });
    }

    /** @test */
    public function test_salary_range_scope()
    {
        Job::factory()->create(['salary_min' => 50000, 'salary_max' => 70000]);
        Job::factory()->create(['salary_min' => 80000, 'salary_max' => 100000]);
        Job::factory()->create(['salary_min' => 120000, 'salary_max' => 150000]);

        $jobsInRange = Job::salaryRange(70000, 110000)->get();
        
        $this->assertCount(1, $jobsInRange);
        $this->assertEquals(80000, $jobsInRange->first()->salary_min);
    }

    /** @test */
    public function test_search_scope()
    {
        Job::factory()->create(['title' => 'Senior PHP Developer']);
        Job::factory()->create(['title' => 'Laravel Developer']);
        Job::factory()->create(['title' => 'Frontend React Developer']);

        $phpJobs = Job::search('PHP')->get();
        $this->assertCount(1, $phpJobs);

        $developerJobs = Job::search('Developer')->get();
        $this->assertCount(3, $developerJobs);
    }

    /** @test */
    public function test_with_skills_scope()
    {
        $skill1 = Skill::factory()->create(['name' => 'PHP']);
        $skill2 = Skill::factory()->create(['name' => 'Laravel']);

        $job1 = Job::factory()->create();
        $job2 = Job::factory()->create();
        
        $job1->skills()->attach([$skill1->id, $skill2->id]);
        $job2->skills()->attach([$skill1->id]);

        $jobsWithBothSkills = Job::withSkills([$skill1->id, $skill2->id])->get();
        $this->assertCount(1, $jobsWithBothSkills);
        $this->assertEquals($job1->id, $jobsWithBothSkills->first()->id);

        $jobsWithAnySkill = Job::withAnySkills([$skill1->id, $skill2->id])->get();
        $this->assertCount(2, $jobsWithAnySkill);
    }

    /** @test */
    public function test_popular_scope()
    {
        $user = User::factory()->create();
        
        $popularJob = Job::factory()->create();
        $unpopularJob = Job::factory()->create();

        // Create applications for popular job
        JobApplication::factory(5)->create([
            'job_id' => $popularJob->id,
            'user_id' => $user->id
        ]);

        // Create fewer applications for unpopular job
        JobApplication::factory(1)->create([
            'job_id' => $unpopularJob->id,
            'user_id' => $user->id
        ]);

        $popularJobs = Job::popular()->get();
        
        $this->assertTrue($popularJobs->contains($popularJob));
        $this->assertEquals($popularJob->id, $popularJobs->first()->id);
    }

    /** @test */
    public function test_expired_scope()
    {
        Job::factory()->create(['deadline' => now()->subDays(1)]); // Expired
        Job::factory()->create(['deadline' => now()->addDays(1)]); // Not expired
        Job::factory()->create(['deadline' => null]); // No deadline

        $expiredJobs = Job::expired()->get();
        
        $this->assertCount(1, $expiredJobs);
    }

    /** @test */
    public function test_is_expired_attribute()
    {
        $expiredJob = Job::factory()->create(['deadline' => now()->subDays(1)]);
        $activeJob = Job::factory()->create(['deadline' => now()->addDays(1)]);
        $noDeadlineJob = Job::factory()->create(['deadline' => null]);

        $this->assertTrue($expiredJob->is_expired);
        $this->assertFalse($activeJob->is_expired);
        $this->assertFalse($noDeadlineJob->is_expired);
    }

    /** @test */
    public function test_is_featured_attribute()
    {
        $featuredJob = Job::factory()->create(['is_featured' => true]);
        $regularJob = Job::factory()->create(['is_featured' => false]);

        $this->assertTrue($featuredJob->is_featured);
        $this->assertFalse($regularJob->is_featured);
    }

    /** @test */
    public function test_applications_count_attribute()
    {
        $user = User::factory()->create();
        JobApplication::factory(5)->create([
            'job_id' => $this->job->id,
            'user_id' => $user->id
        ]);

        $this->assertEquals(5, $this->job->fresh()->applications_count);
    }

    /** @test */
    public function test_formatted_salary_attribute()
    {
        $job = Job::factory()->create([
            'salary_min' => 50000,
            'salary_max' => 75000
        ]);

        $this->assertEquals('$50,000 - $75,000', $job->formatted_salary);

        $jobWithMinOnly = Job::factory()->create([
            'salary_min' => 60000,
            'salary_max' => null
        ]);

        $this->assertEquals('From $60,000', $jobWithMinOnly->formatted_salary);

        $jobWithoutSalary = Job::factory()->create([
            'salary_min' => null,
            'salary_max' => null
        ]);

        $this->assertEquals('Competitive', $jobWithoutSalary->formatted_salary);
    }

    /** @test */
    public function test_time_since_posted_attribute()
    {
        $job = Job::factory()->create(['created_at' => now()->subDays(3)]);
        
        $this->assertStringContains('3 days ago', $job->time_since_posted);
    }

    /** @test */
    public function test_can_apply_method()
    {
        $user = User::factory()->create();
        
        // User hasn't applied yet
        $this->assertTrue($this->job->canApply($user));
        
        // User applies
        JobApplication::factory()->create([
            'job_id' => $this->job->id,
            'user_id' => $user->id
        ]);
        
        // User can't apply again
        $this->assertFalse($this->job->fresh()->canApply($user));
        
        // Job is expired
        $expiredJob = Job::factory()->create(['deadline' => now()->subDays(1)]);
        $this->assertFalse($expiredJob->canApply($user));
        
        // Job is inactive
        $inactiveJob = Job::factory()->create(['status' => 'inactive']);
        $this->assertFalse($inactiveJob->canApply($user));
    }

    /** @test */
    public function test_has_applied_method()
    {
        $user = User::factory()->create();
        
        $this->assertFalse($this->job->hasApplied($user));
        
        JobApplication::factory()->create([
            'job_id' => $this->job->id,
            'user_id' => $user->id
        ]);
        
        $this->assertTrue($this->job->fresh()->hasApplied($user));
    }

    /** @test */
    public function test_get_similar_jobs_method()
    {
        $skill1 = Skill::factory()->create();
        $skill2 = Skill::factory()->create();
        
        $this->job->skills()->attach([$skill1->id, $skill2->id]);
        
        // Create similar jobs
        $similarJob1 = Job::factory()->create(['category_id' => $this->category->id]);
        $similarJob2 = Job::factory()->create(['category_id' => $this->category->id]);
        $similarJob1->skills()->attach([$skill1->id]);
        $similarJob2->skills()->attach([$skill2->id]);
        
        // Create dissimilar job
        $differentCategory = JobCategory::factory()->create();
        Job::factory()->create(['category_id' => $differentCategory->id]);
        
        $similarJobs = $this->job->getSimilarJobs();
        
        $this->assertInstanceOf(Collection::class, $similarJobs);
        $this->assertCount(2, $similarJobs);
    }

    /** @test */
    public function test_increment_views_method()
    {
        $initialViews = $this->job->views_count ?? 0;
        
        $this->job->incrementViews();
        
        $this->assertEquals($initialViews + 1, $this->job->fresh()->views_count);
    }

    /** @test */
    public function test_mark_as_filled_method()
    {
        $this->job->markAsFilled();
        
        $this->assertEquals('filled', $this->job->fresh()->status);
        $this->assertNotNull($this->job->fresh()->filled_at);
    }

    /** @test */
    public function test_reopen_job_method()
    {
        $this->job->update(['status' => 'filled', 'filled_at' => now()]);
        
        $this->job->reopenJob();
        
        $this->assertEquals('active', $this->job->fresh()->status);
        $this->assertNull($this->job->fresh()->filled_at);
    }

    /** @test */
    public function test_job_model_factory()
    {
        $job = Job::factory()->create();
        
        $this->assertInstanceOf(Job::class, $job);
        $this->assertNotNull($job->title);
        $this->assertNotNull($job->description);
        $this->assertNotNull($job->company_id);
        $this->assertNotNull($job->category_id);
    }

    /** @test */
    public function test_job_model_validation_rules()
    {
        $validData = [
            'title' => 'Test Job',
            'description' => 'This is a test job description.',
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'location' => 'Test City',
            'employment_type' => 'full_time'
        ];

        $job = Job::create($validData);
        
        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals('Test Job', $job->title);
    }

    /** @test */
    public function test_job_soft_deletes()
    {
        $jobId = $this->job->id;
        
        $this->job->delete();
        
        // Job should be soft deleted
        $this->assertSoftDeleted('jobs', ['id' => $jobId]);
        
        // Job should not appear in regular queries
        $this->assertNull(Job::find($jobId));
        
        // Job should appear in withTrashed queries
        $this->assertNotNull(Job::withTrashed()->find($jobId));
    }

    /** @test */
    public function test_job_casts()
    {
        $job = Job::factory()->create([
            'requirements' => ['Requirement 1', 'Requirement 2'],
            'benefits' => ['Benefit 1', 'Benefit 2'],
            'is_featured' => 1,
            'is_remote' => 1
        ]);

        $this->assertIsArray($job->requirements);
        $this->assertIsArray($job->benefits);
        $this->assertIsBool($job->is_featured);
        $this->assertIsBool($job->is_remote);
    }

    /** @test */
    public function test_job_fillable_attributes()
    {
        $fillableAttributes = [
            'title', 'description', 'company_id', 'category_id',
            'location', 'salary_min', 'salary_max', 'employment_type',
            'experience_level', 'deadline', 'requirements', 'benefits',
            'is_featured', 'is_remote', 'status'
        ];

        $job = new Job();
        
        foreach ($fillableAttributes as $attribute) {
            $this->assertContains($attribute, $job->getFillable());
        }
    }

    /** @test */
    public function test_job_timestamps()
    {
        $this->assertNotNull($this->job->created_at);
        $this->assertNotNull($this->job->updated_at);
        $this->assertInstanceOf(Carbon::class, $this->job->created_at);
        $this->assertInstanceOf(Carbon::class, $this->job->updated_at);
    }
}
