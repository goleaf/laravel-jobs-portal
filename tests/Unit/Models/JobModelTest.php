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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
    public function it_has_correct_fillable_attributes()
    {
        $job = new Job();
        $fillable = $job->getFillable();

        $expectedAttributes = [
            'job_id',
            'job_title',
            'description',
            'company_id',
            'job_type_id',
            'job_category_id',
            'career_level_id',
            'functional_area_id',
            'job_shift_id',
            'degree_level_id',
            'currency_id',
            'salary_period_id',
            'salary_from',
            'salary_to',
            'hide_salary',
            'no_preference',
            'is_freelance',
            'is_featured',
            'is_suspended',
            'is_created_by_admin',
            'status',
            'position',
            'experience',
            'country_id',
            'state_id',
            'city_id',
            'job_expiry_date',
        ];

        foreach ($expectedAttributes as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function it_can_be_created_with_valid_attributes()
    {
        $company = Company::factory()->create();

        $jobData = [
            'job_id' => 'JOB123456',
            'job_title' => $this->faker->jobTitle(),
            'description' => $this->faker->text(1000),
            'company_id' => $company->id,
            'job_type_id' => 1,
            'job_category_id' => 1,
            'status' => Job::STATUS_OPEN,
            'salary_from' => 50000,
            'salary_to' => 80000,
            'position' => 2,
            'experience' => 3,
            'job_expiry_date' => now()->addDays(30),
        ];

        $job = Job::create($jobData);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($jobData['job_title'], $job->job_title);
        $this->assertEquals($jobData['company_id'], $job->company_id);
        $this->assertEquals($jobData['status'], $job->status);
        $this->assertEquals($jobData['salary_from'], $job->salary_from);
        $this->assertEquals($jobData['salary_to'], $job->salary_to);
    }

    /** @test */
    public function it_belongs_to_company()
    {
        $job = Job::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->company());
        $this->assertInstanceOf(Company::class, $job->company);
    }

    /** @test */
    public function it_belongs_to_job_type()
    {
        $job = Job::factory()->create(['job_type_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->jobType());
    }

    /** @test */
    public function it_belongs_to_job_category()
    {
        $job = Job::factory()->create(['job_category_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->jobCategory());
    }

    /** @test */
    public function it_belongs_to_career_level()
    {
        $job = Job::factory()->create(['career_level_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->careerLevel());
    }

    /** @test */
    public function it_belongs_to_functional_area()
    {
        $job = Job::factory()->create(['functional_area_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->functionalArea());
    }

    /** @test */
    public function it_belongs_to_job_shift()
    {
        $job = Job::factory()->create(['job_shift_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->jobShift());
    }

    /** @test */
    public function it_belongs_to_degree_level()
    {
        $job = Job::factory()->create(['degree_level_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->degreeLevel());
    }

    /** @test */
    public function it_belongs_to_currency()
    {
        $job = Job::factory()->create(['currency_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->currency());
    }

    /** @test */
    public function it_belongs_to_salary_period()
    {
        $job = Job::factory()->create(['salary_period_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->salaryPeriod());
    }

    /** @test */
    public function it_belongs_to_country()
    {
        $job = Job::factory()->create(['country_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->country());
    }

    /** @test */
    public function it_belongs_to_state()
    {
        $job = Job::factory()->create(['state_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->state());
    }

    /** @test */
    public function it_belongs_to_city()
    {
        $job = Job::factory()->create(['city_id' => 1]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $job->city());
    }

    /** @test */
    public function it_has_many_job_applications()
    {
        $job = Job::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $job->appliedJobs());
    }

    /** @test */
    public function it_has_many_to_many_skills()
    {
        $job = Job::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $job->jobsSkill());
    }

    /** @test */
    public function it_has_many_to_many_tags()
    {
        $job = Job::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $job->jobsTag());
    }

    /** @test */
    public function it_has_morph_one_featured_relationship()
    {
        $job = Job::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphOne::class, $job->featured());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphOne::class, $job->activeFeatured());
    }

    /** @test */
    public function it_has_status_scope()
    {
        // Create jobs with different statuses
        Job::factory()->count(3)->create(['status' => Job::STATUS_OPEN]);
        Job::factory()->count(2)->create(['status' => Job::STATUS_CLOSED]);
        Job::factory()->count(1)->create(['status' => Job::STATUS_DRAFT]);

        // Test status scope
        $openJobs = Job::status(Job::STATUS_OPEN)->get();
        $closedJobs = Job::status(Job::STATUS_CLOSED)->get();
        $draftJobs = Job::status(Job::STATUS_DRAFT)->get();

        $this->assertCount(3, $openJobs);
        $this->assertCount(2, $closedJobs);
        $this->assertCount(1, $draftJobs);
    }

    /** @test */
    public function it_generates_full_location_attribute()
    {
        $job = Job::factory()->create([
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
        ]);

        // The full_location attribute should combine location information
        $this->assertIsString($job->full_location);
    }

    /** @test */
    public function it_can_be_featured()
    {
        $job = Job::factory()->featured()->create();

        $this->assertTrue($job->is_featured);
    }

    /** @test */
    public function it_can_be_suspended()
    {
        $job = Job::factory()->suspended()->create();

        $this->assertTrue($job->is_suspended);
    }

    /** @test */
    public function it_can_be_closed()
    {
        $job = Job::factory()->closed()->create();

        $this->assertEquals(Job::STATUS_CLOSED, $job->status);
    }

    /** @test */
    public function it_can_be_draft()
    {
        $job = Job::factory()->draft()->create();

        $this->assertEquals(Job::STATUS_DRAFT, $job->status);
    }

    /** @test */
    public function it_can_be_expired()
    {
        $job = Job::factory()->expired()->create();

        $this->assertTrue($job->job_expiry_date < now());
    }

    /** @test */
    public function it_has_no_preference_constants()
    {
        $expectedNoPreference = [
            2 => 'Both',
            1 => 'Male',
            0 => 'Female',
        ];

        $this->assertEquals($expectedNoPreference, Job::NO_PREFERENCE);
    }

    /** @test */
    public function it_has_gender_constants()
    {
        $expectedGender = [
            0 => 'Male',
            1 => 'Female',
        ];

        $this->assertEquals($expectedGender, Job::GENDER);
    }

    /** @test */
    public function it_has_status_array_constants()
    {
        $expectedStatus = [
            0 => 'Drafted',
            1 => 'Live',
            2 => 'Closed',
            3 => 'Paused',
        ];

        $this->assertEquals($expectedStatus, Job::STATUS_ARRAY);
    }

    /** @test */
    public function it_has_status_color_constants()
    {
        $expectedColors = [
            0 => 'warning',
            1 => 'success',
            2 => 'danger',
            3 => 'primary',
        ];

        $this->assertEquals($expectedColors, Job::STATUS_COLOR);
    }

    /** @test */
    public function it_has_favorite_job_status_constants()
    {
        $expectedFavoriteStatus = [
            1 => 'Live',
            2 => 'Closed',
            3 => 'Paused',
        ];

        $this->assertEquals($expectedFavoriteStatus, Job::FAVORITE_JOB_STATUS);
    }

    /** @test */
    public function it_can_filter_jobs_by_status()
    {
        Job::factory()->count(5)->create(['status' => Job::STATUS_OPEN]);
        Job::factory()->count(3)->create(['status' => Job::STATUS_CLOSED]);
        Job::factory()->count(2)->create(['status' => Job::STATUS_DRAFT]);

        $openJobs = Job::where('status', Job::STATUS_OPEN)->get();
        $closedJobs = Job::where('status', Job::STATUS_CLOSED)->get();
        $draftJobs = Job::where('status', Job::STATUS_DRAFT)->get();

        $this->assertCount(5, $openJobs);
        $this->assertCount(3, $closedJobs);
        $this->assertCount(2, $draftJobs);
    }

    /** @test */
    public function it_can_filter_jobs_by_featured_status()
    {
        Job::factory()->count(4)->featured()->create();
        Job::factory()->count(6)->create(['is_featured' => false]);

        $featuredJobs = Job::where('is_featured', true)->get();
        $nonFeaturedJobs = Job::where('is_featured', false)->get();

        $this->assertCount(4, $featuredJobs);
        $this->assertCount(6, $nonFeaturedJobs);
    }

    /** @test */
    public function it_can_filter_jobs_by_suspension_status()
    {
        Job::factory()->count(2)->suspended()->create();
        Job::factory()->count(8)->create(['is_suspended' => false]);

        $suspendedJobs = Job::where('is_suspended', true)->get();
        $activJobs = Job::where('is_suspended', false)->get();

        $this->assertCount(2, $suspendedJobs);
        $this->assertCount(8, $activJobs);
    }

    /** @test */
    public function it_can_filter_jobs_by_expiry_date()
    {
        Job::factory()->count(3)->expired()->create();
        Job::factory()->count(7)->create(['job_expiry_date' => now()->addDays(30)]);

        $expiredJobs = Job::where('job_expiry_date', '<', now())->get();
        $activeJobs = Job::where('job_expiry_date', '>=', now())->get();

        $this->assertCount(3, $expiredJobs);
        $this->assertCount(7, $activeJobs);
    }

    /** @test */
    public function it_stores_job_id_as_string()
    {
        $job = Job::factory()->create(['job_id' => 'JOB123456']);

        $this->assertEquals('JOB123456', $job->job_id);
        $this->assertIsString($job->job_id);
    }

    /** @test */
    public function it_stores_salary_information()
    {
        $job = Job::factory()->create([
            'salary_from' => 50000,
            'salary_to' => 75000,
            'hide_salary' => false,
        ]);

        $this->assertEquals(50000, $job->salary_from);
        $this->assertEquals(75000, $job->salary_to);
        $this->assertFalse($job->hide_salary);
    }

    /** @test */
    public function it_stores_position_and_experience_requirements()
    {
        $job = Job::factory()->create([
            'position' => 5,
            'experience' => 3,
        ]);

        $this->assertEquals(5, $job->position);
        $this->assertEquals(3, $job->experience);
    }
} 