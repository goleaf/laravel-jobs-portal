<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use App\Services\RedisCacheService;

/**
 * Redis health check controller
 */
class RedisHealthController extends Controller
{
    private RedisCacheService $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function check(): JsonResponse
    {
        $status = [
            'redis' => $this->checkRedisConnection(),
            'cache' => $this->checkCacheConnection(),
            'sessions' => $this->checkSessionConnection(),
            'queues' => $this->checkQueueConnection(),
            'stats' => $this->cacheService->getRedisStats(),
        ];

        $overall = $this->determineOverallHealth($status);

        return response()->json([
            'status' => $overall,
            'timestamp' => now()->toISOString(),
            'services' => $status,
        ], $overall === 'healthy' ? 200 : 503);
    }

    private function checkRedisConnection(): array
    {
        try {
            $redis = Redis::connection();
            $response = $redis->ping();
            return [
                'status' => 'healthy',
                'response_time' => $this->measureResponseTime(fn() => $redis->ping()),
                'message' => 'Redis connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => $e->getMessage()
            ];
        }
    }

    private function checkCacheConnection(): array
    {
        try {
            $redis = Redis::connection('cache');
            $testKey = 'health_check_' . time();
            $redis->setex($testKey, 60, 'test');
            $value = $redis->get($testKey);
            $redis->del($testKey);
            
            return [
                'status' => $value === 'test' ? 'healthy' : 'unhealthy',
                'message' => 'Cache read/write test completed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => $e->getMessage()
            ];
        }
    }

    private function checkSessionConnection(): array
    {
        try {
            $redis = Redis::connection('sessions');
            $redis->ping();
            return [
                'status' => 'healthy',
                'message' => 'Session Redis connection active'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => $e->getMessage()
            ];
        }
    }

    private function checkQueueConnection(): array
    {
        try {
            $redis = Redis::connection('queues');
            $redis->ping();
            return [
                'status' => 'healthy',
                'message' => 'Queue Redis connection active'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => $e->getMessage()
            ];
        }
    }

    private function measureResponseTime(callable $operation): string
    {
        $start = microtime(true);
        $operation();
        $end = microtime(true);
        return round(($end - $start) * 1000, 2) . 'ms';
    }

    private function determineOverallHealth(array $status): string
    {
        foreach ($status as $service => $details) {
            if (is_array($details) && isset($details['status']) && $details['status'] === 'unhealthy') {
                return 'degraded';
            }
        }
        return 'healthy';
    }
}