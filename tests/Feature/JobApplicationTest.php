<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobApplicationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $adminUser;
    protected $employerUser;
    protected $candidateUser;
    protected $company;
    protected $job;
    protected $candidate;
    protected $jobApplication;
    protected $jobStage;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users for testing
        $this->adminUser = User::factory()->create(['user_type' => User::ADMIN]);
        $this->employerUser = User::factory()->create(['user_type' => User::EMPLOYER]);
        $this->candidateUser = User::factory()->create(['user_type' => User::CANDIDATE]);

        // Create company for employer
        $this->company = Company::factory()->create(['user_id' => $this->employerUser->id]);
        $this->employerUser->company()->save($this->company);

        // Create job for the company
        $this->job = Job::factory()->create([
            'company_id' => $this->company->id,
            'status' => Job::STATUS_OPEN,
        ]);

        // Create candidate profile
        $this->candidate = Candidate::factory()->create(['user_id' => $this->candidateUser->id]);

        // Create job application
        $this->jobApplication = JobApplication::factory()->create([
            'job_id' => $this->job->id,
            'candidate_id' => $this->candidate->id,
            'status' => JobApplication::STATUS_APPLIED,
        ]);

        // Create job stage for the company
        $this->jobStage = JobStage::factory()->create(['company_id' => $this->company->id]);
    }

    /** @test */
    public function employerCanViewJobApplications()
    {
        $response = $this->actingAs($this->employerUser)
            ->get("/employer/jobs/{$this->job->id}/applications")
        ;

        $response->assertStatus(200);
        $response->assertViewIs('employer.job_applications.index');
        $response->assertViewHas('jobId', $this->job->id);
    }

    /** @test */
    public function employerCannotViewJobApplicationsForJobsNotBelongingToThem()
    {
        // Create another employer with job
        $otherEmployer = User::factory()->create(['user_type' => User::EMPLOYER]);
        $otherCompany = Company::factory()->create(['user_id' => $otherEmployer->id]);
        $otherJob = Job::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->actingAs($this->employerUser)
            ->get("/employer/jobs/{$otherJob->id}/applications")
        ;

        $response->assertStatus(404);
    }

    /** @test */
    public function employerCanChangeJobApplicationStatus()
    {
        $response = $this->actingAs($this->employerUser)
            ->post("/employer/job-applications/{$this->jobApplication->id}/status-change", [
                'status' => JobApplication::SHORT_LIST,
                'jobId' => $this->job->id,
            ])
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('job_applications', [
            'id' => $this->jobApplication->id,
            'status' => JobApplication::SHORT_LIST,
        ]);
    }

    /** @test */
    public function employerCannotChangeStatusOfApplicationsForJobsNotBelongingToThem()
    {
        // Create another employer with job and application
        $otherEmployer = User::factory()->create(['user_type' => User::EMPLOYER]);
        $otherCompany = Company::factory()->create(['user_id' => $otherEmployer->id]);
        $otherJob = Job::factory()->create(['company_id' => $otherCompany->id]);
        $otherApplication = JobApplication::factory()->create([
            'job_id' => $otherJob->id,
            'candidate_id' => $this->candidate->id,
        ]);

        $response = $this->actingAs($this->employerUser)
            ->post("/employer/job-applications/{$otherApplication->id}/status-change", [
                'status' => JobApplication::SHORT_LIST,
                'jobId' => $otherJob->id,
            ])
        ;

        $response->assertStatus(403); // or appropriate error status
        $this->assertDatabaseMissing('job_applications', [
            'id' => $otherApplication->id,
            'status' => JobApplication::SHORT_LIST,
        ]);
    }

    /** @test */
    public function employerCanDeleteJobApplication()
    {
        $response = $this->actingAs($this->employerUser)
            ->delete("/employer/job-applications/{$this->jobApplication->id}", [
                'jobId' => $this->job->id,
            ])
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('job_applications', [
            'id' => $this->jobApplication->id,
        ]);
    }

    /** @test */
    public function employerCannotDeleteApplicationsForJobsNotBelongingToThem()
    {
        // Create another employer with job and application
        $otherEmployer = User::factory()->create(['user_type' => User::EMPLOYER]);
        $otherCompany = Company::factory()->create(['user_id' => $otherEmployer->id]);
        $otherJob = Job::factory()->create(['company_id' => $otherCompany->id]);
        $otherApplication = JobApplication::factory()->create([
            'job_id' => $otherJob->id,
            'candidate_id' => $this->candidate->id,
        ]);

        $response = $this->actingAs($this->employerUser)
            ->delete("/employer/job-applications/{$otherApplication->id}", [
                'jobId' => $otherJob->id,
            ])
        ;

        $response->assertStatus(403); // or appropriate error status
        $this->assertDatabaseHas('job_applications', [
            'id' => $otherApplication->id,
        ]);
    }

    /** @test */
    public function employerCanChangeJobStage()
    {
        $response = $this->actingAs($this->employerUser)
            ->post('/employer/job-applications/job-stage', [
                'job_application_id' => $this->jobApplication->id,
                'job_stage' => $this->jobStage->id,
            ])
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('job_applications', [
            'id' => $this->jobApplication->id,
            'job_stage_id' => $this->jobStage->id,
        ]);
    }

    /** @test */
    public function candidateCanViewTheirJobApplicationStatus()
    {
        // Assuming there's a route for candidates to view their applications
        $response = $this->actingAs($this->candidateUser)
            ->get('/candidate/job-applications')
        ;

        $response->assertStatus(200);
        // Add any specific assertions for the view response
    }

    /** @test */
    public function employerCanViewSlotScreen()
    {
        $response = $this->actingAs($this->employerUser)
            ->get("/employer/job-applications/{$this->jobApplication->id}/slots-screen")
        ;

        $response->assertStatus(200);
        $response->assertViewIs('employer.job_applications.view_slot_screen');
        $response->assertViewHas(['jobStage', 'applicationId']);
    }

    /** @test */
    public function employerCanStoreInterviewSlot()
    {
        $slotData = [
            'job_application_id' => $this->jobApplication->id,
            'date' => [1 => date('Y-m-d', strtotime('+2 days'))],
            'time' => [1 => '10:00:00'],
            'notes' => [1 => 'Please prepare for technical questions'],
            'scheduleSlotCount' => 1,
        ];

        $response = $this->actingAs($this->employerUser)
            ->post("/employer/job-applications/{$this->job->id}/slots", $slotData)
        ;

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('job_application_schedules', [
            'job_application_id' => $this->jobApplication->id,
            'time' => '10:00:00',
            'notes' => 'Please prepare for technical questions',
        ]);
    }

    /** @test */
    public function employerCannotCreateDuplicateSlots()
    {
        // First create a slot
        $slotData = [
            'job_application_id' => $this->jobApplication->id,
            'date' => [1 => date('Y-m-d', strtotime('+2 days'))],
            'time' => [1 => '10:00:00'],
            'notes' => [1 => 'Interview slot'],
            'scheduleSlotCount' => 1,
        ];

        $this->actingAs($this->employerUser)
            ->post("/employer/job-applications/{$this->job->id}/slots", $slotData)
        ;

        // Try to create the same slot again
        $response = $this->actingAs($this->employerUser)
            ->post("/employer/job-applications/{$this->job->id}/slots", $slotData)
        ;

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function employerCanViewSelectedCandidates()
    {
        $response = $this->actingAs($this->employerUser)
            ->get('/employer/selected-candidate')
        ;

        $response->assertStatus(200);
        $response->assertViewIs('selected_candidate.index');
        $response->assertViewHas('status');
    }
}
