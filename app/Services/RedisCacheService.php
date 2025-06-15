<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * Redis-enhanced cache service
 * Extends the basic CacheService with Redis-specific optimizations.
 */
class RedisCacheService extends CacheService
{
    private $redis;

    public function __construct()
    {
        try {
            $this->redis = Redis::connection('cache');
        } catch (\Exception $e) {
            $this->redis = null;
        }
    }

    /**
     * High-performance Redis caching with pipelining.
     */
    public function multiRemember(array $keys, callable $callback, int $ttl = 3600): array
    {
        if (!$this->redis) {
            return parent::multiGet($keys, $callback, $ttl);
        }

        $results = [];
        $missingKeys = [];

        // Pipeline Redis operations for better performance
        $this->redis->pipeline(function ($pipe) use ($keys, &$results, &$missingKeys) {
            foreach ($keys as $key) {
                $value = $pipe->get($key);
                if (null === $value) {
                    $missingKeys[] = $key;
                } else {
                    $results[$key] = unserialize($value);
                }
            }
        });

        // Get missing values and cache them
        if (!empty($missingKeys)) {
            $missingValues = $callback($missingKeys);

            $this->redis->pipeline(function ($pipe) use ($missingValues, $ttl) {
                foreach ($missingValues as $key => $value) {
                    $pipe->setex($key, $ttl, serialize($value));
                }
            });

            $results = array_merge($results, $missingValues);
        }

        return $results;
    }

    /**
     * Redis-specific cache warming.
     */
    public function warmCache(array $data): void
    {
        if (!$this->redis) {
            return;
        }

        $this->redis->pipeline(function ($pipe) use ($data) {
            foreach ($data as $key => $value) {
                $pipe->setex($key, 3600, serialize($value));
            }
        });
    }

    /**
     * Get cache statistics from Redis.
     */
    public function getRedisStats(): array
    {
        if (!$this->redis) {
            return ['status' => 'disconnected'];
        }

        try {
            $info = $this->redis->info();

            return [
                'status' => 'connected',
                'memory_used' => $info['used_memory_human'] ?? 'N/A',
                'keys_total' => $info['db1']['keys'] ?? 0,
                'hits' => $info['keyspace_hits'] ?? 0,
                'misses' => $info['keyspace_misses'] ?? 0,
                'hit_ratio' => $this->calculateHitRatio($info),
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function calculateHitRatio(array $info): string
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;

        if (0 === $total) {
            return '0%';
        }

        return round(($hits / $total) * 100, 2).'%';
    }
}
