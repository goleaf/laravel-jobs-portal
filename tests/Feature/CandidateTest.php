<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CandidateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function candidate_can_be_created()
    {
        $user = User::factory()->create();
        
        $candidateData = [
            'user_id' => $user->id,
            'expected_salary' => $this->faker->randomNumber(5),
            'experience' => $this->faker->randomNumber(1),
            'career_level_id' => 1,
            'industry_id' => 1,
            'functional_area_id' => 1,
            'current_salary' => $this->faker->randomNumber(5),
            'address' => $this->faker->address,
            'is_immediate_available' => true,
        ];

        $candidate = Candidate::create($candidateData);
        
        $this->assertInstanceOf(Candidate::class, $candidate);
        $this->assertEquals($candidateData['user_id'], $candidate->user_id);
        $this->assertEquals($candidateData['expected_salary'], $candidate->expected_salary);
        $this->assertEquals($candidateData['experience'], $candidate->experience);
        $this->assertTrue($candidate->is_immediate_available);
    }

    /** @test */
    public function candidate_can_be_updated()
    {
        $candidate = Candidate::factory()->create();
        
        $updatedData = [
            'expected_salary' => $this->faker->randomNumber(5),
            'current_salary' => $this->faker->randomNumber(5),
            'is_immediate_available' => false,
        ];
        
        $candidate->update($updatedData);
        $candidate->refresh();
        
        $this->assertEquals($updatedData['expected_salary'], $candidate->expected_salary);
        $this->assertEquals($updatedData['current_salary'], $candidate->current_salary);
        $this->assertFalse($candidate->is_immediate_available);
    }

    /** @test */
    public function candidate_belongs_to_user()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $candidate->user);
        $this->assertEquals($user->id, $candidate->user_id);
    }

    /** @test */
    public function candidate_can_have_education_records()
    {
        $candidate = Candidate::factory()->create();
        
        $education = CandidateEducation::factory()->create([
            'candidate_id' => $candidate->id,
            'degree_level' => $this->faker->word,
            'degree_title' => $this->faker->sentence,
            'year' => $this->faker->year,
            'institute' => $this->faker->company,
        ]);
        
        $this->assertInstanceOf(CandidateEducation::class, $candidate->educations->first());
        $this->assertCount(1, $candidate->educations);
        $this->assertEquals($education->id, $candidate->educations->first()->id);
    }

    /** @test */
    public function candidate_can_have_experience_records()
    {
        $candidate = Candidate::factory()->create();
        
        $experience = CandidateExperience::factory()->create([
            'candidate_id' => $candidate->id,
            'company' => $this->faker->company,
            'job_title' => $this->faker->jobTitle,
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'is_current_job' => false,
        ]);
        
        $this->assertInstanceOf(CandidateExperience::class, $candidate->experiences->first());
        $this->assertCount(1, $candidate->experiences);
        $this->assertEquals($experience->id, $candidate->experiences->first()->id);
    }

    /** @test */
    public function candidate_can_apply_for_jobs()
    {
        $candidate = Candidate::factory()->create();
        $job = Job::factory()->create();
        
        $application = JobApplication::factory()->create([
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'status' => JobApplication::PENDING,
        ]);
        
        $this->assertInstanceOf(JobApplication::class, $candidate->jobApplications->first());
        $this->assertCount(1, $candidate->jobApplications);
        $this->assertEquals($application->id, $candidate->jobApplications->first()->id);
    }

    /** @test */
    public function candidates_can_be_filtered_by_experience()
    {
        Candidate::factory()->count(3)->create(['experience' => 5]);
        Candidate::factory()->count(2)->create(['experience' => 2]);
        
        $experiencedCandidates = Candidate::where('experience', '>=', 5)->get();
        $lesserExperiencedCandidates = Candidate::where('experience', '<', 5)->get();
        
        $this->assertCount(3, $experiencedCandidates);
        $this->assertCount(2, $lesserExperiencedCandidates);
    }

    /** @test */
    public function candidates_can_be_filtered_by_availability()
    {
        Candidate::factory()->count(3)->create(['is_immediate_available' => true]);
        Candidate::factory()->count(2)->create(['is_immediate_available' => false]);
        
        $immediatelyAvailable = Candidate::where('is_immediate_available', true)->get();
        $notImmediatelyAvailable = Candidate::where('is_immediate_available', false)->get();
        
        $this->assertCount(3, $immediatelyAvailable);
        $this->assertCount(2, $notImmediatelyAvailable);
    }
} 