<?php

namespace App\Actions;

use App\Models\JobApplication;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Concerns\IsDispatchable;
use LumoSolutions\Actionable\Concerns\IsRunnable;

class SendJobApplicationNotification
{
    use IsRunnable, IsDispatchable;

    /**
     * Send job application notification to employer or candidate
     */
    public function handle(JobApplication $application, string $recipient = 'employer'): void
    {
        try {
            match($recipient) {
                'employer' => $this->sendEmployerNotification($application),
                'candidate' => $this->sendCandidateNotification($application),
                default => throw new \InvalidArgumentException("Invalid recipient: {$recipient}")
            };

            Log::info('Job application notification sent', [
                'application_id' => $application->id,
                'recipient' => $recipient,
                'job_title' => $application->job->job_title
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send job application notification', [
                'application_id' => $application->id,
                'recipient' => $recipient,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Send notification to employer about new application
     */
    private function sendEmployerNotification(JobApplication $application): void
    {
        $job = $application->job;
        $company = $job->company;
        $candidate = $application->candidate;

        // Get notification email (job contact or company default)
        $notificationEmail = $job->contact_email ?? 
                           $job->application_email ?? 
                           $company->email;

        if (!$notificationEmail) {
            Log::warning('No notification email found for employer', [
                'job_id' => $job->id,
                'company_id' => $company->id
            ]);
            return;
        }

        // Prepare notification data
        $notificationData = [
            'job_title' => $job->job_title,
            'candidate_name' => $candidate->first_name . ' ' . $candidate->last_name,
            'candidate_email' => $candidate->email,
            'application_date' => $application->applied_at->format('M d, Y'),
            'cover_letter' => $application->cover_letter,
            'expected_salary' => $application->expected_salary,
            'company_name' => $company->name,
            'application_url' => route('employer.applications.show', $application->id),
            'resume_url' => $application->resume_path ? asset($application->resume_path) : null
        ];

        // Send email using appropriate mail class
        Mail::to($notificationEmail)
            ->send(new \App\Mail\JobApplicationReceived($notificationData));
    }

    /**
     * Send acknowledgment notification to candidate
     */
    private function sendCandidateNotification(JobApplication $application): void
    {
        $job = $application->job;
        $company = $job->company;
        $candidate = $application->candidate;

        // Check if candidate wants to receive notifications
        if (!$candidate->settings('notifications.application_updates', true)) {
            return;
        }

        // Prepare notification data
        $notificationData = [
            'candidate_name' => $candidate->first_name . ' ' . $candidate->last_name,
            'job_title' => $job->job_title,
            'company_name' => $company->name,
            'application_date' => $application->applied_at->format('M d, Y'),
            'application_status' => $application->getStatusDisplayName(),
            'job_url' => route('jobs.show', $job->slug ?? $job->id),
            'application_tracking_url' => route('candidate.applications.show', $application->id)
        ];

        // Send confirmation email
        Mail::to($candidate->email)
            ->send(new \App\Mail\JobApplicationConfirmation($notificationData));

        // Send SMS notification if enabled and phone number available
        if ($candidate->phone && 
            $candidate->settings('notifications.sms_enabled', false) &&
            $application->settings('notifications.candidate_notifications.application_received', true)) {
            
            $this->sendSMSNotification($candidate->phone, $notificationData);
        }
    }

    /**
     * Send SMS notification (placeholder for SMS service integration)
     */
    private function sendSMSNotification(string $phoneNumber, array $data): void
    {
        // Integrate with SMS service (Twilio, AWS SNS, etc.)
        Log::info('SMS notification queued', [
            'phone' => $phoneNumber,
            'job_title' => $data['job_title']
        ]);
    }
}
