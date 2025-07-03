<?php

namespace Tests\Unit\Models;

use App\Models\CareerLevel;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\FunctionalArea;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobTest extends TestCase
{
    use RefreshDatabase;

    private Job $job;
    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create(['user_id' => $this->user->id]);
        $this->job = Job::factory()->create(['company_id' => $this->company->id]);
    }

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $job = new Job;
        $expectedFillable = [
            'job_id', 'job_title', 'title', 'description', 'requirements', 'benefits',
            'company_id', 'user_id', 'job_type_id', 'job_category_id', 'career_level_id',
            'functional_area_id', 'job_shift_id', 'degree_level_id', 'position_id',
            'currency_id', 'salary_period_id', 'country_id', 'state_id', 'city_id',
            'salary_from', 'salary_to', 'salary_min', 'salary_max', 'job_expiry_date',
            'expires_at', 'published_at', 'country', 'state', 'city', 'location',
            'address', 'no_preference', 'hide_salary', 'is_freelance', 'is_suspended',
            'is_remote', 'is_featured', 'is_active', 'status', 'is_created_by_admin',
            'position', 'experience', 'last_change', 'key_responsibilities',
        ];
        $this->assertEquals($expectedFillable, $job->getFillable());
    }

    /** @test */
    public function it_has_correct_casts(): void
    {
        $job = new Job;
        $expectedCasts = [
            'id' => 'int',
            'company_id' => 'int',
            'user_id' => 'int',
            'job_type_id' => 'int',
            'job_category_id' => 'int',
            'career_level_id' => 'int',
            'functional_area_id' => 'int',
            'job_shift_id' => 'int',
            'degree_level_id' => 'int',
            'position_id' => 'int',
            'currency_id' => 'int',
            'salary_period_id' => 'int',
            'country_id' => 'int',
            'state_id' => 'int',
            'city_id' => 'int',
            'no_preference' => 'boolean',
            'hide_salary' => 'boolean',
            'is_freelance' => 'boolean',
            'is_suspended' => 'boolean',
            'is_remote' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_created_by_admin' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'job_expiry_date' => 'datetime',
        ];
        $this->assertEquals($expectedCasts, $job->getCasts());
    }

    /** @test */
    public function it_belongs_to_company(): void
    {
        $this->assertInstanceOf(Company::class, $this->job->company);
        $this->assertEquals($this->company->id, $this->job->company->id);
    }

    /** @test */
    public function it_belongs_to_job_category(): void
    {
        $category = JobCategory::factory()->create();
        $job = Job::factory()->create(['job_category_id' => $category->id]);

        $this->assertInstanceOf(JobCategory::class, $job->jobCategory);
        $this->assertEquals($category->id, $job->jobCategory->id);
    }

    /** @test */
    public function it_belongs_to_job_type(): void
    {
        $jobType = JobType::factory()->create();
        $job = Job::factory()->create(['job_type_id' => $jobType->id]);

        $this->assertInstanceOf(JobType::class, $job->jobType);
        $this->assertEquals($jobType->id, $job->jobType->id);
    }

    /** @test */
    public function it_belongs_to_career_level(): void
    {
        $careerLevel = CareerLevel::factory()->create();
        $job = Job::factory()->create(['career_level_id' => $careerLevel->id]);

        $this->assertInstanceOf(CareerLevel::class, $job->careerLevel);
        $this->assertEquals($careerLevel->id, $job->careerLevel->id);
    }

    /** @test */
    public function it_belongs_to_functional_area(): void
    {
        $functionalArea = FunctionalArea::factory()->create();
        $job = Job::factory()->create(['functional_area_id' => $functionalArea->id]);

        $this->assertInstanceOf(FunctionalArea::class, $job->functionalArea);
        $this->assertEquals($functionalArea->id, $job->functionalArea->id);
    }

    /** @test */
    public function it_belongs_to_location_models(): void
    {
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);

        $job = Job::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $this->assertInstanceOf(Country::class, $job->country);
        $this->assertInstanceOf(State::class, $job->state);
        $this->assertInstanceOf(City::class, $job->city);

        $this->assertEquals($country->id, $job->country->id);
        $this->assertEquals($state->id, $job->state->id);
        $this->assertEquals($city->id, $job->city->id);
    }

    /** @test */
    public function it_has_many_job_applications(): void
    {
        $applications = JobApplication::factory()->count(3)->create(['job_id' => $this->job->id]);

        $this->assertInstanceOf(Collection::class, $this->job->jobApplications);
        $this->assertCount(3, $this->job->jobApplications);

        foreach ($applications as $application) {
            $this->assertTrue($this->job->jobApplications->contains($application));
        }
    }

    /** @test */
    public function active_scope_returns_only_active_jobs(): void
    {
        Job::factory()->create(['is_suspended' => false]);
        Job::factory()->create(['is_suspended' => true]);

        $activeJobs = Job::active()->get();

        $this->assertCount(2, $activeJobs); // Including the one from setUp
        $activeJobs->each(function ($job) {
            $this->assertFalse($job->is_suspended);
        });
    }

    /** @test */
    public function featured_scope_returns_only_featured_jobs(): void
    {
        Job::factory()->create(['is_featured' => true]);
        Job::factory()->create(['is_featured' => false]);

        $featuredJobs = Job::featured()->get();

        $featuredJobs->each(function ($job) {
            $this->assertTrue($job->is_featured);
        });
    }

    /** @test */
    public function by_company_scope_filters_by_company(): void
    {
        $otherCompany = Company::factory()->create();
        Job::factory()->create(['company_id' => $otherCompany->id]);

        $companyJobs = Job::byCompany($this->company->id)->get();

        $this->assertCount(1, $companyJobs);
        $this->assertEquals($this->company->id, $companyJobs->first()->company_id);
    }

    /** @test */
    public function by_category_scope_filters_by_category(): void
    {
        $category = JobCategory::factory()->create();
        Job::factory()->create(['job_category_id' => $category->id]);

        $categoryJobs = Job::byCategory($category->id)->get();

        $categoryJobs->each(function ($job) use ($category) {
            $this->assertEquals($category->id, $job->job_category_id);
        });
    }

    /** @test */
    public function by_location_scope_filters_by_location(): void
    {
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);

        Job::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $locationJobs = Job::byLocation($country->id, $state->id, $city->id)->get();

        $locationJobs->each(function ($job) use ($country, $state, $city) {
            $this->assertEquals($country->id, $job->country_id);
            $this->assertEquals($state->id, $job->state_id);
            $this->assertEquals($city->id, $job->city_id);
        });
    }

    /** @test */
    public function salary_range_scope_filters_by_salary(): void
    {
        Job::factory()->create(['salary_from' => 50000, 'salary_to' => 80000]);
        Job::factory()->create(['salary_from' => 30000, 'salary_to' => 45000]);

        $salaryJobs = Job::salaryRange(40000, 90000)->get();

        $salaryJobs->each(function ($job) {
            $this->assertTrue($job->salary_from >= 40000 || $job->salary_to <= 90000);
        });
    }

    /** @test */
    public function experience_range_scope_filters_by_experience(): void
    {
        Job::factory()->create(['experience_from' => 2, 'experience_to' => 5]);
        Job::factory()->create(['experience_from' => 5, 'experience_to' => 8]);

        $experienceJobs = Job::experienceRange(3, 6)->get();

        $experienceJobs->each(function ($job) {
            $this->assertTrue($job->experience_from >= 3 || $job->experience_to <= 6);
        });
    }

    /** @test */
    public function search_scope_searches_in_title_and_description(): void
    {
        Job::factory()->create(['title' => 'Software Engineer', 'description' => 'PHP Developer']);
        Job::factory()->create(['title' => 'Marketing Manager', 'description' => 'Digital Marketing']);

        $searchJobs = Job::search('Software')->get();

        $this->assertCount(1, $searchJobs);
        $this->assertStringContainsString('Software', $searchJobs->first()->title);
    }

    /** @test */
    public function recent_scope_returns_jobs_from_last30_days(): void
    {
        Job::factory()->create(['created_at' => now()->subDays(10)]);
        Job::factory()->create(['created_at' => now()->subDays(40)]);

        $recentJobs = Job::recent()->get();

        $recentJobs->each(function ($job) {
            $this->assertTrue($job->created_at->gte(now()->subDays(30)));
        });
    }

    /** @test */
    public function expired_scope_returns_expired_jobs(): void
    {
        Job::factory()->create(['job_expiry_date' => now()->subDays(5)]);
        Job::factory()->create(['job_expiry_date' => now()->addDays(5)]);

        $expiredJobs = Job::expired()->get();

        $expiredJobs->each(function ($job) {
            $this->assertTrue($job->job_expiry_date->lt(now()));
        });
    }

    /** @test */
    public function it_can_check_if_job_is_expired(): void
    {
        $expiredJob = Job::factory()->create(['job_expiry_date' => now()->subDay()]);
        $activeJob = Job::factory()->create(['job_expiry_date' => now()->addDay()]);

        $this->assertTrue($expiredJob->isExpired());
        $this->assertFalse($activeJob->isExpired());
    }

    /** @test */
    public function it_can_get_formatted_salary_range(): void
    {
        $job = Job::factory()->create([
            'salary_from' => 50000,
            'salary_to' => 80000,
            'hide_salary' => false,
        ]);

        $this->assertStringContainsString('50,000', $job->getFormattedSalaryRange());
        $this->assertStringContainsString('80,000', $job->getFormattedSalaryRange());
    }

    /** @test */
    public function it_returns_hidden_salary_when_salary_is_hidden(): void
    {
        $job = Job::factory()->create(['hide_salary' => true]);

        $this->assertEquals('Salary not disclosed', $job->getFormattedSalaryRange());
    }

    /** @test */
    public function it_can_get_applications_count(): void
    {
        JobApplication::factory()->count(5)->create(['job_id' => $this->job->id]);

        $this->assertEquals(5, $this->job->getApplicationsCount());
    }

    /** @test */
    public function it_can_get_full_location_string(): void
    {
        $country = Country::factory()->create(['name' => 'United States']);
        $state = State::factory()->create(['name' => 'California', 'country_id' => $country->id]);
        $city = City::factory()->create(['name' => 'San Francisco', 'state_id' => $state->id]);

        $job = Job::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $fullLocation = $job->full_location;

        $this->assertStringContainsString('San Francisco', $fullLocation);
        $this->assertStringContainsString('California', $fullLocation);
        $this->assertStringContainsString('United States', $fullLocation);
    }
}
