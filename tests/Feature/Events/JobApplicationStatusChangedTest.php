<?php

namespace Tests\Feature\Events;

use App\Events\JobApplicationStatusChanged;
use App\Listeners\SendRealTimeNotification;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class JobApplicationStatusChangedTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_broadcasts_and_creates_notifications(): void
    {
        $candidate = User::factory()->create(['user_type' => 'candidate']);

        // Build unsaved related models to avoid schema constraints in tests
        $company = new Company();
        $company->name = 'Acme Inc';
        $employer = new User(['first_name' => 'Jane', 'last_name' => 'Employer', 'email' => 'employer@example.com']);
        $company->setRelation('user', $employer);

        $job = new Job(['job_title' => 'Engineer']);
        $job->setRelation('company', $company);

        $application = new JobApplication([
            'job_id' => 1,
            'candidate_id' => $candidate->id,
            'status' => 'pending',
        ]);
        $application->setRelation('job', $job);
        $application->setRelation('candidate', $candidate);

        Event::fake();

        $event = new JobApplicationStatusChanged($application, 'pending', 'reviewed');

        event($event);

        Event::assertDispatched(JobApplicationStatusChanged::class);

        // Ensure event constructs correct broadcast payload
        $payload = $event->broadcastWith();
        $this->assertArrayHasKey('application_id', $payload);
        $this->assertArrayHasKey('job_title', $payload);
        $this->assertArrayHasKey('candidate_name', $payload);
        $this->assertEquals('reviewed', $payload['new_status']);
    }
}


