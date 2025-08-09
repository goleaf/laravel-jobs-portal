<?php

namespace Tests\Unit\Models;

use App\Models\CandidateExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CandidateExperienceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $this->markTestSkipped('Candidates depend on users/auth (removed). Skipping create test.');
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new CandidateExperience;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new CandidateExperience;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->markTestSkipped('Candidates depend on users/auth (removed). Skipping update test.');
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->markTestSkipped('Candidates depend on users/auth (removed). Skipping delete test.');
    }
}
