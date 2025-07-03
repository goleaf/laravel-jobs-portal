<?php

namespace Tests\Unit\Models;

use App\Models\JobType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobTypeModelTest extends TestCase
{
    /** @test */
    public function it_has_correct_table_name()
    {
        $jobType = new JobType;
        $this->assertEquals('job_types', $jobType->getTable());
    }

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $jobType = new JobType;
        $expectedFillable = [
            'name',
            'description',
            'is_default',
            'is_active',
            'sort_order',
            'icon',
            'color',
            'is_featured',
            'meta_title',
            'meta_description',
            'slug',
        ];

        $this->assertEquals($expectedFillable, $jobType->getFillable());
    }

    /** @test */
    public function it_has_correct_casts(): void
    {
        $jobType = new JobType;
        $expectedCasts = [
            'id' => 'int',
            'sort_order' => 'int',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];

        $this->assertEquals($expectedCasts, $jobType->getCasts());
    }

    /** @test */
    public function it_can_be_instantiated_with_attributes()
    {
        $jobType = new JobType([
            'name' => 'Full Time',
            'description' => 'Full time employment',
            'company_id' => 1,
            'is_default' => true,
        ]);

        $this->assertEquals('Full Time', $jobType->name);
        $this->assertEquals('Full time employment', $jobType->description);
        $this->assertEquals(1, $jobType->company_id);
        $this->assertEquals(true, $jobType->is_default);
    }

    /** @test */
    public function it_has_relationship_methods()
    {
        $jobType = new JobType;

        // Test that relationship methods exist
        $this->assertTrue(method_exists($jobType, 'jobs'));
        $this->assertTrue(method_exists($jobType, 'candidateJobAlerts'));
    }

    /** @test */
    public function it_has_validation_rules()
    {
        $expectedRules = [
            'name' => 'required|max:160|unique:job_types,name',
        ];

        $this->assertEquals($expectedRules, JobType::$rules);
    }
}
