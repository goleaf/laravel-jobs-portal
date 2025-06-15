<?php

namespace App\Services\Cache;

use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;

/**
 * Enhanced Cache Manager.
 *
 * Provides advanced caching capabilities with:
 * - Multi-layer caching strategy
 * - Cache tagging and invalidation
 * - Flexible TTL management
 * - Performance monitoring
 * - Stale-while-revalidate pattern
 */
class CacheManager
{
    private Repository $cache;
    private array $stats;

    public function __construct()
    {
        $this->cache = Cache::store();
        $this->stats = [
            'hits' => 0,
            'misses' => 0,
            'writes' => 0,
            'deletes' => 0,
        ];
    }

    /**
     * Remember a value with caching.
     *
     * @param int $ttl TTL in seconds
     */
    public function remember(string $key, callable $callback, int $ttl = 3600): mixed
    {
        if ($value = $this->get($key)) {
            ++$this->stats['hits'];

            return $value;
        }

        ++$this->stats['misses'];
        $value = $callback();
        $this->put($key, $value, $ttl);

        return $value;
    }

    /**
     * Flexible caching with stale-while-revalidate pattern.
     *
     * @param int $freshTtl Fresh time in seconds
     * @param int $staleTtl Additional stale time in seconds
     */
    public function flexible(string $key, callable $callback, int $freshTtl = 3600, int $staleTtl = 7200): mixed
    {
        $cacheData = $this->getRaw($key);

        if (!$cacheData) {
            // Cache miss - generate fresh data
            $value = $callback();
            $this->putWithMetadata($key, $value, $freshTtl, $staleTtl);
            ++$this->stats['misses'];

            return $value;
        }

        $metadata = $cacheData['metadata'] ?? [];
        $isFresh = ($metadata['fresh_until'] ?? 0) > time();
        $isValid = ($metadata['stale_until'] ?? 0) > time();

        if ($isFresh) {
            // Cache hit - fresh data
            ++$this->stats['hits'];

            return $cacheData['value'];
        }

        if ($isValid) {
            // Return stale data and refresh in background
            $this->refreshInBackground($key, $callback, $freshTtl, $staleTtl);
            ++$this->stats['hits'];

            return $cacheData['value'];
        }

        // Cache expired - generate fresh data
        $value = $callback();
        $this->putWithMetadata($key, $value, $freshTtl, $staleTtl);
        ++$this->stats['misses'];

        return $value;
    }

    /**
     * Cache with tags for group invalidation.
     */
    public function tags(array $tags): self
    {
        $instance = clone $this;
        $instance->cache = $this->cache->tags($tags);

        return $instance;
    }

    /**
     * Put value in cache.
     */
    public function put(string $key, mixed $value, int $ttl = 3600): bool
    {
        ++$this->stats['writes'];

        return $this->cache->put($key, $value, $ttl);
    }

    /**
     * Get value from cache.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($key, $default);
    }

    /**
     * Check if key exists in cache.
     */
    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    /**
     * Delete key from cache.
     */
    public function forget(string $key): bool
    {
        ++$this->stats['deletes'];

        return $this->cache->forget($key);
    }

    /**
     * Flush all cache.
     */
    public function flush(): bool
    {
        return $this->cache->flush();
    }

    /**
     * Get multiple keys at once.
     */
    public function many(array $keys): array
    {
        return $this->cache->many($keys);
    }

    /**
     * Put multiple key-value pairs.
     */
    public function putMany(array $values, int $ttl = 3600): bool
    {
        $this->stats['writes'] += count($values);

        return $this->cache->putMany($values, $ttl);
    }

    /**
     * Add to cache only if key doesn't exist.
     */
    public function add(string $key, mixed $value, int $ttl = 3600): bool
    {
        if ($this->cache->add($key, $value, $ttl)) {
            ++$this->stats['writes'];

            return true;
        }

        return false;
    }

    /**
     * Increment a cached value.
     */
    public function increment(string $key, int $value = 1): false|int
    {
        return $this->cache->increment($key, $value);
    }

    /**
     * Decrement a cached value.
     */
    public function decrement(string $key, int $value = 1): false|int
    {
        return $this->cache->decrement($key, $value);
    }

    /**
     * Cache a value forever (until manually removed).
     */
    public function forever(string $key, mixed $value): bool
    {
        ++$this->stats['writes'];

        return $this->cache->forever($key, $value);
    }

    /**
     * Generate cache key with consistent formatting.
     */
    public function generateKey(string $prefix, array $params = []): string
    {
        if (empty($params)) {
            return $prefix;
        }

        $serialized = serialize($params);
        $hash = md5($serialized);

        return "{$prefix}:{$hash}";
    }

    /**
     * Get cache performance statistics.
     */
    public function getStats(): array
    {
        $total = $this->stats['hits'] + $this->stats['misses'];
        $hitRatio = $total > 0 ? ($this->stats['hits'] / $total) * 100 : 0;

        return array_merge($this->stats, [
            'hit_ratio' => round($hitRatio, 2),
            'total_requests' => $total,
        ]);
    }

    /**
     * Clear cache statistics.
     */
    public function clearStats(): void
    {
        $this->stats = [
            'hits' => 0,
            'misses' => 0,
            'writes' => 0,
            'deletes' => 0,
        ];
    }

    /**
     * Get raw cache data with metadata.
     */
    private function getRaw(string $key): ?array
    {
        return $this->cache->get($key);
    }

    /**
     * Put value with metadata for flexible caching.
     */
    private function putWithMetadata(string $key, mixed $value, int $freshTtl, int $staleTtl): bool
    {
        $data = [
            'value' => $value,
            'metadata' => [
                'created_at' => time(),
                'fresh_until' => time() + $freshTtl,
                'stale_until' => time() + $freshTtl + $staleTtl,
            ],
        ];

        ++$this->stats['writes'];

        return $this->cache->put($key, $data, $freshTtl + $staleTtl);
    }

    /**
     * Refresh cache in background (simplified version).
     */
    private function refreshInBackground(string $key, callable $callback, int $freshTtl, int $staleTtl): void
    {
        // In a real implementation, this would dispatch a background job
        // For now, we'll just set a flag to refresh on next request
        $refreshKey = "refresh:{$key}";

        if (!$this->cache->has($refreshKey)) {
            $this->cache->put($refreshKey, true, 60); // Prevent multiple refreshes

            // Dispatch background job or use queues
            dispatch(function () use ($key, $callback, $freshTtl, $staleTtl) {
                try {
                    $value = $callback();
                    $this->putWithMetadata($key, $value, $freshTtl, $staleTtl);
                    $this->forget("refresh:{$key}");
                } catch (\Exception $e) {
                    // Log error but don't fail the original request
                    logger()->error('Background cache refresh failed', [
                        'key' => $key,
                        'error' => $e->getMessage(),
                    ]);
                }
            })->onQueue('cache-refresh');
        }
    }
}
