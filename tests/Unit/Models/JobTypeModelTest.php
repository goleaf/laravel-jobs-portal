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
    public function itHasCorrectFillableAttributes()
    {
        $jobType = new JobType();
        $fillable = $jobType->getFillable();

        $expectedFillable = [
            'name',
            'description',
            'company_id',
            'is_default',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function itHasCorrectCasts()
    {
        $jobType = new JobType();
        $casts = $jobType->getCasts();

        $expectedCasts = [
            'id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'is_default' => 'boolean',
        ];

        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals($cast, $casts[$attribute]);
        }
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
