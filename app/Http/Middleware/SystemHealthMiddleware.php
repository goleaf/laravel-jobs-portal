<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Laravel Defibrillator-inspired Middleware
 *
 * Prevents overwhelming the system by monitoring critical operations
 * and blocking requests when the system is under stress
 */
class SystemHealthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $healthLevel = 'basic'): Response
    {
        // Skip health check for health check routes themselves
        if ($request->is('health*') || $request->is('defibrillator*')) {
            return $next($request);
        }

        // Get cached health status
        $healthStatus = Cache::get('defibrillator:last_health_check');

        // If no recent health check, perform basic check
        if (! $healthStatus || Carbon::parse($healthStatus['timestamp'])->diffInMinutes(Carbon::now()) > 10) {
            $this->performQuickHealthCheck();
            $healthStatus = Cache::get('defibrillator:last_health_check');
        }

        // Check system health based on level
        switch ($healthLevel) {
            case 'critical':
                return $this->handleCriticalOperations($request, $next, $healthStatus);
            case 'heavy':
                return $this->handleHeavyOperations($request, $next, $healthStatus);
            case 'basic':
            default:
                return $this->handleBasicOperations($request, $next, $healthStatus);
        }
    }

    /**
     * Handle critical operations (job applications, payments, etc.)
     */
    protected function handleCriticalOperations(Request $request, Closure $next, ?array $healthStatus): Response
    {
        if (! $healthStatus || $healthStatus['status'] === 'critical') {
            Log::warning('Critical operation blocked due to system health issues', [
                'url' => $request->url(),
                'user_id' => $request->user()?->id,
                'health_status' => $healthStatus,
            ]);

            return response()->json([
                'error' => 'System temporarily unavailable for maintenance. Please try again in a few minutes.',
                'status' => 'health_check_failed',
                'retry_after' => 300, // 5 minutes
            ], 503);
        }

        // Add health check headers
        $response = $next($request);
        $response->headers->set('X-System-Health', $healthStatus['status']);
        $response->headers->set('X-Health-Check-Time', $healthStatus['timestamp']);

        return $response;
    }

    /**
     * Handle heavy operations (bulk uploads, reports, etc.)
     */
    protected function handleHeavyOperations(Request $request, Closure $next, ?array $healthStatus): Response
    {
        if (! $healthStatus || $healthStatus['status'] === 'critical') {
            return response()->json([
                'error' => 'Heavy operations temporarily disabled due to system load.',
                'status' => 'health_check_failed',
                'retry_after' => 180, // 3 minutes
            ], 503);
        }

        // Check for specific warnings that should block heavy operations
        if (isset($healthStatus['warnings'])) {
            $blockingWarnings = array_filter($healthStatus['warnings'], function ($warning) {
                return str_contains($warning, 'High memory usage') ||
                       str_contains($warning, 'High queue backlog') ||
                       str_contains($warning, 'Database connection slow');
            });

            if (! empty($blockingWarnings)) {
                Log::info('Heavy operation throttled due to system warnings', [
                    'url' => $request->url(),
                    'warnings' => $blockingWarnings,
                ]);

                return response()->json([
                    'error' => 'System is under heavy load. Please try this operation later.',
                    'status' => 'throttled',
                    'retry_after' => 120, // 2 minutes
                ], 429);
            }
        }

        return $next($request);
    }

    /**
     * Handle basic operations (browsing, searching, etc.)
     */
    protected function handleBasicOperations(Request $request, Closure $next, ?array $healthStatus): Response
    {
        // Only block if system is in critical state
        if (! $healthStatus || $healthStatus['status'] === 'critical') {
            // For web requests, show a maintenance page
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'System maintenance in progress.',
                    'status' => 'maintenance_mode',
                    'retry_after' => 60,
                ], 503);
            } else {
                return response()->view('errors.503-health', [
                    'health_status' => $healthStatus,
                    'retry_after' => 60,
                ], 503);
            }
        }

        // Add performance headers for monitoring
        $response = $next($request);

        if ($healthStatus) {
            $response->headers->set('X-System-Health', $healthStatus['status']);

            // Add performance metrics if available
            if (isset($healthStatus['checks']['database']['connection_time'])) {
                $response->headers->set('X-DB-Response-Time', $healthStatus['checks']['database']['connection_time']);
            }

            if (isset($healthStatus['checks']['resources']['memory_usage_percent'])) {
                $response->headers->set('X-Memory-Usage', $healthStatus['checks']['resources']['memory_usage_percent']);
            }
        }

        return $response;
    }

    /**
     * Perform a quick health check
     */
    protected function performQuickHealthCheck(): void
    {
        try {
            $start = microtime(true);

            // Quick database check
            $dbHealthy = true;
            $connectionTime = 0;

            try {
                $startDb = microtime(true);
                DB::connection()->getPdo();
                $connectionTime = round((microtime(true) - $startDb) * 1000, 2);

                if ($connectionTime > 2000) { // 2 second threshold for quick check
                    $dbHealthy = false;
                }
            } catch (\Exception $e) {
                $dbHealthy = false;
            }

            // Quick queue check
            $queueHealthy = true;
            try {
                $pendingJobs = DB::table('jobs')->count();
                if ($pendingJobs > 500) { // High threshold for quick check
                    $queueHealthy = false;
                }
            } catch (\Exception $e) {
                $queueHealthy = false;
            }

            // Quick memory check
            $memoryHealthy = true;
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = ini_get('memory_limit');
            $memoryLimitBytes = $this->convertToBytes($memoryLimit);
            $memoryUsagePercent = ($memoryUsage / $memoryLimitBytes) * 100;

            if ($memoryUsagePercent > 95) {
                $memoryHealthy = false;
            }

            $overallStatus = ($dbHealthy && $queueHealthy && $memoryHealthy) ? 'healthy' : 'critical';
            $totalTime = round((microtime(true) - $start) * 1000, 2);

            // Cache the quick health check result
            Cache::put('defibrillator:last_health_check', [
                'timestamp' => Carbon::now(),
                'status' => $overallStatus,
                'check_type' => 'quick',
                'check_duration' => $totalTime.'ms',
                'checks' => [
                    'database' => ['status' => $dbHealthy ? 'healthy' : 'critical', 'connection_time' => $connectionTime.'ms'],
                    'queue' => ['status' => $queueHealthy ? 'healthy' : 'critical'],
                    'resources' => ['status' => $memoryHealthy ? 'healthy' : 'critical', 'memory_usage_percent' => round($memoryUsagePercent, 2).'%'],
                ],
                'issues' => $overallStatus === 'critical' ? ['System under stress - quick health check failed'] : [],
                'warnings' => [],
            ], 600); // Cache for 10 minutes

            if ($overallStatus === 'critical') {
                Log::warning('Quick health check detected critical system issues', [
                    'db_healthy' => $dbHealthy,
                    'queue_healthy' => $queueHealthy,
                    'memory_healthy' => $memoryHealthy,
                    'connection_time' => $connectionTime,
                    'memory_usage_percent' => $memoryUsagePercent,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Quick health check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Cache failure status
            Cache::put('defibrillator:last_health_check', [
                'timestamp' => Carbon::now(),
                'status' => 'critical',
                'check_type' => 'quick_failed',
                'error' => $e->getMessage(),
                'checks' => [],
                'issues' => ['Health check system failure: '.$e->getMessage()],
                'warnings' => [],
            ], 300); // Cache for 5 minutes only
        }
    }

    /**
     * Convert memory limit string to bytes
     */
    protected function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }

        return $value;
    }
}
