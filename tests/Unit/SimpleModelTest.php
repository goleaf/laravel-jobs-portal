<?php

namespace Tests\Unit;

use App\Models\Candidate;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SimpleModelTest extends TestCase
{
    /** @test */
    public function itCanInstantiateCandidateModel()
    {
        $candidate = new Candidate();

        $this->assertInstanceOf(Candidate::class, $candidate);
    }

    /** @test */
    public function itHasCorrectFillableAttributes()
    {
        $candidate = new Candidate();
        $fillable = $candidate->getFillable();

        $expectedFillable = [
            'user_id',
            'unique_id',
            'father_name',
            'marital_status_id',
            'nationality',
            'national_id_card',
            'experience',
            'career_level_id',
            'industry_id',
            'functional_area_id',
            'current_salary',
            'expected_salary',
            'image_path',
            'resume_path',
            'available_at',
            'immediate_available',
            'job_alert',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function itHasCorrectCasts()
    {
        $candidate = new Candidate();
        $casts = $candidate->getCasts();

        // Test some basic casts that should exist
        $this->assertArrayHasKey('id', $casts);
        $this->assertArrayHasKey('user_id', $casts);
        $this->assertArrayHasKey('immediate_available', $casts);

        // Laravel 12 uses 'int' for auto-generated casts
        $this->assertEquals('int', $casts['id']);
        $this->assertEquals('int', $casts['user_id']);
    }

    /** @test */
    public function itHasStatusConstants()
    {
        $this->assertEquals(1, Candidate::ACTIVE);
        $this->assertEquals(0, Candidate::DEACTIVE);
        $this->assertEquals(2, Candidate::ALL);
        $this->assertEquals(1, Candidate::CANDIDATE_LOGIN_TYPE);
        $this->assertEquals(2, Candidate::CANDIDATE_EMP_TYPE);
    }

    /** @test */
    public function itHasTableName()
    {
        $candidate = new Candidate();
        $this->assertEquals('candidates', $candidate->getTable());
    }
}
