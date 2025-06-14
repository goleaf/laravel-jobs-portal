<?php

namespace App\Repositories\Enhanced;

use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

/**
 * Enhanced Base Repository - Enhanced Laravel Pattern
 * 
 * Advanced repository implementation with caching, events, search,
 * transaction management, and performance optimization.
 */
abstract class EnhancedBaseRepository implements RepositoryInterface
{
    /**
     * The Eloquent model instance
     */
    protected Model $model;

    /**
     * Cache TTL in seconds (default: 1 hour)
     */
    protected int $cacheTtl = 3600;

    /**
     * Cache prefix for this repository
     */
    protected string $cachePrefix;

    /**
     * Searchable fields for full-text search
     */
    protected array $searchableFields = [];

    /**
     * Default relations to eager load
     */
    protected array $defaultRelations = [];

    /**
     * Fields that can be filtered
     */
    protected array $filterableFields = [];

    /**
     * Fields that can be sorted
     */
    protected array $sortableFields = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->makeModel();
        $this->setCachePrefix();
    }

    /**
     * Get the model class name
     */
    abstract protected function getModelClass(): string;

    /**
     * Make model instance
     */
    protected function makeModel(): void
    {
        $modelClass = $this->getModelClass();
        $this->model = new $modelClass;
    }

    /**
     * Set cache prefix based on model
     */
    protected function setCachePrefix(): void
    {
        $this->cachePrefix = strtolower(class_basename($this->model)) . '_cache';
    }

    /**
     * Get all records with optional filtering
     */
    public function all(array $filters = [], array $relations = [], array $columns = ['*']): Collection
    {
        $cacheKey = $this->generateCacheKey('all', $filters, $relations, $columns);
        
        return $this->cache($cacheKey, $this->cacheTtl, function () use ($filters, $relations, $columns) {
            $query = $this->buildQuery($filters, $relations);
            return $query->get($columns);
        });
    }

    /**
     * Get paginated records
     */
    public function paginate(int $perPage = 15, array $filters = [], array $relations = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->buildQuery($filters, $relations);
        
        return $query->paginate($perPage, $columns);
    }

    /**
     * Find record by ID
     */
    public function find($id, array $relations = [], array $columns = ['*']): ?Model
    {
        $cacheKey = $this->generateCacheKey('find', ['id' => $id], $relations, $columns);
        
        return $this->cache($cacheKey, $this->cacheTtl, function () use ($id, $relations, $columns) {
            $query = $this->model->newQuery();
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            return $query->find($id, $columns);
        });
    }

    /**
     * Find record by ID or fail
     */
    public function findOrFail($id, array $relations = [], array $columns = ['*']): Model
    {
        $model = $this->find($id, $relations, $columns);
        
        if (!$model) {
            throw new ModelNotFoundException("Model not found with ID: {$id}");
        }
        
        return $model;
    }

    /**
     * Find record by specific field
     */
    public function findBy(string $field, $value, array $relations = [], array $columns = ['*']): ?Model
    {
        $cacheKey = $this->generateCacheKey('findBy', [$field => $value], $relations, $columns);
        
        return $this->cache($cacheKey, $this->cacheTtl, function () use ($field, $value, $relations, $columns) {
            $query = $this->model->newQuery();
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            return $query->where($field, $value)->first($columns);
        });
    }

    /**
     * Find records by specific field
     */
    public function findAllBy(string $field, $value, array $relations = [], array $columns = ['*']): Collection
    {
        $cacheKey = $this->generateCacheKey('findAllBy', [$field => $value], $relations, $columns);
        
        return $this->cache($cacheKey, $this->cacheTtl, function () use ($field, $value, $relations, $columns) {
            $query = $this->model->newQuery();
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            return $query->where($field, $value)->get($columns);
        });
    }

    /**
     * Create a new record
     */
    public function create(array $data): Model
    {
        try {
            $model = $this->model->create($data);
            
            // Clear related caches
            $this->clearModelCache();
            
            // Log the creation
            Log::info("Created {$this->getModelName()}", ['id' => $model->id, 'data' => $data]);
            
            return $model;
        } catch (Exception $e) {
            Log::error("Failed to create {$this->getModelName()}", [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update record by ID
     */
    public function update($id, array $data): Model
    {
        try {
            $model = $this->findOrFail($id);
            $model->update($data);
            
            // Clear related caches
            $this->clearModelCache();
            $this->clearCache($this->generateCacheKey('find', ['id' => $id]));
            
            // Log the update
            Log::info("Updated {$this->getModelName()}", ['id' => $id, 'data' => $data]);
            
            return $model->fresh();
        } catch (Exception $e) {
            Log::error("Failed to update {$this->getModelName()}", [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update or create record
     */
    public function updateOrCreate(array $conditions, array $data): Model
    {
        try {
            $model = $this->model->updateOrCreate($conditions, $data);
            
            // Clear related caches
            $this->clearModelCache();
            
            return $model;
        } catch (Exception $e) {
            Log::error("Failed to updateOrCreate {$this->getModelName()}", [
                'conditions' => $conditions,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete record by ID
     */
    public function delete($id): bool
    {
        try {
            $model = $this->findOrFail($id);
            $result = $model->delete();
            
            // Clear related caches
            $this->clearModelCache();
            $this->clearCache($this->generateCacheKey('find', ['id' => $id]));
            
            // Log the deletion
            Log::info("Deleted {$this->getModelName()}", ['id' => $id]);
            
            return $result;
        } catch (Exception $e) {
            Log::error("Failed to delete {$this->getModelName()}", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Bulk delete records
     */
    public function bulkDelete(array $ids): int
    {
        try {
            $count = $this->model->whereIn('id', $ids)->delete();
            
            // Clear related caches
            $this->clearModelCache();
            
            Log::info("Bulk deleted {$count} {$this->getModelName()} records", ['ids' => $ids]);
            
            return $count;
        } catch (Exception $e) {
            Log::error("Failed to bulk delete {$this->getModelName()}", [
                'ids' => $ids,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Restore soft deleted record
     */
    public function restore($id): bool
    {
        try {
            $model = $this->model->withTrashed()->findOrFail($id);
            $result = $model->restore();
            
            // Clear related caches
            $this->clearModelCache();
            
            Log::info("Restored {$this->getModelName()}", ['id' => $id]);
            
            return $result;
        } catch (Exception $e) {
            Log::error("Failed to restore {$this->getModelName()}", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Force delete record (permanent)
     */
    public function forceDelete($id): bool
    {
        try {
            $model = $this->model->withTrashed()->findOrFail($id);
            $result = $model->forceDelete();
            
            // Clear related caches
            $this->clearModelCache();
            
            Log::info("Force deleted {$this->getModelName()}", ['id' => $id]);
            
            return $result;
        } catch (Exception $e) {
            Log::error("Failed to force delete {$this->getModelName()}", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get count of records
     */
    public function count(array $filters = []): int
    {
        $cacheKey = $this->generateCacheKey('count', $filters);
        
        return $this->cache($cacheKey, $this->cacheTtl, function () use ($filters) {
            return $this->buildQuery($filters)->count();
        });
    }

    /**
     * Check if record exists
     */
    public function exists($id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Search records with full-text search
     */
    public function search(string $query, array $fields = [], int $limit = 50): Collection
    {
        $searchFields = !empty($fields) ? $fields : $this->searchableFields;
        
        if (empty($searchFields)) {
            return collect();
        }
        
        $cacheKey = $this->generateCacheKey('search', ['query' => $query, 'fields' => $searchFields, 'limit' => $limit]);
        
        return $this->cache($cacheKey, $this->cacheTtl / 2, function () use ($query, $searchFields, $limit) {
            $builder = $this->model->newQuery();
            
            $builder->where(function ($q) use ($query, $searchFields) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$query}%");
                }
            });
            
            return $builder->limit($limit)->get();
        });
    }

    /**
     * Get records with caching
     */
    public function cache(string $cacheKey, int $ttl, callable $callback)
    {
        return Cache::remember($cacheKey, $ttl, $callback);
    }

    /**
     * Clear specific cache
     */
    public function clearCache(string $cacheKey): bool
    {
        return Cache::forget($cacheKey);
    }

    /**
     * Clear all model cache
     */
    public function clearModelCache(): bool
    {
        $tags = [$this->cachePrefix];
        Cache::tags($tags)->flush();
        return true;
    }

    /**
     * Get the model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Set model instance
     */
    public function setModel(Model $model): RepositoryInterface
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Begin database transaction
     */
    public function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    /**
     * Commit database transaction
     */
    public function commit(): void
    {
        DB::commit();
    }

    /**
     * Rollback database transaction
     */
    public function rollback(): void
    {
        DB::rollBack();
    }

    /**
     * Execute in transaction
     */
    public function transaction(callable $callback)
    {
        return DB::transaction($callback);
    }

    /**
     * Build query with filters and relations
     */
    protected function buildQuery(array $filters = [], array $relations = []): Builder
    {
        $query = $this->model->newQuery();
        
        // Apply default relations
        $allRelations = array_merge($this->defaultRelations, $relations);
        if (!empty($allRelations)) {
            $query->with($allRelations);
        }
        
        // Apply filters
        foreach ($filters as $field => $value) {
            if (in_array($field, $this->filterableFields) || empty($this->filterableFields)) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }
        
        // Apply sorting
        if (isset($filters['sort_by']) && in_array($filters['sort_by'], $this->sortableFields)) {
            $direction = $filters['sort_direction'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $direction);
        }
        
        return $query;
    }

    /**
     * Generate cache key
     */
    protected function generateCacheKey(string $method, array $params = [], array $relations = [], array $columns = ['*']): string
    {
        $key = $this->cachePrefix . '_' . $method;
        
        if (!empty($params)) {
            $key .= '_' . md5(serialize($params));
        }
        
        if (!empty($relations)) {
            $key .= '_rel_' . md5(serialize($relations));
        }
        
        if ($columns !== ['*']) {
            $key .= '_col_' . md5(serialize($columns));
        }
        
        return $key;
    }

    /**
     * Get model name for logging
     */
    protected function getModelName(): string
    {
        return class_basename($this->model);
    }
} 