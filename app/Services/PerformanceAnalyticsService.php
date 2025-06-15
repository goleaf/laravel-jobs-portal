<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Advanced Performance Analytics Service
 * Revolutionary Collection forget() patterns for performance optimization
 */
class PerformanceAnalyticsService
{
    /**
     * Real-time performance analysis with intelligent forget() operations
     */
    public function analyzeRealTimePerformance(array $performanceData): array
    {
        $data = collect($performanceData);
        $analysisStart = microtime(true);
        
        // Phase 1: Remove outdated performance samples
        $outdatedIndices = $this->identifyOutdatedSamples($data);
        $data->forget($outdatedIndices);
        
        // Phase 2: Remove performance anomalies that skew results
        $anomalyIndices = $this->detectPerformanceAnomalies($data);
        $data->forget($anomalyIndices);
        
        // Phase 3: Remove duplicate performance measurements
        $duplicateIndices = $this->findDuplicatePerformanceSamples($data);
        $data->forget($duplicateIndices);
        
        // Phase 4: Remove incomplete performance records
        $incompleteIndices = $this->identifyIncompleteRecords($data);
        $data->forget($incompleteIndices);
        
        // Phase 5: Advanced performance pattern analysis
        $performanceMetrics = $this->calculateAdvancedMetrics($data);
        
        $analysisTime = microtime(true) - $analysisStart;
        
        return [
            'performance_data' => $data->values()->toArray(),
            'metrics' => $performanceMetrics,
            'analysis_time' => round($analysisTime * 1000, 2) . 'ms',
            'data_quality_score' => $this->calculateDataQualityScore($data),
            'optimization_suggestions' => $this->generateOptimizationSuggestions($performanceMetrics)
        ];
    }
    
    /**
     * Memory usage optimization with Collection forget() patterns
     */
    public function optimizeMemoryUsage(array $memoryData): array
    {
        $data = collect($memoryData);
        
        // Remove memory spikes caused by garbage collection
        $gcSpikes = $data->filter(function ($record) {
            return isset($record['gc_triggered']) && $record['gc_triggered'] === true;
        })->keys();
        $data->forget($gcSpikes->toArray());
        
        // Remove memory readings during system maintenance
        $maintenanceIndices = $data->filter(function ($record) {
            $timestamp = Carbon::parse($record['timestamp'] ?? now());
            return $this->isMaintenanceWindow($timestamp);
        })->keys();
        $data->forget($maintenanceIndices->toArray());
        
        // Remove extreme outliers that indicate system errors
        $outlierIndices = $this->identifyMemoryOutliers($data);
        $data->forget($outlierIndices);
        
        return [
            'optimized_memory_data' => $data->values()->toArray(),
            'memory_trends' => $this->analyzeMemoryTrends($data),
            'peak_usage_analysis' => $this->analyzePeakUsage($data),
            'optimization_opportunities' => $this->identifyOptimizationOpportunities($data)
        ];
    }
    
    /**
     * Database query performance optimization
     */
    public function optimizeDatabasePerformance(array $queryData): array
    {
        $queries = collect($queryData);
        
        // Remove test/debug queries from production analysis
        $testQueryIndices = $queries->filter(function ($query) {
            $sql = strtolower($query['sql'] ?? '');
            return str_contains($sql, 'test_') || 
                   str_contains($sql, 'debug_') || 
                   str_contains($sql, 'temp_');
        })->keys();
        $queries->forget($testQueryIndices->toArray());
        
        // Remove duplicate identical queries (keep only one for analysis)
        $duplicateQueries = $this->findDuplicateQueries($queries);
        $queries->forget($duplicateQueries);
        
        // Remove queries executed during database maintenance
        $maintenanceQueries = $queries->filter(function ($query) {
            return isset($query['during_maintenance']) && $query['during_maintenance'];
        })->keys();
        $queries->forget($maintenanceQueries->toArray());
        
        // Analyze remaining high-quality query data
        $analysis = $this->performQueryAnalysis($queries);
        
        return [
            'analyzed_queries' => $queries->values()->toArray(),
            'performance_analysis' => $analysis,
            'slow_query_insights' => $this->analyzeSlowQueries($queries),
            'optimization_recommendations' => $this->generateQueryOptimizations($analysis)
        ];
    }
    
    /**
     * User behavior performance analysis
     */
    public function analyzeUserBehaviorPerformance(array $userInteractions): array
    {
        $interactions = collect($userInteractions);
        
        // Remove bot/crawler interactions
        $botIndices = $interactions->filter(function ($interaction) {
            $userAgent = strtolower($interaction['user_agent'] ?? '');
            return str_contains($userAgent, 'bot') || 
                   str_contains($userAgent, 'crawler') || 
                   str_contains($userAgent, 'spider');
        })->keys();
        $interactions->forget($botIndices->toArray());
        
        // Remove interactions with invalid session data
        $invalidSessionIndices = $interactions->filter(function ($interaction) {
            return empty($interaction['session_id']) || 
                   strlen($interaction['session_id']) < 10;
        })->keys();
        $interactions->forget($invalidSessionIndices->toArray());
        
        // Remove rapid-fire requests (likely automated)
        $rapidFireIndices = $this->identifyRapidFireRequests($interactions);
        $interactions->forget($rapidFireIndices);
        
        // Remove interactions from admin/test users
        $adminInteractions = $interactions->filter(function ($interaction) {
            return ($interaction['user_role'] ?? '') === 'admin' || 
                   str_contains($interaction['email'] ?? '', 'test@');
        })->keys();
        $interactions->forget($adminInteractions->toArray());
        
        return [
            'clean_interactions' => $interactions->values()->toArray(),
            'behavior_patterns' => $this->analyzeBehaviorPatterns($interactions),
            'performance_impact' => $this->calculateBehaviorPerformanceImpact($interactions),
            'user_experience_metrics' => $this->calculateUXMetrics($interactions)
        ];
    }
    
    /**
     * API response time optimization
     */
    public function optimizeApiResponseTimes(array $apiData): array
    {
        $responses = collect($apiData);
        
        // Remove responses during deployment/restart periods
        $deploymentIndices = $responses->filter(function ($response) {
            return isset($response['during_deployment']) && $response['during_deployment'];
        })->keys();
        $responses->forget($deploymentIndices->toArray());
        
        // Remove health check requests (not representative of real usage)
        $healthCheckIndices = $responses->filter(function ($response) {
            $endpoint = $response['endpoint'] ?? '';
            return str_contains($endpoint, '/health') || 
                   str_contains($endpoint, '/status') || 
                   str_contains($endpoint, '/ping');
        })->keys();
        $responses->forget($healthCheckIndices->toArray());
        
        // Remove responses with network timeouts (external factors)
        $timeoutIndices = $responses->filter(function ($response) {
            return ($response['status_code'] ?? 200) === 504 || 
                   ($response['response_time'] ?? 0) > 30000; // 30 seconds
        })->keys();
        $responses->forget($timeoutIndices->toArray());
        
        // Advanced response time analysis
        $analysis = $this->performResponseTimeAnalysis($responses);
        
        return [
            'optimized_responses' => $responses->values()->toArray(),
            'response_time_analysis' => $analysis,
            'endpoint_performance' => $this->analyzeEndpointPerformance($responses),
            'bottleneck_identification' => $this->identifyBottlenecks($responses)
        ];
    }
    
    /**
     * Caching performance optimization with intelligent forget()
     */
    public function optimizeCachePerformance(array $cacheData): array
    {
        $cache = collect($cacheData);
        
        // Remove expired cache entries
        $expiredIndices = $cache->filter(function ($entry) {
            return isset($entry['expires_at']) && 
                   Carbon::parse($entry['expires_at'])->isPast();
        })->keys();
        $cache->forget($expiredIndices->toArray());
        
        // Remove cache entries that are never accessed
        $unusedIndices = $cache->filter(function ($entry) {
            $lastAccessed = Carbon::parse($entry['last_accessed'] ?? now()->subYears(1));
            return $lastAccessed->isBefore(now()->subDays(30));
        })->keys();
        $cache->forget($unusedIndices->toArray());
        
        // Remove cache entries with low hit rates
        $lowHitRateIndices = $cache->filter(function ($entry) {
            $hitRate = ($entry['hits'] ?? 0) / max(1, ($entry['requests'] ?? 1));
            return $hitRate < 0.1; // Less than 10% hit rate
        })->keys();
        $cache->forget($lowHitRateIndices->toArray());
        
        return [
            'optimized_cache' => $cache->values()->toArray(),
            'cache_efficiency' => $this->calculateCacheEfficiency($cache),
            'cache_recommendations' => $this->generateCacheRecommendations($cache),
            'storage_optimization' => $this->calculateStorageOptimization($cache)
        ];
    }
    
    /**
     * Advanced helper methods for performance analysis
     */
    protected function identifyOutdatedSamples(Collection $data): array
    {
        $cutoffTime = now()->subHours(24); // Keep last 24 hours
        
        return $data->filter(function ($sample, $index) use ($cutoffTime) {
            $timestamp = Carbon::parse($sample['timestamp'] ?? now());
            return $timestamp->isBefore($cutoffTime);
        })->keys()->toArray();
    }
    
    protected function detectPerformanceAnomalies(Collection $data): array
    {
        // Calculate statistics for anomaly detection
        $values = $data->pluck('response_time')->filter();
        if ($values->count() < 10) return [];
        
        $mean = $values->avg();
        $stdDev = sqrt($values->map(fn($x) => pow($x - $mean, 2))->avg());
        
        // Remove values more than 3 standard deviations from mean
        return $data->filter(function ($sample, $index) use ($mean, $stdDev) {
            $value = $sample['response_time'] ?? 0;
            return abs($value - $mean) > (3 * $stdDev);
        })->keys()->toArray();
    }
    
    protected function findDuplicatePerformanceSamples(Collection $data): array
    {
        $seen = [];
        $duplicates = [];
        
        foreach ($data as $index => $sample) {
            $signature = md5(serialize([
                'timestamp' => round(($sample['timestamp'] ?? time()) / 10) * 10, // 10-second windows
                'endpoint' => $sample['endpoint'] ?? '',
                'method' => $sample['method'] ?? ''
            ]));
            
            if (isset($seen[$signature])) {
                $duplicates[] = $index;
            } else {
                $seen[$signature] = true;
            }
        }
        
        return $duplicates;
    }
    
    protected function calculateAdvancedMetrics(Collection $data): array
    {
        $responseTimes = $data->pluck('response_time')->filter();
        
        return [
            'avg_response_time' => round($responseTimes->avg(), 2),
            'median_response_time' => $responseTimes->median(),
            'p95_response_time' => $responseTimes->percentile(95),
            'p99_response_time' => $responseTimes->percentile(99),
            'throughput' => $data->count() / max(1, $this->getTimeSpanInSeconds($data)),
            'error_rate' => $this->calculateErrorRate($data)
        ];
    }
    
    protected function calculateDataQualityScore(Collection $data): float
    {
        if ($data->isEmpty()) return 0;
        
        $score = 100;
        $totalRecords = $data->count();
        
        // Penalize for missing required fields
        $missingFields = $data->filter(function ($record) {
            return !isset($record['timestamp']) || !isset($record['response_time']);
        })->count();
        
        $score -= ($missingFields / $totalRecords) * 30;
        
        // Bonus for data completeness
        $completeRecords = $data->filter(function ($record) {
            return isset($record['timestamp'], $record['response_time'], $record['endpoint']);
        })->count();
        
        $score += ($completeRecords / $totalRecords) * 10;
        
        return max(0, min(100, $score));
    }
    
    protected function isMaintenanceWindow(Carbon $timestamp): bool
    {
        // Define maintenance windows (e.g., 2-4 AM daily)
        $hour = $timestamp->hour;
        return $hour >= 2 && $hour <= 4;
    }
    
    protected function identifyRapidFireRequests(Collection $interactions): array
    {
        $rapidFire = [];
        $interactions = $interactions->sortBy('timestamp');
        
        for ($i = 1; $i < $interactions->count(); $i++) {
            $current = $interactions->values()[$i];
            $previous = $interactions->values()[$i - 1];
            
            if (($current['user_id'] ?? null) === ($previous['user_id'] ?? null)) {
                $timeDiff = Carbon::parse($current['timestamp'])->diffInSeconds(
                    Carbon::parse($previous['timestamp'])
                );
                
                if ($timeDiff < 1) { // Less than 1 second between requests
                    $rapidFire[] = $i;
                }
            }
        }
        
        return $rapidFire;
    }
} 