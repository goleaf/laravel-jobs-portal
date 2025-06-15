<?php

namespace App\Traits;

use App\Services\CacheService;

trait QueryOptimization
{
    /**
     * Scope for active records only.
     *
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for featured records.
     *
     * @param mixed $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for recent records.
     *
     * @param mixed $query
     * @param mixed $days
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Cache a query result.
     *
     * @param mixed $key
     * @param mixed $callback
     * @param mixed $duration
     */
    public function cacheQuery($key, $callback, $duration = 'medium')
    {
        $modelName = strtolower(class_basename($this));

        return CacheService::{'cache'.ucfirst($modelName).'s'}($key, $callback, $duration);
    }

    /**
     * Get cached model with relationships.
     *
     * @param mixed $id
     */
    public static function getCachedWithRelations($id, array $relations = [])
    {
        $modelName = strtolower(class_basename(static::class));
        $key = "{$modelName}_{$id}_".md5(implode('_', $relations));

        return CacheService::{'cache'.ucfirst($modelName).'s'}($key, function () use ($id, $relations) {
            return static::with($relations)->find($id);
        });
    }
}
