<?php

namespace Tests\Unit\Models;

use App\Models\Candidate;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_job_application()
    {
        $job = Job::factory()->create();
        $candidate = Candidate::factory()->create();
        
        $jobApplication = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'expected_salary' => 75000,
            'notes' => 'This is a test application',
            'status' => 'applied',
        ]);
        
        $this->assertDatabaseHas('job_applications', [
            'id' => $jobApplication->id,
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
        ]);
    }

    /** @test */
    public function it_belongs_to_a_job()
    {
        $job = Job::factory()->create([
            'title' => 'Senior Developer',
        ]);
        
        $candidate = Candidate::factory()->create();
        
        $jobApplication = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'expected_salary' => 80000,
            'status' => 'applied',
        ]);
        
        $this->assertInstanceOf(Job::class, $jobApplication->job);
        $this->assertEquals('Senior Developer', $jobApplication->job->title);
    }

    /** @test */
    public function it_belongs_to_a_candidate()
    {
        $job = Job::factory()->create();
        
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        
        $candidate = Candidate::factory()->create([
            'user_id' => $user->id,
        ]);
        
        $jobApplication = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'expected_salary' => 85000,
            'status' => 'applied',
        ]);
        
        $this->assertInstanceOf(Candidate::class, $jobApplication->candidate);
        $this->assertEquals($candidate->id, $jobApplication->candidate->id);
    }

    /** @test */
    public function it_has_status_scopes()
    {
        $job = Job::factory()->create();
        $candidate = Candidate::factory()->create();
        
        // Create applications with different statuses
        $appliedApplication = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'expected_salary' => 70000,
            'status' => 'applied',
        ]);
        
        $shortlistedApplication = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'expected_salary' => 75000,
            'status' => 'shortlisted',
        ]);
        
        $rejectedApplication = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'expected_salary' => 80000,
            'status' => 'rejected',
        ]);
        
        $this->assertEquals(1, JobApplication::applied()->count());
        $this->assertEquals(1, JobApplication::shortlisted()->count());
        $this->assertEquals(1, JobApplication::rejected()->count());
    }

    /** @test */
    public function it_can_determine_status()
    {
        $job = Job::factory()->create();
        $candidate = Candidate::factory()->create();
        
        $jobApplication = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'expected_salary' => 75000,
            'status' => 'applied',
        ]);
        
        $this->assertTrue($jobApplication->isApplied());
        $this->assertFalse($jobApplication->isShortlisted());
        $this->assertFalse($jobApplication->isRejected());
        
        $jobApplication->status = 'shortlisted';
        $jobApplication->save();
        
        $this->assertFalse($jobApplication->fresh()->isApplied());
        $this->assertTrue($jobApplication->fresh()->isShortlisted());
        $this->assertFalse($jobApplication->fresh()->isRejected());
    }

    /** @test */
    public function it_can_change_status()
    {
        $job = Job::factory()->create();
        $candidate = Candidate::factory()->create();
        
        $jobApplication = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'expected_salary' => 75000,
            'status' => 'applied',
        ]);
        
        $jobApplication->markAsShortlisted();
        $this->assertEquals('shortlisted', $jobApplication->fresh()->status);
        
        $jobApplication->markAsRejected();
        $this->assertEquals('rejected', $jobApplication->fresh()->status);
        
        $jobApplication->markAsHired();
        $this->assertEquals('hired', $jobApplication->fresh()->status);
    }
} 