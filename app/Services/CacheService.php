<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    /**
     * Default cache TTL in minutes.
     */
    public const DEFAULT_TTL = 60;

    /**
     * Long cache TTL for rarely changing data.
     */
    public const LONG_TTL = 1440; // 24 hours

    /**
     * Short cache TTL for frequently changing data.
     */
    public const SHORT_TTL = 15;

    /**
     * Cache a model query result.
     *
     * @param null|int $ttl TTL in minutes
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
                    'memory_usage' => memory_get_usage(true),
                ]);

                return $result;
            });
        } catch (\Exception $e) {
            Log::error("Cache error for key: {$key}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return uncached result on cache failure
            return $callback();
        }
    }

    /**
     * Cache a model with automatic key generation.
     */
    public function rememberModel(Model $model, callable $callback, ?int $ttl = null): mixed
    {
        $key = $this->generateModelKey($model);

        return $this->remember($key, $callback, $ttl);
    }

    /**
     * Cache a collection with pagination.
     */
    public function rememberPaginated(string $baseKey, array $params, callable $callback, ?int $ttl = null): mixed
    {
        $key = $this->generatePaginationKey($baseKey, $params);

        return $this->remember($key, $callback, $ttl);
    }

    /**
     * Store data with flexible TTL.
     */
    public function put(string $key, mixed $value, null|Carbon|int $ttl = null): bool
    {
        try {
            if ($ttl instanceof Carbon) {
                return Cache::put($key, $value, $ttl);
            }

            $minutes = $ttl ?? self::DEFAULT_TTL;

            return Cache::put($key, $value, now()->addMinutes($minutes));
        } catch (\Exception $e) {
            Log::error("Cache put error for key: {$key}", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get data from cache.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::error("Cache get error for key: {$key}", [
                'error' => $e->getMessage(),
            ]);

            return $default;
        }
    }

    /**
     * Invalidate cache by key pattern.
     *
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
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Invalidate model-related cache.
     */
    public function invalidateModel(Model $model): int
    {
        $pattern = $this->getModelPattern($model);

        return $this->invalidatePattern($pattern);
    }

    /**
     * Forget specific cache key.
     */
    public function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (\Exception $e) {
            Log::error("Cache forget error for key: {$key}", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Flush all cache.
     */
    public function flush(): bool
    {
        try {
            return Cache::flush();
        } catch (\Exception $e) {
            Log::error('Cache flush error', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get cache statistics.
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
                'uptime' => $info['uptime_in_seconds'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Cache stats error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'error' => 'Unable to retrieve cache statistics',
            ];
        }
    }

    /**
     * Increment cache value.
     */
    public function increment(string $key, int $value = 1): false|int
    {
        try {
            return Cache::increment($key, $value);
        } catch (\Exception $e) {
            Log::error("Cache increment error for key: {$key}", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Decrement cache value.
     */
    public function decrement(string $key, int $value = 1): false|int
    {
        try {
            return Cache::decrement($key, $value);
        } catch (\Exception $e) {
            Log::error("Cache decrement error for key: {$key}", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Cache API response with TTL.
     */
    public function rememberApi(string $key, callable $callback, int $ttl = self::SHORT_TTL): mixed
    {
        return $this->remember("api:{$key}", $callback, $ttl);
    }

    /**
     * Cache view fragment.
     */
    public function rememberView(string $viewKey, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        return $this->remember("view:{$viewKey}", $callback, $ttl);
    }

    /**
     * Cache expensive query results.
     */
    public function rememberQuery(string $queryKey, callable $callback, int $ttl = self::LONG_TTL): mixed
    {
        return $this->remember("query:{$queryKey}", $callback, $ttl);
    }

    /**
     * Generate model-specific cache key.
     */
    protected function generateModelKey(Model $model, string $suffix = ''): string
    {
        $class = class_basename($model);
        $key = strtolower($class).':'.$model->getKey();

        if ($suffix) {
            $key .= ':'.$suffix;
        }

        return $key;
    }

    /**
     * Generate pagination cache key.
     */
    protected function generatePaginationKey(string $baseKey, array $params): string
    {
        $paramsHash = md5(serialize($params));

        return $baseKey.':paginated:'.$paramsHash;
    }

    /**
     * Get model cache pattern for invalidation.
     */
    protected function getModelPattern(Model $model): string
    {
        $class = class_basename($model);

        return strtolower($class).':*';
    }

    /**
     * Calculate cache hit rate.
     */
    protected function calculateHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;

        if (0 === $total) {
            return 0.0;
        }

        return round(($hits / $total) * 100, 2);
    }
}
