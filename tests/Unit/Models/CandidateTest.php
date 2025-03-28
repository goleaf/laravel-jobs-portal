<?php

namespace Tests\Unit\Models;

use App\Models\Candidate;
use App\Models\User;
use App\Models\CandidateExperience;
use App\Models\CandidateEducation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_candidate()
    {
        $candidate = Candidate::factory()->create();
        $this->assertDatabaseHas('candidates', ['id' => $candidate->id]);
    }

    /** @test */
    public function a_candidate_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $candidate->user);
        $this->assertEquals($user->id, $candidate->user->id);
    }

    /** @test */
    public function a_candidate_has_experiences()
    {
        $candidate = Candidate::factory()->create();
        CandidateExperience::factory(3)->create(['candidate_id' => $candidate->id]);
        
        $this->assertCount(3, $candidate->experiences);
        $this->assertInstanceOf(CandidateExperience::class, $candidate->experiences->first());
    }

    /** @test */
    public function a_candidate_has_educations()
    {
        $candidate = Candidate::factory()->create();
        CandidateEducation::factory(2)->create(['candidate_id' => $candidate->id]);
        
        $this->assertCount(2, $candidate->educations);
        $this->assertInstanceOf(CandidateEducation::class, $candidate->educations->first());
    }

    /** @test */
    public function it_can_filter_immediate_available_candidates()
    {
        $immediateCandidate = Candidate::factory()->create(['immediate_available' => true]);
        $notImmediateCandidate = Candidate::factory()->create(['immediate_available' => false]);
        
        $immediateCandidates = Candidate::whereImmediateAvailable()->get();
        
        $this->assertTrue($immediateCandidates->contains($immediateCandidate));
        $this->assertFalse($immediateCandidates->contains($notImmediateCandidate));
    }

    /** @test */
    public function it_can_get_candidates_full_name()
    {
        $user = User::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);
        
        $this->assertEquals('John Doe', $candidate->user->full_name);
    }
} 