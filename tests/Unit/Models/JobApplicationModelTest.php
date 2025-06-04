<?php

namespace Tests\Unit\Models;

use App\Models\JobApplication;
use PHPUnit\Framework\TestCase;

class JobApplicationModelTest extends TestCase
{
    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals(0, JobApplication::STATUS_DRAFT);
        $this->assertEquals(1, JobApplication::STATUS_APPLIED);
        $this->assertEquals(2, JobApplication::REJECTED);
        $this->assertEquals(3, JobApplication::COMPLETE);
        $this->assertEquals(4, JobApplication::SHORT_LIST);
        $this->assertEquals(5, JobApplication::SELECT_STATUS);
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $jobApplication = new JobApplication();
        $fillable = $jobApplication->getFillable();

        $expectedFillable = [
            'job_id',
            'candidate_id',
            'resume_id',
            'expected_salary',
            'notes',
            'status',
            'job_stage_id',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $jobApplication = new JobApplication();
        $casts = $jobApplication->getCasts();

        $expectedCasts = [
            'job_id' => 'integer',
            'candidate_id' => 'integer',
            'resume_id' => 'integer',
            'status' => 'integer',
            'expected_salary' => 'double',
            'job_stage_id' => 'integer',
        ];

        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals($cast, $casts[$attribute]);
        }
    }

    /** @test */
    public function it_has_filter_constants()
    {
        $expectedFilter = [
            5 => 'Select Status',
            3 => 'Hired',
            4 => 'Ongoing',
        ];

        $this->assertEquals($expectedFilter, JobApplication::FILTER);
    }

    /** @test */
    public function it_has_status_array_constants()
    {
        $expectedStatus = [
            0 => 'Drafted',
            1 => 'Applied',
            2 => 'Declined',
            3 => 'Hired',
            4 => 'Ongoing',
        ];

        $this->assertEquals($expectedStatus, JobApplication::STATUS);
    }

    /** @test */
    public function it_has_status_color_constants()
    {
        $expectedColors = [
            0 => 'warning',
            1 => 'primary',
            2 => 'danger',
            3 => 'info',
            4 => 'success',
        ];

        $this->assertEquals($expectedColors, JobApplication::STATUS_COLOR);
    }

    /** @test */
    public function it_can_be_instantiated_with_attributes()
    {
        $jobApplication = new JobApplication([
            'job_id' => 1,
            'candidate_id' => 2,
            'resume_id' => 3,
            'expected_salary' => 65000.50,
            'notes' => 'Great candidate for the position',
            'status' => JobApplication::STATUS_APPLIED,
            'job_stage_id' => 1,
        ]);

        $this->assertEquals(1, $jobApplication->job_id);
        $this->assertEquals(2, $jobApplication->candidate_id);
        $this->assertEquals(3, $jobApplication->resume_id);
        $this->assertEquals(65000.50, $jobApplication->expected_salary);
        $this->assertEquals('Great candidate for the position', $jobApplication->notes);
        $this->assertEquals(JobApplication::STATUS_APPLIED, $jobApplication->status);
        $this->assertEquals(1, $jobApplication->job_stage_id);
    }

    /** @test */
    public function it_has_relationship_methods()
    {
        $jobApplication = new JobApplication();
        
        // Test that relationship methods exist by checking they are callable
        $this->assertTrue(method_exists($jobApplication, 'candidate'));
        $this->assertTrue(method_exists($jobApplication, 'job'));
        $this->assertTrue(method_exists($jobApplication, 'jobStage'));
        $this->assertTrue(method_exists($jobApplication, 'applicationSchedule'));
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $jobApplication = new JobApplication();
        $this->assertEquals('job_applications', $jobApplication->getTable());
    }

    /** @test */
    public function it_has_validation_rules()
    {
        $expectedRules = [
            'job_id' => 'required',
            'resume_id' => 'required',
            'expected_salary' => 'required|numeric|min:0|max:9999999999',
        ];

        $this->assertEquals($expectedRules, JobApplication::$rules);
    }

    /** @test */
    public function it_has_appended_attributes()
    {
        $jobApplication = new JobApplication();
        $this->assertContains('resume_url', $jobApplication->getAppends());
    }

    /** @test */
    public function it_has_workflow_status_progression()
    {
        // Test that status constants follow logical progression
        $this->assertLessThan(JobApplication::STATUS_APPLIED, JobApplication::STATUS_DRAFT);
        $this->assertLessThan(JobApplication::REJECTED, JobApplication::STATUS_APPLIED);
        $this->assertLessThan(JobApplication::COMPLETE, JobApplication::REJECTED);
        $this->assertLessThan(JobApplication::SHORT_LIST, JobApplication::COMPLETE);
    }
} 