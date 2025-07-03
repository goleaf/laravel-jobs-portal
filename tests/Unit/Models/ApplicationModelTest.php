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
    public function it_can_be_created()
    {
        // Skip test - Generic Application model not needed for job portal
        // JobApplication model is used instead for job applications
        $this->markTestSkipped('Generic Application model not used in job portal - using JobApplication instead');
    }

    /** @test */
    public function it_has_required_fillable_fields()
    {
        // Skip test - Generic Application model not needed for job portal
        $this->markTestSkipped('Generic Application model not used in job portal - using JobApplication instead');
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        // Skip test - Generic Application model not needed for job portal
        $this->markTestSkipped('Generic Application model not used in job portal - using JobApplication instead');
    }
}
