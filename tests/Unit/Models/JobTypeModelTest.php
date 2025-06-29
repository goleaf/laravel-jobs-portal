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
    public function itHasCorrectTableName()
    {
        $jobType = new JobType();
        $this->assertEquals('job_types', $jobType->getTable());
    }

    /** @test */
    public function itHasCorrectFillableAttributes(): void
    {
        $jobType = new JobType();
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
    public function itHasCorrectCasts(): void
    {
        $jobType = new JobType();
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
    public function itCanBeInstantiatedWithAttributes()
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
    public function itHasRelationshipMethods()
    {
        $jobType = new JobType();

        // Test that relationship methods exist
        $this->assertTrue(method_exists($jobType, 'jobs'));
        $this->assertTrue(method_exists($jobType, 'candidateJobAlerts'));
    }

    /** @test */
    public function itHasValidationRules()
    {
        $expectedRules = [
            'name' => 'required|max:160|unique:job_types,name',
        ];

        $this->assertEquals($expectedRules, JobType::$rules);
    }
}
