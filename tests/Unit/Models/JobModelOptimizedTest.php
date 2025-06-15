<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use Tests\UnitTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobModelOptimizedTest extends UnitTestCase
{
    /** @test */
    public function itHasStatusConstants()
    {
        $this->assertEquals(0, Job::STATUS_DRAFT);
        $this->assertEquals(1, Job::STATUS_OPEN);
        $this->assertEquals(2, Job::STATUS_CLOSED);
        $this->assertEquals(3, Job::STATUS_PAUSED);
        $this->assertEquals(4, Job::STATUS_SUSPENDED);
    }

    /** @test */
    public function itHasBooleanConstants()
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
    public function itHasCorrectFillableAttributes()
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
        ];

        foreach ($expectedAttributes as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function itCanBeInstantiatedWithAttributes()
    {
        $job = new Job([
            'job_id' => 'JOB123456',
            'job_title' => 'Software Engineer',
            'description' => 'Great job opportunity',
            'company_id' => 1,
            'job_type_id' => 1,
            'job_category_id' => 1,
            'status' => Job::STATUS_OPEN,
            'salary_from' => 50000,
            'salary_to' => 80000,
            'position' => 2,
            'experience' => 3,
        ]);

        $this->assertEquals('JOB123456', $job->job_id);
        $this->assertEquals('Software Engineer', $job->job_title);
        $this->assertEquals('Great job opportunity', $job->description);
        $this->assertEquals(1, $job->company_id);
        $this->assertEquals(Job::STATUS_OPEN, $job->status);
        $this->assertEquals(50000, $job->salary_from);
        $this->assertEquals(80000, $job->salary_to);
    }

    /** @test */
    public function itHasRelationshipMethods()
    {
        $job = new Job();

        // Test that relationship methods exist
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
    }

    /** @test */
    public function itHasNoPreferenceConstants()
    {
        $expectedNoPreference = [
            2 => 'Both',
            1 => 'Male',
            0 => 'Female',
        ];

        $this->assertEquals($expectedNoPreference, Job::NO_PREFERENCE);
    }

    /** @test */
    public function itHasGenderConstants()
    {
        $expectedGender = [
            0 => 'Male',
            1 => 'Female',
        ];

        $this->assertEquals($expectedGender, Job::GENDER);
    }

    /** @test */
    public function itHasStatusArrayConstants()
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
    public function itHasStatusColorConstants()
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
    public function itHasFavoriteJobStatusConstants()
    {
        $expectedFavoriteStatus = [
            1 => 'Live',
            2 => 'Closed',
            3 => 'Paused',
        ];

        $this->assertEquals($expectedFavoriteStatus, Job::FAVORITE_JOB_STATUS);
    }
}
