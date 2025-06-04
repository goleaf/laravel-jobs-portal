<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        $duration = round(($endTime - $startTime) * 1000, 2); // milliseconds
        $memoryUsed = $this->formatBytes($endMemory - $startMemory);
        $peakMemory = $this->formatBytes(memory_get_peak_usage(true));
        
        // Log slow requests
        if ($duration > 1000) { // Slower than 1 second
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'duration' => $duration . 'ms',
                'memory' => $memoryUsed,
                'peak_memory' => $peakMemory,
                'user_id' => auth()->id(),
                'ip' => $request->ip()
            ]);
        }
        
        // Add performance headers for debugging
        if (app()->environment('local', 'staging')) {
            $response->headers->set('X-Debug-Time', $duration . 'ms');
            $response->headers->set('X-Debug-Memory', $memoryUsed);
            $response->headers->set('X-Debug-Peak-Memory', $peakMemory);
        }
        
        return $response;
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}