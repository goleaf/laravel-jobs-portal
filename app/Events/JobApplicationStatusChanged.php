<?php

namespace App\Events;

use App\Models\JobApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobApplicationStatusChanged implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $jobApplication;
    public $oldStatus;
    public $newStatus;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(JobApplication $jobApplication, string $oldStatus, string $newStatus)
    {
        $this->jobApplication = $jobApplication;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->message = $this->generateStatusMessage($oldStatus, $newStatus);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('job-application.'.$this->jobApplication->candidate_id),
            new PrivateChannel('job-applications.'.$this->jobApplication->job->company_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'application_id' => $this->jobApplication->id,
            'job_title' => $this->jobApplication->job->title,
            'company_name' => $this->jobApplication->job->company->name,
            'candidate_name' => $this->jobApplication->candidate->first_name.' '.$this->jobApplication->candidate->last_name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
            'notification_type' => $this->getNotificationType($this->newStatus),
        ];
    }

    /**
     * Generate human-readable status message.
     */
    private function generateStatusMessage(string $oldStatus, string $newStatus): string
    {
        $messages = [
            'pending' => 'Your application is under review',
            'reviewed' => 'Your application has been reviewed',
            'shortlisted' => 'Congratulations! You have been shortlisted',
            'interview_scheduled' => 'Interview has been scheduled',
            'interview_completed' => 'Interview completed',
            'rejected' => 'Application was not successful this time',
            'hired' => 'Congratulations! You have been hired',
            'withdrawn' => 'Application has been withdrawn',
        ];

        return $messages[$newStatus] ?? 'Application status updated';
    }

    /**
     * Get notification type for UI styling.
     */
    private function getNotificationType(string $status): string
    {
        $types = [
            'pending' => 'info',
            'reviewed' => 'info',
            'shortlisted' => 'success',
            'interview_scheduled' => 'warning',
            'interview_completed' => 'info',
            'rejected' => 'danger',
            'hired' => 'success',
            'withdrawn' => 'secondary',
        ];

        return $types[$status] ?? 'info';
    }
}
