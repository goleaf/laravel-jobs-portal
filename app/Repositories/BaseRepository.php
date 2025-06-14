<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Universal Base Repository - Enhanced Implementation
 * 
 * Following Laravel best practices for repository pattern with:
 * - Caching layer integration
 * - Query optimization patterns
 * - Service layer compatibility
 * - Memory-efficient data processing
 * - Advanced relationship handling
 */
abstract class BaseRepository
{
    protected Model $model;
    protected int $cacheMinutes = 60;
    protected string $cachePrefix = 'universal_repo';
    protected array $with = [];
    protected array $withCount = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get all records with optional filtering
     */
    public function all(array $filters = [], array $relations = [], array $columns = ['*']): Collection
    {
        $cacheKey = $this->getCacheKey('all', $columns);

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $query = $this->newQuery();
        
        if (!empty($this->with)) {
            $query->with($this->with);
        }

        if (!empty($this->withCount)) {
            $query->withCount($this->withCount);
        }

        $results = $query->get($columns);

        if ($useCache) {
            Cache::put($cacheKey, $results, now()->addMinutes($this->cacheMinutes));
        }

        return $results;
    }

    /**
     * Find record by ID with caching
     */
    public function find($id, array $columns = ['*'], bool $useCache = true): ?Model
    {
        $cacheKey = $this->getCacheKey('find', [$id, $columns]);

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $query = $this->newQuery();
        
        if (!empty($this->with)) {
            $query->with($this->with);
        }

        $result = $query->find($id, $columns);

        if ($useCache && $result) {
            Cache::put($cacheKey, $result, now()->addMinutes($this->cacheMinutes));
        }

        return $result;
    }

    /**
     * Find record by ID or fail
     */
    public function findOrFail($id, array $columns = ['*']): Model
    {
        return $this->newQuery()->findOrFail($id, $columns);
    }

    /**
     * Create new record
     */
    public function create(array $attributes): Model
    {
        $record = $this->model->create($attributes);
        
        $this->clearModelCache();
        
        Log::info('Universal Repository: Record created', [
            'model' => get_class($this->model),
            'id' => $record->getKey(),
            'attributes' => $attributes
        ]);

        return $record;
    }

    /**
     * Update record by ID
     */
    public function update($id, array $attributes): bool
    {
        $record = $this->findOrFail($id);
        $updated = $record->update($attributes);
        
        $this->clearModelCache($id);
        
        Log::info('Universal Repository: Record updated', [
            'model' => get_class($this->model),
            'id' => $id,
            'attributes' => $attributes
        ]);

        return $updated;
    }

    /**
     * Delete record by ID
     */
    public function delete($id): bool
    {
        $record = $this->findOrFail($id);
        $deleted = $record->delete();
        
        $this->clearModelCache($id);
        
        Log::info('Universal Repository: Record deleted', [
            'model' => get_class($this->model),
            'id' => $id
        ]);

        return $deleted;
    }

    /**
     * Get paginated results with advanced filtering
     */
    public function paginate(
        int $perPage = 15, 
        array $columns = ['*'], 
        string $pageName = 'page', 
        ?int $page = null,
        array $filters = [],
        bool $useCache = false
    ): LengthAwarePaginator {
        $query = $this->newQuery();

        // Apply relationships
        if (!empty($this->with)) {
            $query->with($this->with);
        }

        if (!empty($this->withCount)) {
            $query->withCount($this->withCount);
        }

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        return $query->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * Process large datasets in chunks for memory efficiency
     */
    public function processInChunks(int $chunkSize = 500, callable $callback = null): void
    {
        $this->newQuery()->chunk($chunkSize, function ($records) use ($callback) {
            if ($callback) {
                $callback($records);
            }
            
            foreach ($records as $record) {
                // Default processing - can be overridden
                $this->processRecord($record);
            }
        });
    }

    /**
     * Find records by criteria with caching
     */
    public function findBy(array $criteria, array $columns = ['*'], bool $useCache = true): Collection
    {
        $cacheKey = $this->getCacheKey('findBy', [$criteria, $columns]);

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $query = $this->newQuery();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        $results = $query->get($columns);

        if ($useCache) {
            Cache::put($cacheKey, $results, now()->addMinutes($this->cacheMinutes));
        }

        return $results;
    }

    /**
     * Find first record by criteria
     */
    public function findOneBy(array $criteria, array $columns = ['*']): ?Model
    {
        $query = $this->newQuery();

        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        return $query->first($columns);
    }

    /**
     * Count records with criteria
     */
    public function count(array $criteria = []): int
    {
        $query = $this->newQuery();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }

    /**
     * Check if record exists
     */
    public function exists(array $criteria): bool
    {
        $query = $this->newQuery();

        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        return $query->exists();
    }

    /**
     * Get first record or create if not exists
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        $record = $this->model->firstOrCreate($attributes, $values);
        
        if ($record->wasRecentlyCreated) {
            $this->clearModelCache();
        }

        return $record;
    }

    /**
     * Update or create record
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        $record = $this->model->updateOrCreate($attributes, $values);
        
        $this->clearModelCache();

        return $record;
    }

    /**
     * Bulk insert records
     */
    public function bulkInsert(array $data): bool
    {
        $inserted = $this->model->insert($data);
        
        if ($inserted) {
            $this->clearModelCache();
            
            Log::info('Universal Repository: Bulk insert completed', [
                'model' => get_class($this->model),
                'count' => count($data)
            ]);
        }

        return $inserted;
    }

    /**
     * Search records with full-text search capabilities
     */
    public function search(string $term, array $fields = [], array $columns = ['*']): Collection
    {
        $query = $this->newQuery();

        if (empty($fields)) {
            $fields = $this->getSearchableFields();
        }

        $query->where(function ($q) use ($term, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
        });

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        return $query->get($columns);
    }

    /**
     * Set relationships to eager load
     */
    public function with(array $relations)
    {
        $this->with = $relations;
        return $this;
    }

    /**
     * Set relationship counts to load
     */
    public function withCount(array $relations)
    {
        $this->withCount = $relations;
        return $this;
    }

    /**
     * Create new query instance
     */
    protected function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Apply filters to query - can be overridden in child classes
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        // Basic filter implementation - override in child repositories
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Process individual record - override in child classes
     */
    protected function processRecord(Model $record): void
    {
        // Default implementation - override in child repositories
    }

    /**
     * Get searchable fields - override in child classes
     */
    protected function getSearchableFields(): array
    {
        return ['name', 'title', 'description'];
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey(string $method, array $params = []): string
    {
        $modelName = class_basename($this->model);
        $paramsHash = md5(serialize($params));
        
        return "{$this->cachePrefix}:{$modelName}:{$method}:{$paramsHash}";
    }

    /**
     * Clear model-specific cache
     */
    protected function clearModelCache($id = null): void
    {
        $modelName = class_basename($this->model);
        $pattern = "{$this->cachePrefix}:{$modelName}:*";
        
        // Clear all cached entries for this model
        $keys = Cache::getRedis()->keys($pattern);
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
        
        Log::debug('Universal Repository: Cache cleared', [
            'model' => $modelName,
            'pattern' => $pattern,
            'id' => $id
        ]);
    }

    /**
     * Get fresh instance without cache
     */
    public function fresh(): self
    {
        return new static($this->model);
    }

    /**
     * Enable query logging for debugging
     */
    public function enableQueryLog(): self
    {
        \DB::enableQueryLog();
        return $this;
    }

    /**
     * Get executed queries for debugging
     */
    public function getQueryLog(): array
    {
        return \DB::getQueryLog();
    }
} 