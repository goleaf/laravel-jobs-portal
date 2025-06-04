<?php

namespace Tests\Feature;

use App\Events\JobApplicationStatusChanged;
use App\Models\User;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RealTimeTrackingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users with minimal required fields
        $this->candidate = User::create([
            'first_name' => 'John',
            'last_name' => 'Candidate', 
            'email' => 'candidate@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'candidate'
        ]);
        
        $this->employer = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Employer',
            'email' => 'employer@test.com', 
            'password' => bcrypt('password'),
            'user_type' => 'employer'
        ]);
        
        // Create test company with minimal required fields
        $this->company = Company::create([
            'user_id' => $this->employer->id,
            'name' => 'Test Company'
        ]);
        
        // Create test job with minimal required fields
        $this->job = Job::create([
            'company_id' => $this->company->id,
            'title' => 'Test Job Position'
        ]);
        
        // Create test application with minimal required fields
        $this->application = JobApplication::create([
            'job_id' => $this->job->id,
            'candidate_id' => $this->candidate->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function authenticated_user_can_access_realtime_dashboard()
    {
        $response = $this->actingAs($this->candidate)
            ->get('/realtime/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user_stats',
            'recent_activities',
            'system_health',
            'real_time_metrics'
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_realtime_dashboard()
    {
        $response = $this->get('/realtime/dashboard');
        $response->assertStatus(302); // Redirect to login
    }

    /** @test */
    public function candidate_can_get_their_stats()
    {
        $response = $this->actingAs($this->candidate)
            ->get('/realtime/dashboard');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('total_applications', $data['user_stats']);
        $this->assertArrayHasKey('pending_applications', $data['user_stats']);
        $this->assertArrayHasKey('interviews_scheduled', $data['user_stats']);
        $this->assertArrayHasKey('successful_applications', $data['user_stats']);
    }

    /** @test */
    public function employer_can_get_their_stats()
    {
        $response = $this->actingAs($this->employer)
            ->get('/realtime/dashboard');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('active_jobs', $data['user_stats']);
        $this->assertArrayHasKey('total_applications', $data['user_stats']);
        $this->assertArrayHasKey('pending_reviews', $data['user_stats']);
        $this->assertArrayHasKey('scheduled_interviews', $data['user_stats']);
    }

    /** @test */
    public function employer_can_update_application_status()
    {
        Event::fake();

        $response = $this->actingAs($this->employer)
            ->postJson("/realtime/applications/{$this->application->id}/status", [
                'status' => 'reviewed',
                'notes' => 'Application has been reviewed'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Status updated successfully',
            'broadcast_sent' => true
        ]);

        // Verify the status was updated in database
        $this->application->refresh();
        $this->assertEquals('reviewed', $this->application->status);
        $this->assertEquals('Application has been reviewed', $this->application->notes);

        // Verify the event was dispatched
        Event::assertDispatched(JobApplicationStatusChanged::class, function ($event) {
            return $event->jobApplication->id === $this->application->id
                && $event->oldStatus === 'pending'
                && $event->newStatus === 'reviewed';
        });
    }

    /** @test */
    public function candidate_can_only_withdraw_their_own_application()
    {
        $response = $this->actingAs($this->candidate)
            ->postJson("/realtime/applications/{$this->application->id}/status", [
                'status' => 'withdrawn'
            ]);

        $response->assertStatus(200);
        
        $this->application->refresh();
        $this->assertEquals('withdrawn', $this->application->status);
    }

    /** @test */
    public function candidate_cannot_update_application_to_other_statuses()
    {
        $response = $this->actingAs($this->candidate)
            ->postJson("/realtime/applications/{$this->application->id}/status", [
                'status' => 'hired'
            ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Unauthorized to update this status']);
    }

    /** @test */
    public function unauthorized_user_cannot_update_application_status()
    {
        $otherUser = User::factory()->create(['user_type' => 'candidate']);
        
        $response = $this->actingAs($otherUser)
            ->postJson("/realtime/applications/{$this->application->id}/status", [
                'status' => 'reviewed'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function websocket_auth_returns_correct_channels_for_candidate()
    {
        $response = $this->actingAs($this->candidate)
            ->get('/realtime/websocket-auth');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertContains("job-application.{$this->candidate->id}", $data['channels']);
        $this->assertEquals($this->candidate->id, $data['user_id']);
    }

    /** @test */
    public function websocket_auth_returns_correct_channels_for_employer()
    {
        $response = $this->actingAs($this->employer)
            ->get('/realtime/websocket-auth');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertContains("job-applications.{$this->company->id}", $data['channels']);
        $this->assertEquals($this->employer->id, $data['user_id']);
    }

    /** @test */
    public function activity_feed_returns_candidate_applications()
    {
        $response = $this->actingAs($this->candidate)
            ->get('/realtime/activity');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('activities', $data);
        $this->assertArrayHasKey('total_count', $data);
        
        if (count($data['activities']) > 0) {
            $this->assertArrayHasKey('type', $data['activities'][0]);
            $this->assertArrayHasKey('title', $data['activities'][0]);
            $this->assertArrayHasKey('description', $data['activities'][0]);
        }
    }

    /** @test */
    public function activity_feed_returns_employer_applications()
    {
        $response = $this->actingAs($this->employer)
            ->get('/realtime/activity');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('activities', $data);
        $this->assertArrayHasKey('total_count', $data);
    }

    /** @test */
    public function real_time_stats_returns_current_metrics()
    {
        $response = $this->actingAs($this->candidate)
            ->get('/realtime/stats');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('status_changes', $data);
        $this->assertArrayHasKey('applications_reviewed', $data);
        $this->assertArrayHasKey('interviews_scheduled', $data);
        $this->assertArrayHasKey('hires_made', $data);
        $this->assertArrayHasKey('current_time', $data);
        $this->assertArrayHasKey('active_users', $data);
        $this->assertArrayHasKey('system_load', $data);
    }

    /** @test */
    public function job_application_status_changed_event_has_correct_data()
    {
        $event = new JobApplicationStatusChanged($this->application, 'pending', 'reviewed');

        $this->assertEquals($this->application->id, $event->jobApplication->id);
        $this->assertEquals('pending', $event->oldStatus);
        $this->assertEquals('reviewed', $event->newStatus);
        $this->assertNotNull($event->message);
    }

    /** @test */
    public function event_broadcast_data_contains_required_fields()
    {
        $event = new JobApplicationStatusChanged($this->application, 'pending', 'shortlisted');
        $broadcastData = $event->broadcastWith();

        $requiredFields = [
            'application_id',
            'job_title',
            'company_name',
            'candidate_name',
            'old_status',
            'new_status',
            'message',
            'timestamp',
            'notification_type'
        ];

        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey($field, $broadcastData);
        }
    }

    /** @test */
    public function event_broadcasts_on_correct_channels()
    {
        $event = new JobApplicationStatusChanged($this->application, 'pending', 'reviewed');
        $channels = $event->broadcastOn();

        $this->assertCount(2, $channels);
        
        $channelNames = array_map(function ($channel) {
            return $channel->name;
        }, $channels);
        
        $this->assertContains("job-application.{$this->candidate->id}", $channelNames);
        $this->assertContains("job-applications.{$this->company->id}", $channelNames);
    }

    /** @test */
    public function status_validation_rejects_invalid_statuses()
    {
        $response = $this->actingAs($this->employer)
            ->postJson("/realtime/applications/{$this->application->id}/status", [
                'status' => 'invalid_status'
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function dashboard_page_loads_successfully()
    {
        $response = $this->actingAs($this->candidate)
            ->get('/dashboard/realtime');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.realtime');
    }

    /** @test */
    public function system_health_endpoint_returns_status()
    {
        $response = $this->actingAs($this->candidate)
            ->get('/realtime/dashboard');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('system_health', $data);
        $this->assertArrayHasKey('database', $data['system_health']);
        $this->assertArrayHasKey('cache', $data['system_health']);
        $this->assertArrayHasKey('websockets', $data['system_health']);
    }
}
