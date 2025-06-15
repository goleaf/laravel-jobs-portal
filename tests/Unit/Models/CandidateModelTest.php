<?php

namespace Tests\Unit\Models;

use App\Models\Candidate;
use PHPUnit\Framework\TestCase;

class CandidateModelTest extends TestCase
{
    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals(1, Candidate::ACTIVE);
        $this->assertEquals(0, Candidate::DEACTIVE);
        $this->assertEquals(2, Candidate::ALL);
        $this->assertEquals(1, Candidate::CANDIDATE_LOGIN_TYPE);
        $this->assertEquals(2, Candidate::CANDIDATE_EMP_TYPE);
    }

    /** @test */
    public function it_has_availability_constants()
    {
        $this->assertEquals(1, Candidate::IMMEDIATE_AVAILABLE);
        $this->assertEquals(0, Candidate::Not_IMMEDIATE_AVAILABLE);
    }

    /** @test */
    public function it_has_path_constants()
    {
        $this->assertEquals('candidates/resumes', Candidate::RESUME_PATH);
        $this->assertEquals('candidates/images', Candidate::IMAGE_PATH);
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
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
    public function it_has_correct_casts()
    {
        $candidate = new Candidate();
        $casts = $candidate->getCasts();

        $expectedCasts = [
            'id' => 'integer',  // Laravel 12 returns 'integer' not 'int'
            'user_id' => 'integer',
            'marital_status_id' => 'integer',
            'career_level_id' => 'integer',
            'industry_id' => 'integer',
            'functional_area_id' => 'integer',
            'immediate_available' => 'integer',
        ];

        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals($cast, $casts[$attribute]);
        }
    }

    /** @test */
    public function it_has_status_array_constants()
    {
        $expectedStatus = [
            2 => 'All',
            1 => 'Active',
            0 => 'Deactive',
        ];

        $this->assertEquals($expectedStatus, Candidate::STATUS);
    }

    /** @test */
    public function it_has_immediate_availability_constants()
    {
        $expectedImmediate = [
            2 => 'All',
            1 => 'Immediate Available',
            0 => 'Not Immediate Available',
        ];

        $this->assertEquals($expectedImmediate, Candidate::IMMEDIATE);
    }

    /** @test */
    public function it_can_be_instantiated_with_attributes()
    {
        $candidate = new Candidate([
            'user_id' => 1,
            'unique_id' => 'CAND123456',
            'father_name' => 'John Smith Sr.',
            'marital_status_id' => 1,
            'nationality' => 'American',
            'national_id_card' => '123-45-6789',
            'experience' => '3 years',
            'career_level_id' => 2,
            'industry_id' => 1,
            'functional_area_id' => 3,
            'current_salary' => '50000',
            'expected_salary' => '60000',
            'immediate_available' => Candidate::IMMEDIATE_AVAILABLE,
        ]);

        $this->assertEquals(1, $candidate->user_id);
        $this->assertEquals('CAND123456', $candidate->unique_id);
        $this->assertEquals('John Smith Sr.', $candidate->father_name);
        $this->assertEquals(1, $candidate->marital_status_id);
        $this->assertEquals('American', $candidate->nationality);
        $this->assertEquals('123-45-6789', $candidate->national_id_card);
        $this->assertEquals('3 years', $candidate->experience);
        $this->assertEquals(2, $candidate->career_level_id);
        $this->assertEquals(1, $candidate->industry_id);
        $this->assertEquals(3, $candidate->functional_area_id);
        $this->assertEquals('50000.00', $candidate->current_salary);
        $this->assertEquals('60000.00', $candidate->expected_salary);
        $this->assertEquals(Candidate::IMMEDIATE_AVAILABLE, $candidate->immediate_available);
    }

    /** @test */
    public function it_has_relationship_methods()
    {
        $candidate = new Candidate();
        
        // Test that relationship methods exist by checking they are callable
        $this->assertTrue(method_exists($candidate, 'user'));
        $this->assertTrue(method_exists($candidate, 'candidateEducation'));
        $this->assertTrue(method_exists($candidate, 'candidateExperience'));
        $this->assertTrue(method_exists($candidate, 'jobAlerts'));
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $candidate = new Candidate();
        $this->assertEquals('candidates', $candidate->getTable());
    }

    /** @test */
    public function it_has_validation_rules()
    {
        $expectedRules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email:filter|unique:users,email',
            'password' => 'nullable|same:password_confirmation|min:6',
            'marital_status_id' => 'nullable|integer|exists:marital_statuses,id',
            'nationality' => 'nullable|string|max:100',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:states,id',
            'city_id' => 'required|integer|exists:cities,id',
            'phone' => 'required|string|max:20',
            'experience' => 'nullable|integer|min:0|max:50',
            'career_level_id' => 'nullable|integer|exists:career_levels,id',
            'industry_id' => 'nullable|integer|exists:industries,id',
            'functional_area_id' => 'nullable|integer|exists:functional_areas,id',
            'current_salary' => 'nullable|numeric|min:0',
            'expected_salary' => 'nullable|numeric|min:0',
            'father_name' => 'nullable|string|max:100',
            'national_id_card' => 'nullable|string|max:50',
        ];

        $this->assertEquals($expectedRules, Candidate::$rules);
    }
} 