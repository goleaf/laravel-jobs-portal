<?php

namespace Tests\Unit\Models;

use App\Models\Application;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobApplicationModelTest extends TestCase
{
    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $application = new Application;
        $fillable = $application->getFillable();
        $expectedFillable = ['job_id', 'candidate_id', 'resume_id', 'expected_salary', 'status', 'notes'];
        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function it_has_relationship_methods()
    {
        $application = new Application;
        $this->assertTrue(method_exists($application, 'candidate'));
        $this->assertTrue(method_exists($application, 'job'));
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $application = new Application;
        $this->assertEquals('job_applications', $application->getTable());
    }
}
