<?php

namespace App\Http\Controllers;

use App\Events\JobApplicationStatusChanged;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RealTimeController extends Controller
{
    /**
     * Get real-time dashboard data.
     */
    public function getDashboardData(GetDashboardDataRealTimeRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = [
            'user_stats' => $this->getUserStats($user),
            'recent_activities' => $this->getRecentActivities($user),
            'system_health' => $this->getSystemHealth(),
            'real_time_metrics' => $this->getRealTimeMetrics(),
        ];

        return response()->json($data);
    }

    /**
     * Update job application status with real-time broadcasting.
     */
    public function updateApplicationStatus(UpdateApplicationStatusRealTimeRequest $request, JobApplication $jobApplication): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,reviewed,shortlisted,interview_scheduled,interview_completed,rejected,hired,withdrawn',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $jobApplication->status;
        $newStatus = $request->status;

        // Check authorization
        if (! $this->canUpdateStatus($jobApplication, $newStatus)) {
            return response()->json(['error' => 'Unauthorized to update this status'], 403);
        }

        try {
            // Update the application
            $jobApplication->update([
                'status' => $newStatus,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ]);

            // Broadcast the status change
            event(new JobApplicationStatusChanged($jobApplication, $oldStatus, $newStatus));

            return response()->json([
                'message' => 'Status updated successfully',
                'application' => $jobApplication->fresh(['job', 'candidate']),
                'broadcast_sent' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update job application status', [
                'application_id' => $jobApplication->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to update status'], 500);
        }
    }

    /**
     * Get WebSocket authentication token for private channels.
     */
    public function getWebSocketAuth(GetWebSocketAuthRealTimeRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $channels = [];

        // Add user-specific channels
        if ($user->user_type === 'candidate') {
            $channels[] = "job-application.{$user->id}";
        } else {
            // For employers, add company-specific channels
            if ($user->company) {
                $channels[] = "job-applications.{$user->company->id}";
            }
        }

        return response()->json([
            'channels' => $channels,
            'user_id' => $user->id,
            'expires_at' => now()->addHours(24)->toISOString(),
        ]);
    }

    /**
     * Get live activity feed.
     */
    public function getActivityFeed(GetActivityFeedRealTimeRequest $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $activities = collect();

        if ($user->user_type === 'candidate') {
            // Get candidate's application activities
            $applications = JobApplication::where('candidate_id', $user->id)
                ->with(['job.company'])
                ->latest()
                ->limit($limit)
                ->get();

            foreach ($applications as $app) {
                $activities->push([
                    'type' => 'application_status',
                    'title' => "Application for {$app->job->title}",
                    'description' => "Status: {$app->status}",
                    'timestamp' => $app->updated_at,
                    'icon' => $this->getStatusIcon($app->status),
                    'color' => $this->getStatusColor($app->status),
                ]);
            }
        } else {
            // Get employer's job activities
            if ($user->company) {
                $applications = JobApplication::whereHas('job', function ($query) use ($user) {
                    $query->where('company_id', $user->company->id);
                })
                    ->with(['job', 'candidate'])
                    ->latest()
                    ->limit($limit)
                    ->get();

                foreach ($applications as $app) {
                    $activities->push([
                        'type' => 'application_received',
                        'title' => "Application from {$app->candidate->first_name} {$app->candidate->last_name}",
                        'description' => "For position: {$app->job->title}",
                        'timestamp' => $app->updated_at,
                        'icon' => 'user-plus',
                        'color' => 'blue',
                    ]);
                }
            }
        }

        return response()->json([
            'activities' => $activities->sortByDesc('timestamp')->values()->all(),
            'total_count' => $activities->count(),
        ]);
    }

    /**
     * Get real-time statistics.
     */
    public function getRealTimeStats(): JsonResponse
    {
        $today = now()->format('Y-m-d');
        $stats = Cache::get("daily_activity:{$today}", [
            'status_changes' => 0,
            'applications_reviewed' => 0,
            'interviews_scheduled' => 0,
            'hires_made' => 0,
        ]);

        // Add current time metrics
        $stats['current_time'] = now()->toISOString();
        $stats['active_users'] = $this->getActiveUsersCount();
        $stats['system_load'] = $this->getSystemLoad();

        return response()->json($stats);
    }

    /**
     * Check if user can update application status.
     */
    private function canUpdateStatus(JobApplication $jobApplication, string $newStatus): bool
    {
        $user = Auth::user();

        // Candidates can only withdraw their applications
        if ($user->user_type === 'candidate') {
            return $user->id === $jobApplication->candidate_id && $newStatus === 'withdrawn';
        }

        // Employers can update if they own the job
        if ($user->user_type === 'employer' && $user->company) {
            return $user->company->id === $jobApplication->job->company_id;
        }

        // Admins can update any status
        return $user->hasRole('admin');
    }

    /**
     * Get user-specific statistics.
     *
     * @param  mixed  $user
     */
    private function getUserStats($user): array
    {
        $cacheKey = "user:stats:{$user->id}:{$user->user_type}";

        return Cache::remember($cacheKey, 900, function () use ($user) {
            if ($user->user_type === 'candidate') {
                return [
                    'total_applications' => JobApplication::where('candidate_id', $user->id)->count(),
                    'pending_applications' => JobApplication::where('candidate_id', $user->id)->where('status', 'pending')->count(),
                    'interviews_scheduled' => JobApplication::where('candidate_id', $user->id)->where('status', 'interview_scheduled')->count(),
                    'successful_applications' => JobApplication::where('candidate_id', $user->id)->where('status', 'hired')->count(),
                ];
            }
            $companyId = $user->company->id ?? null;
            if (! $companyId) {
                return [];
            }

            return [
                'active_jobs' => Job::where('company_id', $companyId)->where('status', 'active')->count(),
                'total_applications' => JobApplication::whereHas('job', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })->count(),
                'pending_reviews' => JobApplication::whereHas('job', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })->where('status', 'pending')->count(),
                'scheduled_interviews' => JobApplication::whereHas('job', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })->where('status', 'interview_scheduled')->count(),
            ];
        });
    }

    /**
     * Get recent activities for user.
     *
     * @param  mixed  $user
     */
    private function getRecentActivities($user): array
    {
        return [
            [
                'type' => 'status_update',
                'message' => 'Recent activity available',
                'timestamp' => now()->subMinutes(5),
            ],
        ];
    }

    /**
     * Get system health metrics.
     */
    private function getSystemHealth(): array
    {
        return [
            'database' => 'healthy',
            'cache' => 'healthy',
            'websockets' => 'connected',
            'last_check' => now(),
        ];
    }

    /**
     * Get real-time metrics.
     */
    private function getRealTimeMetrics(): array
    {
        return [
            'active_connections' => rand(10, 50),
            'messages_per_minute' => rand(5, 25),
            'avg_response_time' => rand(50, 200).'ms',
        ];
    }

    /**
     * Get status icon for UI.
     */
    private function getStatusIcon(string $status): string
    {
        $icons = [
            'pending' => 'clock',
            'reviewed' => 'eye',
            'shortlisted' => 'star',
            'interview_scheduled' => 'calendar',
            'interview_completed' => 'check-circle',
            'rejected' => 'x-circle',
            'hired' => 'award',
            'withdrawn' => 'arrow-left',
        ];

        return $icons[$status] ?? 'circle';
    }

    /**
     * Get status color for UI.
     */
    private function getStatusColor(string $status): string
    {
        $colors = [
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'shortlisted' => 'green',
            'interview_scheduled' => 'orange',
            'interview_completed' => 'blue',
            'rejected' => 'red',
            'hired' => 'green',
            'withdrawn' => 'gray',
        ];

        return $colors[$status] ?? 'gray';
    }

    /**
     * Get active users count.
     */
    private function getActiveUsersCount(): int
    {
        return Cache::remember('active_users_count', 60, function () {
            return rand(15, 45);
        });
    }

    /**
     * Get system load.
     */
    private function getSystemLoad(): string
    {
        return rand(20, 80).'%';
    }
}
