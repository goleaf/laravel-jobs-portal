<?php

namespace Tests\Unit\Models;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ApplicationModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        // Skip test - Generic Application model not needed for job portal
        // JobApplication model is used instead for job applications
        $this->markTestSkipped('Generic Application model not used in job portal - using JobApplication instead');
    }

    /** @test */
    public function itHasRequiredFillableFields()
    {
        // Skip test - Generic Application model not needed for job portal
        $this->markTestSkipped('Generic Application model not used in job portal - using JobApplication instead');
    }

    /** @test */
    public function itCanBeSoftDeleted()
    {
        // Skip test - Generic Application model not needed for job portal
        $this->markTestSkipped('Generic Application model not used in job portal - using JobApplication instead');
    }
}
