<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use App\Models\Candidate;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /** @test */
    public function candidate_can_apply_for_a_job()
    {
        Storage::fake('public');

        $company = Company::factory()->create(['status' => 'active']);
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'status' => 'active'
        ]);
        
        $user = User::factory()->create(['role' => 'candidate']);
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $resumeFile = UploadedFile::fake()->create('resume.pdf', 1000);
        
        $response = $this->post(route('jobs.apply', $job->id), [
            'cover_letter' => $this->faker->paragraph,
            'resume' => $resumeFile,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('job_applications', [
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
        ]);
        
        Storage::disk('public')->assertExists('resumes/' . $resumeFile->hashName());
    }
    
    /** @test */
    public function candidate_cannot_apply_for_inactive_job()
    {
        $company = Company::factory()->create(['status' => 'active']);
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'status' => 'inactive'
        ]);
        
        $user = User::factory()->create(['role' => 'candidate']);
        Candidate::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $response = $this->post(route('jobs.apply', $job->id), [
            'cover_letter' => $this->faker->paragraph,
        ]);
        
        $response->assertStatus(404);
    }
    
    /** @test */
    public function company_owner_can_view_applications()
    {
        $user = User::factory()->create(['role' => 'company']);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'status' => 'active'
        ]);
        
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'status' => 'active'
        ]);
        
        $candidateUser = User::factory()->create(['role' => 'candidate']);
        $candidate = Candidate::factory()->create(['user_id' => $candidateUser->id]);
        
        JobApplication::factory()->create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'pending'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get(route('company.applications'));
        
        $response->assertStatus(200);
        $response->assertViewIs('company.applications');
        $response->assertSee($job->title);
    }
    
    /** @test */
    public function company_owner_can_update_application_status()
    {
        $user = User::factory()->create(['role' => 'company']);
        $company = Company::factory()->create([
            'user_id' => $user->id,
            'status' => 'active'
        ]);
        
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'status' => 'active'
        ]);
        
        $candidateUser = User::factory()->create(['role' => 'candidate']);
        $candidate = Candidate::factory()->create(['user_id' => $candidateUser->id]);
        
        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'pending'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->patch(route('applications.update-status', $application->id), [
            'status' => 'accepted'
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'accepted'
        ]);
    }
    
    /** @test */
    public function candidate_can_view_their_applications()
    {
        $company = Company::factory()->create(['status' => 'active']);
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'status' => 'active'
        ]);
        
        $user = User::factory()->create(['role' => 'candidate']);
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);
        
        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'pending'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get(route('candidate.applications'));
        
        $response->assertStatus(200);
        $response->assertViewIs('candidate.applications');
        $response->assertSee($job->title);
    }
    
    /** @test */
    public function candidate_can_withdraw_application()
    {
        $company = Company::factory()->create(['status' => 'active']);
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'status' => 'active'
        ]);
        
        $user = User::factory()->create(['role' => 'candidate']);
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);
        
        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'pending'
        ]);
        
        $this->actingAs($user);
        
        $response = $this->delete(route('applications.withdraw', $application->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertSoftDeleted($application);
    }
    
    /** @test */
    public function non_owner_cannot_access_application_details()
    {
        $company = Company::factory()->create(['status' => 'active']);
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'status' => 'active'
        ]);
        
        $candidateUser = User::factory()->create(['role' => 'candidate']);
        $candidate = Candidate::factory()->create(['user_id' => $candidateUser->id]);
        
        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'pending'
        ]);
        
        // Create another company user that doesn't own this application
        $anotherUser = User::factory()->create(['role' => 'company']);
        $anotherCompany = Company::factory()->create(['user_id' => $anotherUser->id]);
        
        $this->actingAs($anotherUser);
        
        $response = $this->get(route('applications.show', $application->id));
        
        $response->assertStatus(403);
    }
} 