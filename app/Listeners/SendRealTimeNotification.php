<?php

namespace App\Listeners;

use App\Events\JobApplicationStatusChanged;
use App\Models\Notification;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
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
            // Create notification for candidate in our Notification model
            $candidateNotification = Notification::create([
                'user_id' => $event->jobApplication->candidate_id,
                'type' => Notification::TYPE_JOB_APPLICATION,
                'notification_for' => Notification::NOTIFICATION_FOR_CANDIDATE,
                'title' => __('events.application.status_update.title'),
                'text' => __(
                    'events.application.status_update.message',
                    [
                        'job' => $event->jobApplication->job->job_title ?? $event->jobApplication->job->title,
                        'old' => $event->oldStatus,
                        'new' => $event->newStatus,
                    ]
                ),
                'meta' => json_encode([
                    'application_id' => $event->jobApplication->id,
                    'job_title' => $event->jobApplication->job->job_title ?? $event->jobApplication->job->title,
                    'company_name' => $event->jobApplication->job->company->name ?? null,
                    'old_status' => $event->oldStatus,
                    'new_status' => $event->newStatus,
                ]),
                'read_at' => null,
            ]);

            // Send Filament database notification to candidate user if available
            $candidateUser = $event->jobApplication->candidate->user ?? User::find($event->jobApplication->candidate_id);
            if ($candidateUser) {
                FilamentNotification::make()
                    ->title(__('events.application.status_update.title'))
                    ->body($candidateNotification->text)
                    ->color($this->mapStatusToColor($event->newStatus))
                    ->sendToDatabase($candidateUser);
            }

            // Create notification for employer if status is candidate-initiated
            if (in_array($event->newStatus, ['withdrawn'])) {
                $employer = $event->jobApplication->job->company->user;
                if ($employer) {
                    $employerNotification = Notification::create([
                        'user_id' => $employer->id,
                        'type' => Notification::TYPE_JOB_APPLICATION,
                        'notification_for' => Notification::NOTIFICATION_FOR_COMPANY,
                        'title' => __('events.application.withdrawn.title'),
                        'text' => __(
                            'events.application.withdrawn.message',
                            [
                                'candidate' => trim(($event->jobApplication->candidate->first_name ?? '').' '.($event->jobApplication->candidate->last_name ?? '')),
                                'job' => $event->jobApplication->job->job_title ?? $event->jobApplication->job->title,
                            ]
                        ),
                        'meta' => json_encode([
                            'application_id' => $event->jobApplication->id,
                            'job_title' => $event->jobApplication->job->job_title ?? $event->jobApplication->job->title,
                            'candidate_name' => trim(($event->jobApplication->candidate->first_name ?? '').' '.($event->jobApplication->candidate->last_name ?? '')),
                            'old_status' => $event->oldStatus,
                            'new_status' => $event->newStatus,
                        ]),
                        'read_at' => null,
                    ]);

                    FilamentNotification::make()
                        ->title(__('events.application.withdrawn.title'))
                        ->body($employerNotification->text)
                        ->color('warning')
                        ->sendToDatabase($employer);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to create notification for job application status change', [
                'application_id' => $event->jobApplication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function mapStatusToColor(string $status): string
    {
        return match ($status) {
            'shortlisted', 'hired' => 'success',
            'interview_scheduled' => 'warning',
            'rejected' => 'danger',
            default => 'info',
        };
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
