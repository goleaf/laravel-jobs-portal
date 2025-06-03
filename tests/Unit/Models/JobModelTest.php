<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use App\Models\Company;
use App\Models\JobType;
use App\Models\JobCategory;
use App\Models\CareerLevel;
use App\Models\FunctionalArea;
use App\Models\JobShift;
use App\Models\RequiredDegreeLevel;
use App\Models\SalaryCurrency;
use App\Models\SalaryPeriod;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\JobApplication;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\FeaturedRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class JobModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Job $job;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->job = Job::factory()->create([
            'job_title' => 'Software Engineer',
            'description' => 'Great opportunity for a software engineer',
            'status' => Job::STATUS_OPEN,
            'is_suspended' => false,
            'job_expiry_date' => now()->addDays(30),
            'salary_from' => 50000,
            'salary_to' => 80000,
            'hide_salary' => false,
        ]);
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals(0, Job::STATUS_DRAFT);
        $this->assertEquals(1, Job::STATUS_OPEN);
        $this->assertEquals(2, Job::STATUS_CLOSED);
        $this->assertEquals(3, Job::STATUS_PAUSED);
        $this->assertEquals(4, Job::STATUS_SUSPENDED);
    }

    /** @test */
    public function it_has_boolean_constants()
    {
        $this->assertEquals(1, Job::YES);
        $this->assertEquals(0, Job::NO);
        $this->assertEquals(0, Job::NOT_SUSPENDED);
        $this->assertEquals(2, Job::SELECT_FEATURD);
        $this->assertEquals(2, Job::SELECT_IS_SUSPENDED);
        $this->assertEquals(2, Job::SELECT_IS_FREELANCER);
        $this->assertEquals(2, Job::SELECT_JOBS_ACTIVE);
    }

    /** @test */
    public function it_has_proper_fillable_attributes()
    {
        $fillable = [
            'job_title', 'description', 'job_id', 'company_id', 'job_category_id',
            'country_id', 'state_id', 'city_id', 'salary_from', 'salary_to',
            'currency_id', 'salary_period_id', 'job_type_id', 'career_level_id',
            'functional_area_id', 'job_shift_id', 'degree_level_id', 'experience',
            'job_expiry_date', 'no_preference', 'hide_salary', 'is_freelance',
            'is_suspended', 'status', 'is_created_by_admin',
        ];

        $this->assertEquals($fillable, $this->job->getFillable());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $casts = [
            'id' => 'integer',
            'company_id' => 'integer',
            'job_category_id' => 'integer',
            'country_id' => 'integer',
            'state_id' => 'integer',
            'city_id' => 'integer',
            'currency_id' => 'integer',
            'salary_period_id' => 'integer',
            'job_type_id' => 'integer',
            'career_level_id' => 'integer',
            'functional_area_id' => 'integer',
            'job_shift_id' => 'integer',
            'degree_level_id' => 'integer',
            'experience' => 'integer',
            'job_expiry_date' => 'date',
            'no_preference' => 'integer',
            'hide_salary' => 'boolean',
            'is_freelance' => 'boolean',
            'is_suspended' => 'boolean',
            'status' => 'integer',
            'is_created_by_admin' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];

        foreach ($casts as $attribute => $cast) {
            $this->assertEquals($cast, $this->job->getCasts()[$attribute] ?? null);
        }
    }

    /** @test */
    public function it_generates_unique_job_id_on_creation()
    {
        $newJob = Job::factory()->create(['job_id' => null]);
        
        $this->assertNotNull($newJob->job_id);
        $this->assertStringStartsWith('JOB-', $newJob->job_id);
    }

    /** @test */
    public function it_belongs_to_country()
    {
        $country = Country::factory()->create();
        $this->job->update(['country_id' => $country->id]);

        $this->assertInstanceOf(Country::class, $this->job->country);
        $this->assertEquals($country->id, $this->job->country->id);
    }

    /** @test */
    public function it_belongs_to_state()
    {
        $state = State::factory()->create();
        $this->job->update(['state_id' => $state->id]);

        $this->assertInstanceOf(State::class, $this->job->state);
        $this->assertEquals($state->id, $this->job->state->id);
    }

    /** @test */
    public function it_belongs_to_city()
    {
        $city = City::factory()->create();
        $this->job->update(['city_id' => $city->id]);

        $this->assertInstanceOf(City::class, $this->job->city);
        $this->assertEquals($city->id, $this->job->city->id);
    }

    /** @test */
    public function it_belongs_to_company()
    {
        $company = Company::factory()->create();
        $this->job->update(['company_id' => $company->id]);

        $this->assertInstanceOf(Company::class, $this->job->company);
        $this->assertEquals($company->id, $this->job->company->id);
    }

    /** @test */
    public function it_belongs_to_currency()
    {
        $currency = SalaryCurrency::factory()->create();
        $this->job->update(['currency_id' => $currency->id]);

        $this->assertInstanceOf(SalaryCurrency::class, $this->job->currency);
        $this->assertEquals($currency->id, $this->job->currency->id);
    }

    /** @test */
    public function it_belongs_to_salary_period()
    {
        $period = SalaryPeriod::factory()->create();
        $this->job->update(['salary_period_id' => $period->id]);

        $this->assertInstanceOf(SalaryPeriod::class, $this->job->salaryPeriod);
        $this->assertEquals($period->id, $this->job->salaryPeriod->id);
    }

    /** @test */
    public function it_belongs_to_job_type()
    {
        $jobType = JobType::factory()->create();
        $this->job->update(['job_type_id' => $jobType->id]);

        $this->assertInstanceOf(JobType::class, $this->job->jobType);
        $this->assertEquals($jobType->id, $this->job->jobType->id);
    }

    /** @test */
    public function it_belongs_to_career_level()
    {
        $careerLevel = CareerLevel::factory()->create();
        $this->job->update(['career_level_id' => $careerLevel->id]);

        $this->assertInstanceOf(CareerLevel::class, $this->job->careerLevel);
        $this->assertEquals($careerLevel->id, $this->job->careerLevel->id);
    }

    /** @test */
    public function it_belongs_to_functional_area()
    {
        $functionalArea = FunctionalArea::factory()->create();
        $this->job->update(['functional_area_id' => $functionalArea->id]);

        $this->assertInstanceOf(FunctionalArea::class, $this->job->functionalArea);
        $this->assertEquals($functionalArea->id, $this->job->functionalArea->id);
    }

    /** @test */
    public function it_belongs_to_job_shift()
    {
        $jobShift = JobShift::factory()->create();
        $this->job->update(['job_shift_id' => $jobShift->id]);

        $this->assertInstanceOf(JobShift::class, $this->job->jobShift);
        $this->assertEquals($jobShift->id, $this->job->jobShift->id);
    }

    /** @test */
    public function it_belongs_to_degree_level()
    {
        $degreeLevel = RequiredDegreeLevel::factory()->create();
        $this->job->update(['degree_level_id' => $degreeLevel->id]);

        $this->assertInstanceOf(RequiredDegreeLevel::class, $this->job->degreeLevel);
        $this->assertEquals($degreeLevel->id, $this->job->degreeLevel->id);
    }

    /** @test */
    public function it_belongs_to_job_category()
    {
        $category = JobCategory::factory()->create();
        $this->job->update(['job_category_id' => $category->id]);

        $this->assertInstanceOf(JobCategory::class, $this->job->jobCategory);
        $this->assertEquals($category->id, $this->job->jobCategory->id);
    }

    /** @test */
    public function it_has_many_to_many_skills()
    {
        $skills = Skill::factory()->count(3)->create();
        $this->job->jobsSkill()->attach($skills->pluck('id'));

        $this->assertCount(3, $this->job->jobsSkill);
        $this->assertInstanceOf(Skill::class, $this->job->jobsSkill->first());
    }

    /** @test */
    public function it_has_many_to_many_tags()
    {
        $tags = Tag::factory()->count(2)->create();
        $this->job->jobsTag()->attach($tags->pluck('id'));

        $this->assertCount(2, $this->job->jobsTag);
        $this->assertInstanceOf(Tag::class, $this->job->jobsTag->first());
    }

    /** @test */
    public function it_has_many_job_applications()
    {
        $applications = JobApplication::factory()->count(3)->create(['job_id' => $this->job->id]);

        $this->assertCount(3, $this->job->appliedJobs);
        $this->assertInstanceOf(JobApplication::class, $this->job->appliedJobs->first());
    }

    /** @test */
    public function it_returns_cached_country_name()
    {
        $country = Country::factory()->create(['name' => 'United States']);
        $this->job->update(['country_id' => $country->id]);

        Cache::forget("job.{$this->job->id}.country_name");

        $countryName = $this->job->country_name;
        $this->assertEquals('United States', $countryName);

        $this->assertTrue(Cache::has("job.{$this->job->id}.country_name"));
    }

    /** @test */
    public function it_returns_cached_state_name()
    {
        $state = State::factory()->create(['name' => 'California']);
        $this->job->update(['state_id' => $state->id]);

        Cache::forget("job.{$this->job->id}.state_name");

        $stateName = $this->job->state_name;
        $this->assertEquals('California', $stateName);

        $this->assertTrue(Cache::has("job.{$this->job->id}.state_name"));
    }

    /** @test */
    public function it_returns_cached_city_name()
    {
        $city = City::factory()->create(['name' => 'Los Angeles']);
        $this->job->update(['city_id' => $city->id]);

        Cache::forget("job.{$this->job->id}.city_name");

        $cityName = $this->job->city_name;
        $this->assertEquals('Los Angeles', $cityName);

        $this->assertTrue(Cache::has("job.{$this->job->id}.city_name"));
    }

    /** @test */
    public function it_returns_full_location_attribute()
    {
        $country = Country::factory()->create(['name' => 'United States']);
        $state = State::factory()->create(['name' => 'California']);
        $city = City::factory()->create(['name' => 'Los Angeles']);
        
        $this->job->update([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        Cache::forget("job.{$this->job->id}.full_location");

        $fullLocation = $this->job->full_location;
        $this->assertEquals('Los Angeles, California, United States', $fullLocation);
    }

    /** @test */
    public function it_returns_remote_when_no_location()
    {
        $this->job->update([
            'country_id' => null,
            'state_id' => null,
            'city_id' => null,
        ]);

        Cache::forget("job.{$this->job->id}.full_location");

        $fullLocation = $this->job->full_location;
        $this->assertEquals('Remote', $fullLocation);
    }

    /** @test */
    public function it_scopes_jobs_by_status()
    {
        Job::factory()->create(['status' => Job::STATUS_DRAFT]);
        Job::factory()->create(['status' => Job::STATUS_OPEN]);

        $openJobs = Job::status(Job::STATUS_OPEN)->get();
        $this->assertTrue($openJobs->every(fn($job) => $job->status === Job::STATUS_OPEN));
    }

    /** @test */
    public function it_scopes_active_jobs()
    {
        Job::factory()->create(['status' => Job::STATUS_CLOSED]);
        Job::factory()->create(['is_suspended' => true]);
        Job::factory()->create(['job_expiry_date' => now()->subDays(1)]);

        $activeJobs = Job::active()->get();
        $this->assertTrue($activeJobs->every(fn($job) => $job->isActive()));
    }

    /** @test */
    public function it_scopes_jobs_by_location()
    {
        $country = Country::factory()->create();
        $state = State::factory()->create();
        $city = City::factory()->create();

        Job::factory()->create(['country_id' => $country->id, 'state_id' => $state->id, 'city_id' => $city->id]);
        Job::factory()->create(['country_id' => 999]); // Different location

        $locationJobs = Job::byLocation($country->id, $state->id, $city->id)->get();
        $this->assertTrue($locationJobs->every(function($job) use ($country, $state, $city) {
            return $job->country_id === $country->id && 
                   $job->state_id === $state->id && 
                   $job->city_id === $city->id;
        }));
    }

    /** @test */
    public function it_scopes_jobs_by_salary_range()
    {
        Job::factory()->create(['salary_from' => 30000, 'salary_to' => 50000]);
        Job::factory()->create(['salary_from' => 60000, 'salary_to' => 80000]);

        $salaryJobs = Job::bySalaryRange(40000, 70000)->get();
        $this->assertTrue($salaryJobs->every(function($job) {
            return $job->salary_from >= 40000 && $job->salary_to <= 70000;
        }));
    }

    /** @test */
    public function it_checks_if_job_is_expired()
    {
        $expiredJob = Job::factory()->create(['job_expiry_date' => now()->subDays(1)]);
        $activeJob = Job::factory()->create(['job_expiry_date' => now()->addDays(1)]);

        $this->assertTrue($expiredJob->isExpired());
        $this->assertFalse($activeJob->isExpired());
    }

    /** @test */
    public function it_checks_if_job_is_active()
    {
        $activeJob = Job::factory()->create([
            'status' => Job::STATUS_OPEN,
            'is_suspended' => false,
            'job_expiry_date' => now()->addDays(1),
        ]);

        $inactiveJob = Job::factory()->create([
            'status' => Job::STATUS_CLOSED,
        ]);

        $this->assertTrue($activeJob->isActive());
        $this->assertFalse($inactiveJob->isActive());
    }

    /** @test */
    public function it_formats_salary_correctly()
    {
        $currency = SalaryCurrency::factory()->create(['currency_symbol' => '$']);
        $period = SalaryPeriod::factory()->create(['period' => 'month']);
        
        $this->job->update([
            'currency_id' => $currency->id,
            'salary_period_id' => $period->id,
            'salary_from' => 50000,
            'salary_to' => 80000,
            'hide_salary' => false,
        ]);

        $formattedSalary = $this->job->formatted_salary;
        $this->assertEquals('$50000 - $80000 per month', $formattedSalary);
    }

    /** @test */
    public function it_returns_salary_not_disclosed_when_hidden()
    {
        $this->job->update(['hide_salary' => true]);

        $formattedSalary = $this->job->formatted_salary;
        $this->assertEquals('Salary not disclosed', $formattedSalary);
    }

    /** @test */
    public function it_returns_salary_negotiable_when_no_range()
    {
        $this->job->update([
            'salary_from' => null,
            'salary_to' => null,
            'hide_salary' => false,
        ]);

        $formattedSalary = $this->job->formatted_salary;
        $this->assertEquals('Salary negotiable', $formattedSalary);
    }

    /** @test */
    public function it_returns_correct_status_badge_class()
    {
        $this->job->update(['status' => Job::STATUS_OPEN]);
        $this->assertEquals('badge-success', $this->job->status_badge_class);

        $this->job->update(['status' => Job::STATUS_DRAFT]);
        $this->assertEquals('badge-warning', $this->job->status_badge_class);

        $this->job->update(['status' => Job::STATUS_CLOSED]);
        $this->assertEquals('badge-danger', $this->job->status_badge_class);
    }

    /** @test */
    public function it_returns_correct_status_text()
    {
        $this->job->update(['status' => Job::STATUS_OPEN]);
        $statusText = $this->job->status_text;
        $this->assertNotEmpty($statusText);
    }

    /** @test */
    public function it_clears_cache_when_updated()
    {
        Cache::put("job.{$this->job->id}", 'test_data');
        Cache::put("job.featured", 'featured_data');
        Cache::put("jobs.active", 'active_data');

        $this->job->update(['job_title' => 'Updated Title']);

        $this->assertFalse(Cache::has("job.{$this->job->id}"));
        $this->assertFalse(Cache::has("job.featured"));
        $this->assertFalse(Cache::has("jobs.active"));
    }

    /** @test */
    public function it_clears_cache_when_deleted()
    {
        Cache::put("job.{$this->job->id}", 'test_data');
        Cache::put("job.featured", 'featured_data');
        Cache::put("jobs.active", 'active_data');

        $this->job->delete();

        $this->assertFalse(Cache::has("job.{$this->job->id}"));
        $this->assertFalse(Cache::has("job.featured"));
        $this->assertFalse(Cache::has("jobs.active"));
    }

    /** @test */
    public function it_has_featured_relationship()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphOne::class, $this->job->featured());
    }

    /** @test */
    public function it_has_active_featured_relationship()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphOne::class, $this->job->activeFeatured());
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
} 