<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use PHPUnit\Framework\TestCase;

class JobModelSimpleTest extends TestCase
{
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
            'is_suspended',
            'is_created_by_admin',
            'status',
            'position',
            'experience',
            'country_id',
            'state_id',
            'city_id',
            'job_expiry_date',
            'last_change',
            'key_responsibilities',
        ];

        foreach ($expectedAttributes as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
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
    public function it_has_relationship_methods()
    {
        $job = new Job();
        
        // Test that relationship methods exist by checking they are callable
        $this->assertTrue(method_exists($job, 'company'));
        $this->assertTrue(method_exists($job, 'jobType'));
        $this->assertTrue(method_exists($job, 'jobCategory'));
        $this->assertTrue(method_exists($job, 'careerLevel'));
        $this->assertTrue(method_exists($job, 'functionalArea'));
        $this->assertTrue(method_exists($job, 'jobShift'));
        $this->assertTrue(method_exists($job, 'degreeLevel'));
        $this->assertTrue(method_exists($job, 'currency'));
        $this->assertTrue(method_exists($job, 'salaryPeriod'));
        $this->assertTrue(method_exists($job, 'country'));
        $this->assertTrue(method_exists($job, 'state'));
        $this->assertTrue(method_exists($job, 'city'));
        $this->assertTrue(method_exists($job, 'appliedJobs'));
        $this->assertTrue(method_exists($job, 'jobsSkill'));
        $this->assertTrue(method_exists($job, 'jobsTag'));
        $this->assertTrue(method_exists($job, 'featured'));
        $this->assertTrue(method_exists($job, 'activeFeatured'));
    }

    /** @test */
    public function it_can_be_instantiated_with_attributes()
    {
        $job = new Job([
            'job_id' => 'JOB123456',
            'job_title' => 'Software Developer',
            'description' => 'A great job opportunity',
            'status' => Job::STATUS_OPEN,
            'salary_from' => 50000,
            'salary_to' => 75000,
            'position' => 2,
            'experience' => 3,
            'is_suspended' => false,
        ]);

        $this->assertEquals('JOB123456', $job->job_id);
        $this->assertEquals('Software Developer', $job->job_title);
        $this->assertEquals('A great job opportunity', $job->description);
        $this->assertEquals(Job::STATUS_OPEN, $job->status);
        $this->assertEquals(50000, $job->salary_from);
        $this->assertEquals(75000, $job->salary_to);
        $this->assertEquals(2, $job->position);
        $this->assertEquals(3, $job->experience);
        $this->assertFalse($job->is_suspended);
    }
} 