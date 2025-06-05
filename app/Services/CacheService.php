<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CacheService
{
    /**
     * Default cache TTL in minutes
     */
    const DEFAULT_TTL = 60;
    
    /**
     * Long cache TTL for rarely changing data
     */
    const LONG_TTL = 1440; // 24 hours
    
    /**
     * Short cache TTL for frequently changing data
     */
    const SHORT_TTL = 15;

    /**
     * Cache a model query result
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl TTL in minutes
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;
        
        try {
            return Cache::remember($key, now()->addMinutes($ttl), function () use ($callback, $key) {
                $startTime = microtime(true);
                $result = $callback();
                $executionTime = microtime(true) - $startTime;
                
                Log::info("Cache miss for key: {$key}", [
                    'execution_time' => $executionTime,
                    'memory_usage' => memory_get_usage(true)
                ]);
                
                return $result;
            });
        } catch (\Exception $e) {
            Log::error("Cache error for key: {$key}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return uncached result on cache failure
            return $callback();
        }
    }

    /**
     * Cache a model with automatic key generation
     *
     * @param Model $model
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    public function rememberModel(Model $model, callable $callback, ?int $ttl = null): mixed
    {
        $key = $this->generateModelKey($model);
        return $this->remember($key, $callback, $ttl);
    }

    /**
     * Cache a collection with pagination
     *
     * @param string $baseKey
     * @param array $params
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    public function rememberPaginated(string $baseKey, array $params, callable $callback, ?int $ttl = null): mixed
    {
        $key = $this->generatePaginationKey($baseKey, $params);
        return $this->remember($key, $callback, $ttl);
    }

    /**
     * Store data with flexible TTL
     *
     * @param string $key
     * @param mixed $value
     * @param int|Carbon|null $ttl
     * @return bool
     */
    public function put(string $key, mixed $value, int|Carbon|null $ttl = null): bool
    {
        try {
            if ($ttl instanceof Carbon) {
                return Cache::put($key, $value, $ttl);
            }
            
            $minutes = $ttl ?? self::DEFAULT_TTL;
            return Cache::put($key, $value, now()->addMinutes($minutes));
        } catch (\Exception $e) {
            Log::error("Cache put error for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get data from cache
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::error("Cache get error for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Invalidate cache by key pattern
     *
     * @param string $pattern
     * @return int Number of keys deleted
     */
    public function invalidatePattern(string $pattern): int
    {
        try {
            $keys = Redis::keys($pattern);
            if (empty($keys)) {
                return 0;
            }
            
            return Redis::del($keys);
        } catch (\Exception $e) {
            Log::error("Cache invalidation error for pattern: {$pattern}", [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Invalidate model-related cache
     *
     * @param Model $model
     * @return int
     */
    public function invalidateModel(Model $model): int
    {
        $pattern = $this->getModelPattern($model);
        return $this->invalidatePattern($pattern);
    }

    /**
     * Forget specific cache key
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (\Exception $e) {
            Log::error("Cache forget error for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Flush all cache
     *
     * @return bool
     */
    public function flush(): bool
    {
        try {
            return Cache::flush();
        } catch (\Exception $e) {
            Log::error("Cache flush error", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get cache statistics
     *
     * @return array
     */
    public function getStats(): array
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info();
            
            return [
                'used_memory' => $info['used_memory_human'] ?? 'N/A',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'total_commands_processed' => $info['total_commands_processed'] ?? 0,
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $this->calculateHitRate($info),
                'uptime' => $info['uptime_in_seconds'] ?? 0
            ];
        } catch (\Exception $e) {
            Log::error("Cache stats error", [
                'error' => $e->getMessage()
            ]);
            
            return [
                'error' => 'Unable to retrieve cache statistics'
            ];
        }
    }

    /**
     * Increment cache value
     *
     * @param string $key
     * @param int $value
     * @return int|false
     */
    public function increment(string $key, int $value = 1): int|false
    {
        try {
            return Cache::increment($key, $value);
        } catch (\Exception $e) {
            Log::error("Cache increment error for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Decrement cache value
     *
     * @param string $key
     * @param int $value
     * @return int|false
     */
    public function decrement(string $key, int $value = 1): int|false
    {
        try {
            return Cache::decrement($key, $value);
        } catch (\Exception $e) {
            Log::error("Cache decrement error for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate model-specific cache key
     *
     * @param Model $model
     * @param string $suffix
     * @return string
     */
    protected function generateModelKey(Model $model, string $suffix = ''): string
    {
        $class = class_basename($model);
        $key = strtolower($class) . ':' . $model->getKey();
        
        if ($suffix) {
            $key .= ':' . $suffix;
        }
        
        return $key;
    }

    /**
     * Generate pagination cache key
     *
     * @param string $baseKey
     * @param array $params
     * @return string
     */
    protected function generatePaginationKey(string $baseKey, array $params): string
    {
        $paramsHash = md5(serialize($params));
        return $baseKey . ':paginated:' . $paramsHash;
    }

    /**
     * Get model cache pattern for invalidation
     *
     * @param Model $model
     * @return string
     */
    protected function getModelPattern(Model $model): string
    {
        $class = class_basename($model);
        return strtolower($class) . ':*';
    }

    /**
     * Calculate cache hit rate
     *
     * @param array $info
     * @return float
     */
    protected function calculateHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;
        
        if ($total === 0) {
            return 0.0;
        }
        
        return round(($hits / $total) * 100, 2);
    }

    /**
     * Cache API response with TTL
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function rememberApi(string $key, callable $callback, int $ttl = self::SHORT_TTL): mixed
    {
        return $this->remember("api:{$key}", $callback, $ttl);
    }

    /**
     * Cache view fragment
     *
     * @param string $viewKey
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function rememberView(string $viewKey, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        return $this->remember("view:{$viewKey}", $callback, $ttl);
    }

    /**
     * Cache expensive query results
     *
     * @param string $queryKey
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function rememberQuery(string $queryKey, callable $callback, int $ttl = self::LONG_TTL): mixed
    {
        return $this->remember("query:{$queryKey}", $callback, $ttl);
    }
}