<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Performance monitoring middleware
 */
class PerformanceMonitoring
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = round(($endTime - $startTime) * 1000, 2); // ms
        $memoryUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2); // MB
        $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2); // MB

        // Log slow requests (>2 seconds)
        if ($executionTime > 2000) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime,
                'memory_usage' => $memoryUsage,
                'peak_memory' => $peakMemory,
                'user_id' => auth()->id(),
            ]);
        }

        // Add performance headers in development
        if (app()->environment('local', 'development')) {
            $response->headers->set('X-Execution-Time', $executionTime . 'ms');
            $response->headers->set('X-Memory-Usage', $memoryUsage . 'MB');
            $response->headers->set('X-Peak-Memory', $peakMemory . 'MB');
        }

        // Store performance metrics (you could use a proper metrics service)
        cache()->put(
            'performance_metrics_' . date('Y-m-d-H'),
            cache()->get('performance_metrics_' . date('Y-m-d-H'), []) + [
                time() => [
                    'execution_time' => $executionTime,
                    'memory_usage' => $memoryUsage,
                    'endpoint' => $request->path(),
                ]
            ],
            3600
        );

        return $response;
    }
}