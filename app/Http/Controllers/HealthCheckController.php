<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * Laravel Defibrillator Health Check Controller
 */
class HealthCheckController extends Controller
{
    /**
     * Basic health check endpoint
     */
    public function index(): JsonResponse
    {
        $healthStatus = Cache::get('defibrillator:last_health_check');

        if (! $healthStatus || Carbon::parse($healthStatus['timestamp'])->diffInMinutes(Carbon::now()) > 15) {
            Artisan::call('defibrillator:check', ['--verbose' => true]);
            $healthStatus = Cache::get('defibrillator:last_health_check');
        }

        $response = [
            'status' => $healthStatus['status'] ?? 'unknown',
            'timestamp' => $healthStatus['timestamp'] ?? Carbon::now(),
            'uptime' => $this->getSystemUptime(),
            'version' => config('app.version', '1.0.0'),
        ];

        $statusCode = ($healthStatus['status'] ?? 'unknown') === 'healthy' ? 200 : 503;

        return response()->json($response, $statusCode);
    }

    /**
     * Detailed health check with full system information
     */
    public function detailed(): JsonResponse
    {
        Artisan::call('defibrillator:check', ['--verbose' => true]);
        $healthStatus = Cache::get('defibrillator:last_health_check');

        $response = [
            'status' => $healthStatus['status'] ?? 'unknown',
            'timestamp' => $healthStatus['timestamp'] ?? Carbon::now(),
            'checks' => $healthStatus['checks'] ?? [],
            'issues' => $healthStatus['issues'] ?? [],
            'warnings' => $healthStatus['warnings'] ?? [],
            'system_info' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'memory_usage' => $this->formatBytes(memory_get_usage(true)),
                'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
                'uptime' => $this->getSystemUptime(),
            ],
        ];

        $statusCode = ($healthStatus['status'] ?? 'unknown') === 'healthy' ? 200 : 503;

        return response()->json($response, $statusCode);
    }

    /**
     * Quick ping endpoint
     */
    public function ping(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => Carbon::now(),
            'service' => 'job-portal',
        ]);
    }

    /**
     * System heartbeat (Laravel Defibrillator concept)
     */
    public function heartbeat(): JsonResponse
    {
        $healthStatus = Cache::get('defibrillator:last_health_check');
        $lastCheckTime = $healthStatus['timestamp'] ?? null;

        $heartbeat = [
            'alive' => true,
            'status' => $healthStatus['status'] ?? 'unknown',
            'last_health_check' => $lastCheckTime,
            'minutes_since_check' => $lastCheckTime ? Carbon::parse($lastCheckTime)->diffInMinutes(Carbon::now()) : null,
            'rhythm' => $this->getSystemRhythm($healthStatus),
        ];

        return response()->json($heartbeat);
    }

    /**
     * Run manual health check
     */
    public function check(Request $request): JsonResponse
    {
        $options = [];

        if ($request->boolean('verbose')) {
            $options['--verbose'] = true;
        }

        if ($request->boolean('repair')) {
            $options['--repair'] = true;
        }

        $exitCode = Artisan::call('defibrillator:check', $options);
        $output = Artisan::output();

        $healthStatus = Cache::get('defibrillator:last_health_check');

        return response()->json([
            'status' => $healthStatus['status'] ?? 'unknown',
            'exit_code' => $exitCode,
            'output' => $output,
            'details' => $healthStatus,
            'timestamp' => Carbon::now(),
        ], $exitCode === 0 ? 200 : 503);
    }

    protected function getSystemUptime(): string
    {
        $uptime = shell_exec('uptime');

        return $uptime ? trim($uptime) : 'Unknown';
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    protected function getSystemRhythm(?array $healthStatus): string
    {
        if (! $healthStatus) {
            return 'unknown';
        }

        $status = $healthStatus['status'];
        $issues = count($healthStatus['issues'] ?? []);
        $warnings = count($healthStatus['warnings'] ?? []);

        if ($status === 'healthy' && $issues === 0 && $warnings === 0) {
            return 'normal sinus rhythm';
        } elseif ($status === 'healthy' && $warnings > 0) {
            return 'sinus rhythm with irregularities';
        } elseif ($status === 'critical') {
            return 'arrhythmia detected';
        }

        return 'rhythm under evaluation';
    }
}
