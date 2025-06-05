<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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
        
        // Enable query logging
        DB::enableQueryLog();
        
        $response = $next($request);
        
        // Calculate performance metrics
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;
        $queries = DB::getQueryLog();
        
        $metrics = [
            'url' => $request->url(),
            'method' => $request->method(),
            'execution_time' => round($executionTime, 2),
            'memory_usage' => $this->formatBytes($memoryUsage),
            'memory_usage_bytes' => $memoryUsage,
            'query_count' => count($queries),
            'slow_queries' => $this->identifySlowQueries($queries),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ];
        
        // Log performance data
        $this->logPerformanceMetrics($metrics);
        
        // Cache performance data for monitoring
        $this->cachePerformanceData($metrics);
        
        // Add performance headers to response
        $response->headers->set('X-Response-Time', $metrics['execution_time'] . 'ms');
        $response->headers->set('X-Memory-Usage', $metrics['memory_usage']);
        $response->headers->set('X-Query-Count', $metrics['query_count']);
        
        return $response;
    }
    
    /**
     * Identify slow queries (>100ms)
     */
    private function identifySlowQueries(array $queries): array
    {
        $slowQueries = [];
        
        foreach ($queries as $query) {
            if ($query['time'] > 100) { // 100ms threshold
                $slowQueries[] = [
                    'query' => $query['query'],
                    'time' => $query['time'],
                    'bindings' => $query['bindings']
                ];
            }
        }
        
        return $slowQueries;
    }
    
    /**
     * Log performance metrics
     */
    private function logPerformanceMetrics(array $metrics): void
    {
        // Log slow requests (>1000ms)
        if ($metrics['execution_time'] > 1000) {
            Log::warning('Slow request detected', $metrics);
        }
        
        // Log high memory usage (>50MB)
        if ($metrics['memory_usage_bytes'] > 50 * 1024 * 1024) {
            Log::warning('High memory usage detected', $metrics);
        }
        
        // Log many queries (>20)
        if ($metrics['query_count'] > 20) {
            Log::warning('High query count detected', $metrics);
        }
        
        // Log slow queries
        if (!empty($metrics['slow_queries'])) {
            Log::warning('Slow queries detected', [
                'url' => $metrics['url'],
                'slow_queries' => $metrics['slow_queries']
            ]);
        }
        
        // Regular performance log
        Log::info('Request performance', [
            'url' => $metrics['url'],
            'method' => $metrics['method'],
            'execution_time' => $metrics['execution_time'],
            'memory_usage' => $metrics['memory_usage'],
            'query_count' => $metrics['query_count']
        ]);
    }
    
    /**
     * Cache performance data for monitoring dashboard
     */
    private function cachePerformanceData(array $metrics): void
    {
        try {
            $cacheKey = 'performance_metrics_' . date('Y-m-d-H');
            
            $cachedData = Cache::get($cacheKey, [
                'requests' => 0,
                'total_time' => 0,
                'total_memory' => 0,
                'total_queries' => 0,
                'slow_requests' => 0,
                'slow_queries_count' => 0
            ]);
            
            $cachedData['requests']++;
            $cachedData['total_time'] += $metrics['execution_time'];
            $cachedData['total_memory'] += $metrics['memory_usage_bytes'];
            $cachedData['total_queries'] += $metrics['query_count'];
            
            if ($metrics['execution_time'] > 1000) {
                $cachedData['slow_requests']++;
            }
            
            if (!empty($metrics['slow_queries'])) {
                $cachedData['slow_queries_count'] += count($metrics['slow_queries']);
            }
            
            Cache::put($cacheKey, $cachedData, 3600); // Cache for 1 hour
        } catch (\Exception $e) {
            Log::error('Failed to cache performance metrics', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $base = log($bytes, 1024);
        
        return round(pow(1024, $base - floor($base)), 2) . ' ' . $units[floor($base)];
    }
}