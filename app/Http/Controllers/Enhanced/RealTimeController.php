<?php

namespace App\Http\Controllers\Enhanced;

use App\Http\Controllers\AppBaseController;
use App\Events\JobApplicationStatusChanged;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

/**
 * Enhanced RealTimeController - Context7 patterns implementation
 * 
 * Demonstrates modern Laravel real-time controller patterns with:
 * - Advanced WebSocket management
 * - Real-time analytics and monitoring
 * - Performance optimization for live data
 * - Enhanced caching for real-time metrics
 * - Comprehensive error handling
 * - Activity feed management
 * - System health monitoring
 */
class RealTimeController extends AppBaseController
{
    /**
     * Cache TTL for real-time data (5 minutes for frequent updates)
     */
    private const CACHE_TTL = 300;

    /**
     * Cache TTL for dashboard data (2 minutes for live updates)
     */
    private const DASHBOARD_CACHE_TTL = 120;

    /**
     * Get real-time dashboard data with enhanced caching and performance
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return $this->sendError('Unauthorized access', 401);
            }

            $cacheKey = $this->buildCacheKey('dashboard.data', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'timestamp' => now()->format('Y-m-d-H-i')
            ]);

            $data = Cache::remember($cacheKey, self::DASHBOARD_CACHE_TTL, function () use ($user) {
                return [
                    'user_stats' => $this->getUserStats($user),
                    'recent_activities' => $this->getRecentActivities($user),
                    'system_health' => $this->getSystemHealth(),
                    'real_time_metrics' => $this->getRealTimeMetrics(),
                    'performance_metrics' => $this->getPerformanceMetrics(),
                    'notification_count' => $this->getUnreadNotificationCount($user),
                    'last_updated' => now()->toISOString()
                ];
            });

            // Add real-time timestamp
            $data['server_time'] = now()->toISOString();
            $data['cache_status'] = 'hit';

            return $this->sendResponse($data, 'Dashboard data retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error retrieving dashboard data', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->sendServerError('Failed to retrieve dashboard data');
        }
    }

    /**
     * Update job application status with real-time broadcasting and enhanced validation
     */
    public function updateApplicationStatus(Request $request, JobApplication $jobApplication): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,reviewed,shortlisted,interview_scheduled,interview_completed,rejected,hired,withdrawn',
            'notes' => 'nullable|string|max:1000',
            'notify_candidate' => 'boolean',
            'schedule_interview' => 'boolean',
            'interview_date' => 'nullable|date|after:now',
            'interview_notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $oldStatus = $jobApplication->status;
            $newStatus = $request->status;

            // Enhanced authorization check
            if (!$this->canUpdateStatus($jobApplication, $newStatus, $user)) {
                return $this->sendError('Unauthorized to update this status', 403);
            }

            // Validate status transition
            if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
                return $this->sendError('Invalid status transition', 400);
            }

            // Update the application with enhanced data
            $updateData = [
                'status' => $newStatus,
                'notes' => $request->notes,
                'updated_by' => $user->id,
                'status_changed_at' => now(),
                'previous_status' => $oldStatus
            ];

            // Handle interview scheduling
            if ($request->boolean('schedule_interview') && $request->filled('interview_date')) {
                $updateData['interview_scheduled_at'] = $request->interview_date;
                $updateData['interview_notes'] = $request->interview_notes;
            }

            $jobApplication->update($updateData);

            // Clear related caches
            $this->clearRealTimeCaches($jobApplication);

            // Broadcast the status change with enhanced data
            $broadcastData = [
                'application' => $jobApplication->fresh(['job', 'candidate.user', 'company']),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by' => $user->only(['id', 'name', 'email']),
                'timestamp' => now()->toISOString(),
                'notify_candidate' => $request->boolean('notify_candidate', true)
            ];

            event(new JobApplicationStatusChanged($broadcastData));

            // Update real-time statistics
            $this->updateRealTimeStats('status_change', $newStatus);

            // Log the status change
            Log::info('Job application status updated via real-time', [
                'application_id' => $jobApplication->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by' => $user->id,
                'job_id' => $jobApplication->job_id,
                'candidate_id' => $jobApplication->candidate_id
            ]);

            DB::commit();

            return $this->sendResponse([
                'application' => $jobApplication->fresh(['job', 'candidate.user']),
                'broadcast_sent' => true,
                'status_transition' => [
                    'from' => $oldStatus,
                    'to' => $newStatus,
                    'timestamp' => now()->toISOString()
                ]
            ], 'Status updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update job application status', [
                'application_id' => $jobApplication->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->sendServerError('Failed to update status');
        }
    }

    /**
     * Get WebSocket authentication token for private channels with enhanced security
     */
    public function getWebSocketAuth(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return $this->sendError('Unauthorized access', 401);
            }

            $channels = [];
            $permissions = [];
            
            // Add user-specific channels based on role
            switch ($user->user_type) {
                case 'candidate':
                    $channels[] = "job-application.{$user->id}";
                    $channels[] = "notifications.{$user->id}";
                    $permissions = ['read', 'subscribe'];
                    break;

                case 'employer':
                    if ($user->company) {
                        $channels[] = "job-applications.{$user->company->id}";
                        $channels[] = "company-notifications.{$user->company->id}";
                        $channels[] = "real-time-stats.{$user->company->id}";
                        $permissions = ['read', 'write', 'subscribe'];
                    }
                    break;

                case 'admin':
                    $channels[] = "admin-dashboard";
                    $channels[] = "system-health";
                    $channels[] = "global-stats";
                    $permissions = ['read', 'write', 'admin', 'subscribe'];
                    break;
            }

            // Generate secure token
            $token = $this->generateWebSocketToken($user, $channels);

            // Cache the token for validation
            Cache::put("websocket_token:{$token}", [
                'user_id' => $user->id,
                'channels' => $channels,
                'permissions' => $permissions,
                'expires_at' => now()->addHours(24)
            ], now()->addHours(24));

            return $this->sendResponse([
                'token' => $token,
                'channels' => $channels,
                'permissions' => $permissions,
                'user_id' => $user->id,
                'expires_at' => now()->addHours(24)->toISOString(),
                'server_time' => now()->toISOString()
            ], 'WebSocket authentication token generated');

        } catch (\Exception $e) {
            Log::error('Error generating WebSocket auth token', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to generate authentication token');
        }
    }

    /**
     * Get live activity feed with enhanced filtering and pagination
     */
    public function getActivityFeed(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $limit = min($request->get('limit', 20), 100); // Cap at 100
            $offset = $request->get('offset', 0);
            $filter = $request->get('filter', 'all'); // all, applications, interviews, hires

            $cacheKey = $this->buildCacheKey('activity.feed', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'limit' => $limit,
                'offset' => $offset,
                'filter' => $filter,
                'timestamp' => now()->format('Y-m-d-H-i')
            ]);

            $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $limit, $offset, $filter) {
                $activities = collect();

                if ($user->user_type === 'candidate') {
                    $activities = $this->getCandidateActivities($user, $limit, $offset, $filter);
                } elseif ($user->user_type === 'employer') {
                    $activities = $this->getEmployerActivities($user, $limit, $offset, $filter);
                } elseif ($user->user_type === 'admin') {
                    $activities = $this->getAdminActivities($user, $limit, $offset, $filter);
                }

                return [
                    'activities' => $activities->values()->all(),
                    'total_count' => $activities->count(),
                    'has_more' => $activities->count() === $limit
                ];
            });

            return $this->sendResponse($data, 'Activity feed retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error retrieving activity feed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to retrieve activity feed');
        }
    }

    /**
     * Get real-time statistics with enhanced metrics
     */
    public function getRealTimeStats(Request $request): JsonResponse
    {
        try {
            $timeframe = $request->get('timeframe', 'today'); // today, week, month
            $cacheKey = $this->buildCacheKey('realtime.stats', [
                'timeframe' => $timeframe,
                'timestamp' => now()->format('Y-m-d-H')
            ]);

            $stats = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($timeframe) {
                $baseStats = $this->getBaseStats($timeframe);
                
                return array_merge($baseStats, [
                    'current_time' => now()->toISOString(),
                    'active_users' => $this->getActiveUsersCount(),
                    'system_load' => $this->getSystemLoad(),
                    'websocket_connections' => $this->getWebSocketConnections(),
                    'cache_hit_ratio' => $this->getCacheHitRatio(),
                    'response_time_avg' => $this->getAverageResponseTime(),
                    'error_rate' => $this->getErrorRate()
                ]);
            });

            return $this->sendResponse($stats, 'Real-time statistics retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error retrieving real-time stats', [
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to retrieve statistics');
        }
    }

    /**
     * Get system health status with comprehensive monitoring
     */
    public function getSystemHealth(): JsonResponse
    {
        try {
            $cacheKey = 'system.health.' . now()->format('Y-m-d-H-i');

            $health = Cache::remember($cacheKey, 60, function () { // 1 minute cache
                return [
                    'database' => $this->checkDatabaseHealth(),
                    'redis' => $this->checkRedisHealth(),
                    'storage' => $this->checkStorageHealth(),
                    'queue' => $this->checkQueueHealth(),
                    'websocket' => $this->checkWebSocketHealth(),
                    'memory_usage' => $this->getMemoryUsage(),
                    'cpu_usage' => $this->getCpuUsage(),
                    'disk_usage' => $this->getDiskUsage(),
                    'uptime' => $this->getSystemUptime(),
                    'last_check' => now()->toISOString()
                ];
            });

            $overallStatus = $this->calculateOverallHealth($health);

            return $this->sendResponse([
                'status' => $overallStatus,
                'components' => $health,
                'timestamp' => now()->toISOString()
            ], 'System health retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error retrieving system health', [
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to retrieve system health');
        }
    }

    /**
     * Broadcast message to specific channels
     */
    public function broadcastMessage(Request $request): JsonResponse
    {
        $request->validate([
            'channel' => 'required|string',
            'event' => 'required|string',
            'data' => 'required|array',
            'target_users' => 'nullable|array',
            'target_users.*' => 'integer|exists:users,id'
        ]);

        try {
            $user = Auth::user();

            // Check if user has permission to broadcast
            if (!$this->canBroadcast($user, $request->channel)) {
                return $this->sendError('Unauthorized to broadcast to this channel', 403);
            }

            $broadcastData = [
                'event' => $request->event,
                'data' => $request->data,
                'sender' => $user->only(['id', 'name', 'user_type']),
                'timestamp' => now()->toISOString(),
                'channel' => $request->channel
            ];

            // Broadcast the message
            broadcast(new \App\Events\CustomBroadcast($request->channel, $broadcastData));

            // Log the broadcast
            Log::info('Message broadcasted', [
                'channel' => $request->channel,
                'event' => $request->event,
                'sender_id' => $user->id,
                'target_users' => $request->target_users ?? 'all'
            ]);

            return $this->sendResponse([
                'broadcast_sent' => true,
                'channel' => $request->channel,
                'timestamp' => now()->toISOString()
            ], 'Message broadcasted successfully');

        } catch (\Exception $e) {
            Log::error('Error broadcasting message', [
                'channel' => $request->channel ?? null,
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to broadcast message');
        }
    }

    /**
     * Enhanced authorization check for status updates
     */
    private function canUpdateStatus(JobApplication $jobApplication, string $newStatus, User $user): bool
    {
        // Candidates can only withdraw their applications
        if ($user->user_type === 'candidate') {
            return $user->id === $jobApplication->candidate_id && $newStatus === 'withdrawn';
        }

        // Employers can update applications for their jobs
        if ($user->user_type === 'employer') {
            return $jobApplication->job->company_id === $user->company?->id;
        }

        // Admins can update any application
        if ($user->user_type === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Validate status transitions
     */
    private function isValidStatusTransition(string $oldStatus, string $newStatus): bool
    {
        $validTransitions = [
            'pending' => ['reviewed', 'shortlisted', 'rejected', 'withdrawn'],
            'reviewed' => ['shortlisted', 'interview_scheduled', 'rejected'],
            'shortlisted' => ['interview_scheduled', 'hired', 'rejected'],
            'interview_scheduled' => ['interview_completed', 'rejected', 'hired'],
            'interview_completed' => ['hired', 'rejected'],
            'rejected' => [], // Final state
            'hired' => [], // Final state
            'withdrawn' => [] // Final state
        ];

        return in_array($newStatus, $validTransitions[$oldStatus] ?? []);
    }

    /**
     * Generate secure WebSocket token
     */
    private function generateWebSocketToken(User $user, array $channels): string
    {
        return hash('sha256', $user->id . implode(',', $channels) . now()->timestamp . config('app.key'));
    }

    /**
     * Get candidate activities
     */
    private function getCandidateActivities(User $user, int $limit, int $offset, string $filter): \Illuminate\Support\Collection
    {
        $query = JobApplication::where('candidate_id', $user->id)
                              ->with(['job.company'])
                              ->latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $applications = $query->offset($offset)->limit($limit)->get();

        return $applications->map(function ($app) {
            return [
                'id' => $app->id,
                'type' => 'application_status',
                'title' => "Application for {$app->job->job_title}",
                'description' => "Status: " . ucfirst(str_replace('_', ' ', $app->status)),
                'timestamp' => $app->updated_at,
                'icon' => $this->getStatusIcon($app->status),
                'color' => $this->getStatusColor($app->status),
                'job_id' => $app->job_id,
                'company' => $app->job->company->company_name ?? 'Unknown Company'
            ];
        });
    }

    /**
     * Get employer activities
     */
    private function getEmployerActivities(User $user, int $limit, int $offset, string $filter): \Illuminate\Support\Collection
    {
        if (!$user->company) {
            return collect();
        }

        $query = JobApplication::whereHas('job', function ($q) use ($user) {
                    $q->where('company_id', $user->company->id);
                })
                ->with(['job', 'candidate.user'])
                ->latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $applications = $query->offset($offset)->limit($limit)->get();

        return $applications->map(function ($app) {
            return [
                'id' => $app->id,
                'type' => 'application_received',
                'title' => "Application from {$app->candidate->user->first_name} {$app->candidate->user->last_name}",
                'description' => "For position: {$app->job->job_title}",
                'timestamp' => $app->updated_at,
                'icon' => 'user-plus',
                'color' => 'blue',
                'job_id' => $app->job_id,
                'candidate_id' => $app->candidate_id,
                'status' => $app->status
            ];
        });
    }

    /**
     * Get admin activities
     */
    private function getAdminActivities(User $user, int $limit, int $offset, string $filter): \Illuminate\Support\Collection
    {
        // Admin sees system-wide activities
        $query = JobApplication::with(['job.company', 'candidate.user'])
                              ->latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $applications = $query->offset($offset)->limit($limit)->get();

        return $applications->map(function ($app) {
            return [
                'id' => $app->id,
                'type' => 'system_activity',
                'title' => "Application: {$app->candidate->user->first_name} {$app->candidate->user->last_name} → {$app->job->job_title}",
                'description' => "Company: {$app->job->company->company_name} | Status: {$app->status}",
                'timestamp' => $app->updated_at,
                'icon' => 'activity',
                'color' => 'purple',
                'job_id' => $app->job_id,
                'candidate_id' => $app->candidate_id,
                'company_id' => $app->job->company_id
            ];
        });
    }

    /**
     * Update real-time statistics
     */
    private function updateRealTimeStats(string $type, string $value): void
    {
        $today = now()->format('Y-m-d');
        $key = "daily_activity:{$today}";
        
        $stats = Cache::get($key, [
            'status_changes' => 0,
            'applications_reviewed' => 0,
            'interviews_scheduled' => 0,
            'hires_made' => 0,
        ]);

        switch ($type) {
            case 'status_change':
                $stats['status_changes']++;
                if ($value === 'reviewed') $stats['applications_reviewed']++;
                if ($value === 'interview_scheduled') $stats['interviews_scheduled']++;
                if ($value === 'hired') $stats['hires_made']++;
                break;
        }

        Cache::put($key, $stats, now()->endOfDay());
    }

    /**
     * Clear real-time related caches
     */
    private function clearRealTimeCaches(JobApplication $jobApplication): void
    {
        $tags = [
            'dashboard.data',
            'activity.feed',
            'realtime.stats',
            "job_applications.{$jobApplication->job->company_id}"
        ];

        foreach ($tags as $tag) {
            Cache::tags($tag)->flush();
        }
    }

    /**
     * Get various system metrics (placeholder implementations)
     */
    private function getActiveUsersCount(): int
    {
        return Cache::remember('active_users_count', 300, function () {
            return User::where('last_activity_at', '>=', now()->subMinutes(15))->count();
        });
    }

    private function getSystemLoad(): string
    {
        return sys_getloadavg()[0] ?? '0.00';
    }

    private function getWebSocketConnections(): int
    {
        return Cache::get('websocket_connections', 0);
    }

    private function getCacheHitRatio(): float
    {
        return 85.5; // Placeholder - implement based on your cache system
    }

    private function getAverageResponseTime(): float
    {
        return 120.5; // Placeholder - implement based on your monitoring
    }

    private function getErrorRate(): float
    {
        return 0.5; // Placeholder - implement based on your error tracking
    }

    private function getStatusIcon(string $status): string
    {
        return match($status) {
            'pending' => 'clock',
            'reviewed' => 'eye',
            'shortlisted' => 'star',
            'interview_scheduled' => 'calendar',
            'interview_completed' => 'check-circle',
            'hired' => 'user-check',
            'rejected' => 'x-circle',
            'withdrawn' => 'arrow-left',
            default => 'help-circle'
        };
    }

    private function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'shortlisted' => 'purple',
            'interview_scheduled' => 'orange',
            'interview_completed' => 'indigo',
            'hired' => 'green',
            'rejected' => 'red',
            'withdrawn' => 'gray',
            default => 'gray'
        };
    }

    // Placeholder health check methods
    private function checkDatabaseHealth(): array { return ['status' => 'healthy', 'response_time' => '5ms']; }
    private function checkRedisHealth(): array { return ['status' => 'healthy', 'response_time' => '2ms']; }
    private function checkStorageHealth(): array { return ['status' => 'healthy', 'free_space' => '85%']; }
    private function checkQueueHealth(): array { return ['status' => 'healthy', 'pending_jobs' => 12]; }
    private function checkWebSocketHealth(): array { return ['status' => 'healthy', 'connections' => 45]; }
    private function getMemoryUsage(): string { return '65%'; }
    private function getCpuUsage(): string { return '25%'; }
    private function getDiskUsage(): string { return '45%'; }
    private function getSystemUptime(): string { return '15 days'; }
    private function getUserStats($user): array { return ['applications' => 5, 'interviews' => 2]; }
    private function getRecentActivities($user): array { return []; }
    private function getRealTimeMetrics(): array { return ['active_sessions' => 150]; }
    private function getPerformanceMetrics(): array { return ['avg_response' => '120ms']; }
    private function getUnreadNotificationCount($user): int { return 3; }
    private function getBaseStats($timeframe): array { return ['applications' => 100, 'hires' => 15]; }
    private function calculateOverallHealth($health): string { return 'healthy'; }
    private function canBroadcast($user, $channel): bool { return $user->user_type === 'admin'; }
} 