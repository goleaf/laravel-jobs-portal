    /**
     * Get performance metrics dashboard
     */
    public function getPerformanceMetrics()
    {
        try {
            $cacheKey = 'performance_metrics_' . now()->format('Y-m-d-H');
            
            return Cache::remember($cacheKey, 3600, function () {
                return [
                    'response_times' => $this->getResponseTimeMetrics(),
                    'database_performance' => $this->getDatabaseMetrics(),
                    'cache_performance' => $this->getCacheMetrics(),
                    'memory_usage' => $this->getMemoryUsage(),
                    'queue_performance' => $this->getQueueMetrics(),
                    'system_load' => $this->getSystemLoad(),
                    'error_rates' => $this->getErrorRates(),
                    'slow_queries' => $this->getSlowQueries()
                ];
            });
        } catch (\Exception $e) {
            Log::error('Performance metrics error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'error' => 'Unable to load performance metrics',
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Optimize database queries
     */
    public function optimizeQueries()
    {
        try {
            $optimizations = [];
            
            // Find and cache frequently used queries
            $frequentQueries = DB::table('query_logs')
                ->select('query_hash', DB::raw('COUNT(*) as frequency'), DB::raw('AVG(execution_time) as avg_time'))
                ->where('created_at', '>=', now()->subHours(24))
                ->groupBy('query_hash')
                ->having('frequency', '>', 100)
                ->orderBy('frequency', 'desc')
                ->limit(50)
                ->get();
            
            foreach ($frequentQueries as $query) {
                // Cache results for frequent queries
                $cacheKey = 'optimized_query_' . $query->query_hash;
                if (!Cache::has($cacheKey)) {
                    // Mark for optimization
                    $optimizations['cached_queries'][] = $query->query_hash;
                }
            }
            
            // Identify slow queries for optimization
            $slowQueries = DB::table('query_logs')
                ->where('execution_time', '>', 1000) // > 1 second
                ->where('created_at', '>=', now()->subHours(24))
                ->select('query', 'execution_time', 'created_at')
                ->orderBy('execution_time', 'desc')
                ->limit(20)
                ->get();
            
            $optimizations['slow_queries'] = $slowQueries->map(function ($query) {
                return [
                    'query' => Str::limit($query->query, 100),
                    'execution_time' => $query->execution_time . 'ms',
                    'recommendation' => $this->getQueryOptimizationRecommendation($query->query)
                ];
            });
            
            // Clear outdated cache entries
            $clearedCaches = $this->clearOutdatedCaches();
            $optimizations['cache_cleared'] = $clearedCaches;
            
            // Optimize indexes
            $indexOptimizations = $this->optimizeIndexes();
            $optimizations['index_optimizations'] = $indexOptimizations;
            
            Log::info('Database optimization completed', [
                'optimizations' => $optimizations
            ]);
            
            return $optimizations;
            
        } catch (\Exception $e) {
            Log::error('Query optimization error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Manage cache optimization
     */
    public function optimizeCache()
    {
        try {
            $results = [];
            
            // Pre-warm critical caches
            $criticalCaches = [
                'job_categories' => function() {
                    return DB::table('job_categories')->where('is_active', true)->get();
                },
                'active_companies' => function() {
                    return DB::table('companies')->where('is_active', true)->limit(100)->get();
                },
                'popular_skills' => function() {
                    return DB::table('skills')->orderBy('usage_count', 'desc')->limit(50)->get();
                },
                'system_settings' => function() {
                    return DB::table('settings')->pluck('value', 'key');
                }
            ];
            
            foreach ($criticalCaches as $key => $callback) {
                Cache::remember($key, 3600, $callback);
                $results['prewarmed_caches'][] = $key;
            }
            
            // Implement cache tags for better invalidation
            $this->implementCacheTags();
            $results['cache_tags_implemented'] = true;
            
            // Set up cache monitoring
            $this->setupCacheMonitoring();
            $results['cache_monitoring_enabled'] = true;
            
            // Optimize Redis configuration
            if (config('cache.default') === 'redis') {
                $redisOptimizations = $this->optimizeRedisConfig();
                $results['redis_optimizations'] = $redisOptimizations;
            }
            
            Log::info('Cache optimization completed', [
                'results' => $results
            ]);
            
            return $results;
            
        } catch (\Exception $e) {
            Log::error('Cache optimization error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Monitor system performance
     */
    public function monitorPerformance()
    {
        try {
            $metrics = [];
            
            // CPU and Memory monitoring
            $systemMetrics = $this->getSystemMetrics();
            $metrics['system'] = $systemMetrics;
            
            // Database performance
            $dbMetrics = $this->getDatabasePerformanceMetrics();
            $metrics['database'] = $dbMetrics;
            
            // Application response times
            $responseMetrics = $this->getApplicationResponseMetrics();
            $metrics['response_times'] = $responseMetrics;
            
            // Error rate monitoring
            $errorMetrics = $this->getErrorMetrics();
            $metrics['errors'] = $errorMetrics;
            
            // Queue performance
            $queueMetrics = $this->getQueuePerformanceMetrics();
            $metrics['queues'] = $queueMetrics;
            
            // Check for performance alerts
            $alerts = $this->checkPerformanceAlerts($metrics);
            if (!empty($alerts)) {
                $this->sendPerformanceAlerts($alerts);
                $metrics['alerts'] = $alerts;
            }
            
            // Store metrics for trending
            $this->storePerformanceMetrics($metrics);
            
            return $metrics;
            
        } catch (\Exception $e) {
            Log::error('Performance monitoring error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate performance report
     */
    public function generatePerformanceReport($period = '24h')
    {
        try {
            $startTime = $this->getPeriodStartTime($period);
            $endTime = now();
            
            $report = [
                'period' => $period,
                'start_time' => $startTime->toISOString(),
                'end_time' => $endTime->toISOString(),
                'summary' => $this->getPerformanceSummary($startTime, $endTime),
                'detailed_metrics' => $this->getDetailedMetrics($startTime, $endTime),
                'recommendations' => $this->getPerformanceRecommendations($startTime, $endTime),
                'trending_data' => $this->getTrendingData($startTime, $endTime)
            ];
            
            // Cache the report
            $cacheKey = 'performance_report_' . $period . '_' . $endTime->format('Y-m-d-H');
            Cache::put($cacheKey, $report, 3600);
            
            Log::info('Performance report generated', [
                'period' => $period,
                'report_size' => strlen(json_encode($report))
            ]);
            
            return $report;
            
        } catch (\Exception $e) {
            Log::error('Performance report generation error', [
                'error' => $e->getMessage(),
                'period' => $period,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get response time metrics
     */
    private function getResponseTimeMetrics()
    {
        try {
            return [
                'average_response_time' => DB::table('performance_logs')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->avg('response_time') ?? 0,
                'p95_response_time' => $this->getPercentileResponseTime(95),
                'p99_response_time' => $this->getPercentileResponseTime(99),
                'slowest_endpoints' => DB::table('performance_logs')
                    ->select('endpoint', DB::raw('AVG(response_time) as avg_time'))
                    ->where('created_at', '>=', now()->subHours(24))
                    ->groupBy('endpoint')
                    ->orderBy('avg_time', 'desc')
                    ->limit(10)
                    ->get()
            ];
        } catch (\Exception $e) {
            Log::error('Response time metrics error', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch response time metrics'];
        }
    }

    /**
     * Get database performance metrics
     */
    private function getDatabaseMetrics()
    {
        try {
            return [
                'query_count' => DB::table('query_logs')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->count(),
                'average_query_time' => DB::table('query_logs')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->avg('execution_time') ?? 0,
                'slow_query_count' => DB::table('query_logs')
                    ->where('execution_time', '>', 1000)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->count(),
                'connection_pool_usage' => $this->getConnectionPoolUsage(),
                'index_usage' => $this->getIndexUsageStats()
            ];
        } catch (\Exception $e) {
            Log::error('Database metrics error', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch database metrics'];
        }
    }

    /**
     * Get cache performance metrics
     */
    private function getCacheMetrics()
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info();
            
            return [
                'hit_rate' => $this->calculateCacheHitRate(),
                'memory_usage' => $info['used_memory_human'] ?? 'N/A',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'operations_per_sec' => $info['instantaneous_ops_per_sec'] ?? 0,
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'expired_keys' => $info['expired_keys'] ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('Cache metrics error', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch cache metrics'];
        }
    }

    /**
     * Get memory usage statistics
     */
    private function getMemoryUsage()
    {
        try {
            $memoryLimit = ini_get('memory_limit');
            $memoryUsage = memory_get_usage(true);
            $peakMemoryUsage = memory_get_peak_usage(true);
            
            return [
                'current_usage' => $this->formatBytes($memoryUsage),
                'peak_usage' => $this->formatBytes($peakMemoryUsage),
                'limit' => $memoryLimit,
                'usage_percentage' => $this->calculateMemoryPercentage($memoryUsage, $memoryLimit)
            ];
        } catch (\Exception $e) {
            Log::error('Memory usage error', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch memory usage'];
        }
    }

    /**
     * Get queue performance metrics
     */
    private function getQueueMetrics()
    {
        try {
            return [
                'pending_jobs' => DB::table('jobs')->count(),
                'failed_jobs' => DB::table('failed_jobs')->count(),
                'processed_jobs_24h' => DB::table('job_batches')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->sum('total_jobs'),
                'average_processing_time' => $this->getAverageJobProcessingTime(),
                'queue_health' => $this->assessQueueHealth()
            ];
        } catch (\Exception $e) {
            Log::error('Queue metrics error', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch queue metrics'];
        }
    }

    /**
     * Get system load information
     */
    private function getSystemLoad()
    {
        try {
            if (function_exists('sys_getloadavg')) {
                $load = sys_getloadavg();
                return [
                    '1_minute' => $load[0],
                    '5_minutes' => $load[1],
                    '15_minutes' => $load[2],
                    'cpu_cores' => $this->getCpuCoreCount()
                ];
            }
            
            return ['error' => 'System load monitoring not available'];
        } catch (\Exception $e) {
            Log::error('System load error', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch system load'];
        }
    }

    /**
     * Get error rate metrics
     */
    private function getErrorRates()
    {
        try {
            $totalRequests = DB::table('performance_logs')
                ->where('created_at', '>=', now()->subHours(24))
                ->count();
            
            $errorCount = DB::table('performance_logs')
                ->where('status_code', '>=', 400)
                ->where('created_at', '>=', now()->subHours(24))
                ->count();
            
            return [
                'total_requests' => $totalRequests,
                'error_count' => $errorCount,
                'error_rate' => $totalRequests > 0 ? ($errorCount / $totalRequests) * 100 : 0,
                'error_breakdown' => $this->getErrorBreakdown()
            ];
        } catch (\Exception $e) {
            Log::error('Error rates error', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch error rates'];
        }
    }

    /**
     * Get slow queries list
     */
    private function getSlowQueries()
    {
        try {
            return DB::table('query_logs')
                ->where('execution_time', '>', 1000)
                ->where('created_at', '>=', now()->subHours(24))
                ->select('query', 'execution_time', 'created_at')
                ->orderBy('execution_time', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($query) {
                    return [
                        'query' => Str::limit($query->query, 100),
                        'execution_time' => $query->execution_time . 'ms',
                        'created_at' => $query->created_at
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Slow queries error', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to fetch slow queries'];
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Store performance metrics for trending
     */
    private function storePerformanceMetrics($metrics)
    {
        try {
            DB::table('performance_metrics')->insert([
                'timestamp' => now(),
                'metrics' => json_encode($metrics),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Store performance metrics error', ['error' => $e->getMessage()]);
        }
    } 