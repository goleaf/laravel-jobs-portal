<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Exception;

/**
 * Enhanced Base Repository
 * 
 * Comprehensive repository pattern implementation following Enhanced best practices
 * with caching, performance optimization, and Laravel 12 patterns
 */
abstract class EnhancedBaseRepository
{
    /**
     * The model instance
     */
    protected Model $model;
    
    /**
     * Cache TTL in minutes
     */
    protected int $cacheTtl = 60;
    
    /**
     * Cache prefix for this repository
     */
    protected string $cachePrefix;
    
    /**
     * Relationships to eager load by default
     */
    protected array $defaultWith = [];
    
    /**
     * Fields that can be searched
     */
    protected array $searchableFields = [];
    
    /**
     * Fields that can be filtered
     */
    protected array $filterableFields = [];
    
    /**
     * Fields that can be sorted
     */
    protected array $sortableFields = [];

    public function __construct()
    {
        $this->model = $this->makeModel();
        $this->cachePrefix = Str::snake(class_basename($this->model)) . '_';
    }

    /**
     * Specify Model class name
     */
    abstract protected function getModelClass(): string;

    /**
     * Make Model instance
     */
    protected function makeModel(): Model
    {
        $model = app($this->getModelClass());
        
        if (!$model instanceof Model) {
            throw new \InvalidArgumentException("Class {$this->getModelClass()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
        
        return $model;
    }

    /**
     * Get all records with caching
     */
    public function all(array $columns = ['*'], array $with = []): Collection
    {
        $cacheKey = $this->getCacheKey('all', [
            'columns' => $columns,
            'with' => array_merge($this->defaultWith, $with)
        ]);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($columns, $with) {
            return $this->model
                ->with(array_merge($this->defaultWith, $with))
                ->get($columns);
        });
    }

    /**
     * Find record by ID with caching
     */
    public function find(int $id, array $columns = ['*'], array $with = []): ?Model
    {
        $cacheKey = $this->getCacheKey('find', [
            'id' => $id,
            'columns' => $columns,
            'with' => array_merge($this->defaultWith, $with)
        ]);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $columns, $with) {
            return $this->model
                ->with(array_merge($this->defaultWith, $with))
                ->find($id, $columns);
        });
    }

    /**
     * Find record by ID or fail
     */
    public function findOrFail(int $id, array $columns = ['*'], array $with = []): Model
    {
        $model = $this->find($id, $columns, $with);
        
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                "Model " . get_class($this->model) . " with ID {$id} not found"
            );
        }
        
        return $model;
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        try {
            DB::beginTransaction();
            
            $model = $this->model->create($data);
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            
            Log::info("EnhancedRepository: Created {$this->getModelClass()}", [
                'id' => $model->id,
                'data' => $data
            ]);
            
            return $model;
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("EnhancedRepository: Failed to create {$this->getModelClass()}", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): Model
    {
        try {
            DB::beginTransaction();
            
            $model = $this->findOrFail($id);
            $model->update($data);
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            
            Log::info("EnhancedRepository: Updated {$this->getModelClass()}", [
                'id' => $id,
                'data' => $data
            ]);
            
            return $model->fresh();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("EnhancedRepository: Failed to update {$this->getModelClass()}", [
                'id' => $id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $model = $this->findOrFail($id);
            $result = $model->delete();
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            
            Log::info("EnhancedRepository: Deleted {$this->getModelClass()}", [
                'id' => $id
            ]);
            
            return $result;
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("EnhancedRepository: Failed to delete {$this->getModelClass()}", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get paginated results with advanced filtering
     */
    public function paginate(
        int $perPage = 15,
        array $filters = [],
        array $columns = ['*'],
        array $with = [],
        string $orderBy = 'id',
        string $orderDirection = 'desc'
    ): LengthAwarePaginator {
        $cacheKey = $this->getCacheKey('paginate', [
            'perPage' => $perPage,
            'filters' => $filters,
            'columns' => $columns,
            'with' => array_merge($this->defaultWith, $with),
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection
        ]);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use (
            $perPage, $filters, $columns, $with, $orderBy, $orderDirection
        ) {
            $query = $this->model->newQuery();
            
            // Apply relationships
            if (!empty($with) || !empty($this->defaultWith)) {
                $query->with(array_merge($this->defaultWith, $with));
            }
            
            // Apply filters
            $this->applyFilters($query, $filters);
            
            // Apply ordering
            if (in_array($orderBy, $this->sortableFields) || $orderBy === 'id') {
                $query->orderBy($orderBy, $orderDirection);
            }
            
            return $query->paginate($perPage, $columns);
        });
    }

    /**
     * Search records
     */
    public function search(
        string $term,
        array $fields = [],
        int $perPage = 15,
        array $with = []
    ): LengthAwarePaginator {
        $searchFields = !empty($fields) ? $fields : $this->searchableFields;
        
        if (empty($searchFields)) {
            return $this->paginate($perPage, [], ['*'], $with);
        }

        $query = $this->model->newQuery();
        
        // Apply relationships
        if (!empty($with) || !empty($this->defaultWith)) {
            $query->with(array_merge($this->defaultWith, $with));
        }
        
        // Build search query
        $query->where(function ($q) use ($term, $searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
        });
        
        return $query->paginate($perPage);
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if (!in_array($field, $this->filterableFields)) {
                continue;
            }
            
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } elseif (is_string($value) && Str::contains($value, '%')) {
                $query->where($field, 'LIKE', $value);
            } else {
                $query->where($field, $value);
            }
        }
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey(string $method, array $params = []): string
    {
        $key = $this->cachePrefix . $method;
        
        if (!empty($params)) {
            $key .= '_' . md5(serialize($params));
        }
        
        return $key;
    }

    /**
     * Clear all caches for this repository
     */
    protected function clearCaches(): void
    {
        $pattern = $this->cachePrefix . '*';
        
        // Note: This is a simplified cache clearing. In production,
        // you might want to use Redis tags or a more sophisticated approach
        Cache::flush();
        
        Log::info("EnhancedRepository: Cleared caches for {$this->getModelClass()}");
    }

    /**
     * Get model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Reset model to fresh instance
     */
    public function resetModel(): self
    {
        $this->model = $this->makeModel();
        return $this;
    }

    /**
     * Find by field
     */
    public function findByField(string $field, $value, array $columns = ['*']): Collection
    {
        $cacheKey = $this->getCacheKey('findByField', [
            'field' => $field,
            'value' => $value,
            'columns' => $columns
        ]);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($field, $value, $columns) {
            return $this->model->where($field, $value)->get($columns);
        });
    }

    /**
     * Count records
     */
    public function count(array $filters = []): int
    {
        $cacheKey = $this->getCacheKey('count', ['filters' => $filters]);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($filters) {
            $query = $this->model->newQuery();
            $this->applyFilters($query, $filters);
            return $query->count();
        });
    }

    /**
     * Check if record exists
     */
    public function exists(int $id): bool
    {
        $cacheKey = $this->getCacheKey('exists', ['id' => $id]);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->where('id', $id)->exists();
        });
    }

    /**
     * Get first record
     */
    public function first(array $filters = [], array $columns = ['*']): ?Model
    {
        $cacheKey = $this->getCacheKey('first', [
            'filters' => $filters,
            'columns' => $columns
        ]);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($filters, $columns) {
            $query = $this->model->newQuery();
            $this->applyFilters($query, $filters);
            return $query->first($columns);
        });
    }

    /**
     * Get latest records
     */
    public function latest(int $limit = 10, array $columns = ['*']): Collection
    {
        $cacheKey = $this->getCacheKey('latest', [
            'limit' => $limit,
            'columns' => $columns
        ]);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($limit, $columns) {
            return $this->model->latest()->limit($limit)->get($columns);
        });
    }

    /**
     * Bulk insert
     */
    public function bulkInsert(array $data): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $this->model->insert($data);
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            
            Log::info("EnhancedRepository: Bulk inserted {$this->getModelClass()}", [
                'count' => count($data)
            ]);
            
            return $result;
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("EnhancedRepository: Failed to bulk insert {$this->getModelClass()}", [
                'error' => $e->getMessage(),
                'count' => count($data)
            ]);
            throw $e;
        }
    }

    /**
     * Update or create record
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        try {
            DB::beginTransaction();
            
            $model = $this->model->updateOrCreate($attributes, $values);
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            
            Log::info("EnhancedRepository: Updated or created {$this->getModelClass()}", [
                'id' => $model->id,
                'attributes' => $attributes,
                'values' => $values
            ]);
            
            return $model;
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("EnhancedRepository: Failed to update or create {$this->getModelClass()}", [
                'error' => $e->getMessage(),
                'attributes' => $attributes,
                'values' => $values
            ]);
            throw $e;
        }
    }

    /**
     * Get model count with optional filters
     */
    public function getStatistics(): array
    {
        $cacheKey = $this->getCacheKey('statistics');
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return [
                'total_count' => $this->model->count(),
                'created_today' => $this->model->whereDate('created_at', today())->count(),
                'created_this_week' => $this->model->where('created_at', '>=', now()->subDays(7))->count(),
                'created_this_month' => $this->model->where('created_at', '>=', now()->subDays(30))->count(),
                'updated_today' => $this->model->whereDate('updated_at', today())->count(),
                'last_created' => $this->model->latest('created_at')->first()?->created_at,
                'last_updated' => $this->model->latest('updated_at')->first()?->updated_at,
            ];
        });
    }

    /**
     * Refresh model cache
     */
    public function refreshCache(?int $id = null): void
    {
        $this->clearCaches();
        
        // Pre-warm commonly accessed data
        if ($id) {
            $this->find($id);
        } else {
            $this->paginate();
            $this->getStatistics();
        }
    }

    /**
     * Get model with lock for update
     */
    public function findForUpdate(int $id): ?Model
    {
        return $this->model->lockForUpdate()->find($id);
    }

    /**
     * Execute callback within transaction
     */
    public function transaction(callable $callback)
    {
        return DB::transaction($callback);
    }

    /**
     * Get query builder for advanced operations
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Restore soft deleted model
     */
    public function restore(int $id): bool
    {
        if (!method_exists($this->model, 'restore')) {
            throw new InvalidArgumentException('Model does not support soft deletes');
        }
        
        try {
            $restored = $this->model->withTrashed()->find($id)?->restore();
            
            if ($restored) {
                $this->clearCaches();
                $this->logActivity('restored', $this->find($id));
            }
            
            return (bool) $restored;
        } catch (Exception $e) {
            $this->logError('Failed to restore model', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Force delete model (permanent)
     */
    public function forceDelete(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $model = $this->model->withTrashed()->findOrFail($id);
                $modelData = $model->toArray();
                
                $deleted = $model->forceDelete();
                
                if ($deleted) {
                    $this->clearCaches();
                    $this->logActivity('force_deleted', null, ['deleted_data' => $modelData]);
                }
                
                return $deleted;
            });
        } catch (Exception $e) {
            $this->logError('Failed to force delete model', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Log repository activity
     */
    protected function logActivity(string $action, ?Model $model = null, array $extra = []): void
    {
        $data = [
            'repository' => static::class,
            'action' => $action,
            'model_type' => $this->getModelClass(),
            'model_id' => $model?->id,
            'user_id' => auth()->id(),
            'timestamp' => now(),
            ...$extra
        ];
        
        Log::info('Repository activity', $data);
        
        // Could also store in activity_log table if needed
        if (class_exists(\Spatie\Activitylog\Models\Activity::class) && $model) {
            activity()
                ->performedOn($model)
                ->withProperties($extra)
                ->log($action);
        }
    }

    /**
     * Log repository errors
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error($message, [
            'repository' => static::class,
            'model_type' => $this->getModelClass(),
            'user_id' => auth()->id(),
            'timestamp' => now(),
            ...$context
        ]);
    }
}