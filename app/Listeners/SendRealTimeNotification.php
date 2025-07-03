<?php

namespace App\Listeners;

use App\Events\JobApplicationStatusChanged;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendRealTimeNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(JobApplicationStatusChanged $event): void
    {
        $this->createNotification($event);
        $this->updateActivityStats($event);
        $this->logStatusChange($event);
        $this->updateDashboardStats($event);
    }

    /**
     * Handle failed job.
     */
    public function failed(JobApplicationStatusChanged $event, \Throwable $exception): void
    {
        Log::error('Failed to send real-time notification', [
            'application_id' => $event->jobApplication->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Create database notification record.
     */
    private function createNotification(JobApplicationStatusChanged $event): void
    {
        try {
            // Create notification for candidate
            Notification::create([
                'user_id' => $event->jobApplication->candidate_id,
                'title' => 'Application Status Update',
                'message' => $event->message,
                'type' => 'job_application_status',
                'data' => json_encode([
                    'application_id' => $event->jobApplication->id,
                    'job_title' => $event->jobApplication->job->title,
                    'company_name' => $event->jobApplication->job->company->name,
                    'old_status' => $event->oldStatus,
                    'new_status' => $event->newStatus,
                ]),
                'read_at' => null,
            ]);

            // Create notification for employer if status is candidate-initiated
            if (in_array($event->newStatus, ['withdrawn'])) {
                $employer = $event->jobApplication->job->company->user;
                if ($employer) {
                    Notification::create([
                        'user_id' => $employer->id,
                        'title' => 'Application Withdrawn',
                        'message' => "Candidate {$event->jobApplication->candidate->first_name} {$event->jobApplication->candidate->last_name} withdrew their application for {$event->jobApplication->job->title}",
                        'type' => 'job_application_status',
                        'data' => json_encode([
                            'application_id' => $event->jobApplication->id,
                            'job_title' => $event->jobApplication->job->title,
                            'candidate_name' => $event->jobApplication->candidate->first_name.' '.$event->jobApplication->candidate->last_name,
                            'old_status' => $event->oldStatus,
                            'new_status' => $event->newStatus,
                        ]),
                        'read_at' => null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to create notification for job application status change', [
                'application_id' => $event->jobApplication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update activity statistics in cache.
     */
    private function updateActivityStats(JobApplicationStatusChanged $event): void
    {
        $today = now()->format('Y-m-d');

        // Update daily activity stats
        $statsKey = "daily_activity:{$today}";
        $stats = Cache::get($statsKey, [
            'status_changes' => 0,
            'applications_reviewed' => 0,
            'interviews_scheduled' => 0,
            'hires_made' => 0,
        ]);

        $stats['status_changes']++;

        switch ($event->newStatus) {
            case 'reviewed':
                $stats['applications_reviewed']++;

                break;

            case 'interview_scheduled':
                $stats['interviews_scheduled']++;

                break;

            case 'hired':
                $stats['hires_made']++;

                break;
        }

        Cache::put($statsKey, $stats, now()->addDays(7));

        // Update company-specific stats
        $companyStatsKey = "company_activity:{$event->jobApplication->job->company_id}:{$today}";
        $companyStats = Cache::get($companyStatsKey, [
            'status_changes' => 0,
            'active_applications' => 0,
        ]);

        $companyStats['status_changes']++;
        Cache::put($companyStatsKey, $companyStats, now()->addDays(30));
    }

    /**
     * Log status change for audit trail.
     */
    private function logStatusChange(JobApplicationStatusChanged $event): void
    {
        Log::info('Job application status changed', [
            'application_id' => $event->jobApplication->id,
            'job_id' => $event->jobApplication->job_id,
            'candidate_id' => $event->jobApplication->candidate_id,
            'company_id' => $event->jobApplication->job->company_id,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'timestamp' => now()->toISOString(),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Update real-time dashboard statistics.
     */
    private function updateDashboardStats(JobApplicationStatusChanged $event): void
    {
        // Clear cached dashboard stats to force refresh
        Cache::forget("user:stats:{$event->jobApplication->candidate_id}:candidate");

        if ($event->jobApplication->job->company->user) {
            Cache::forget("user:stats:{$event->jobApplication->job->company->user->id}:employer");
        }

        // Update global application stats
        Cache::forget('app:stats');
    }
}
