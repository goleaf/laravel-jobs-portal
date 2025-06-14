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

    public function __construct(?Model $model = null)
    {
        if ($model) {
            $this->model = $model;
        } else {
            $this->model = $this->makeModel();
        }
    }

    /**
     * Make model instance
     */
    public function makeModel(): Model
    {
        $modelClass = $this->model();
        return app($modelClass);
    }

    /**
     * Specify Model class name
     */
    abstract public function model();

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
    public function all(array $filters = [], array $relations = [], array $columns = ['*'], bool $useCache = true): Collection
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
        
        if (!empty($this->with)) {
            $query->with($this->with);
        }

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $results = $query->get($columns);

        if ($useCache) {
            Cache::put($cacheKey, $results, now()->addMinutes($this->cacheMinutes));
        }

        return $results;
    }

    /**
     * Find single record by criteria
     */
    public function findOneBy(array $criteria, array $columns = ['*']): ?Model
    {
        $query = $this->newQuery();
        
        if (!empty($this->with)) {
            $query->with($this->with);
        }

        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        return $query->first($columns);
    }

    /**
     * Count records by criteria
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
     * Check if records exist by criteria
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
     * First or create record
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        $record = $this->model->firstOrCreate($attributes, $values);
        
        $this->clearModelCache();
        
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
        
        $this->clearModelCache();
        
        Log::info('Universal Repository: Bulk insert completed', [
            'model' => get_class($this->model),
            'count' => count($data)
        ]);

        return $inserted;
    }

    /**
     * Search records by term in specified fields
     */
    public function search(string $term, array $fields = [], array $columns = ['*']): Collection
    {
        $query = $this->newQuery();
        
        if (!empty($this->with)) {
            $query->with($this->with);
        }

        if (empty($fields)) {
            $fields = $this->getSearchableFields();
        }

        $query->where(function ($q) use ($term, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
        });

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
     * Set relationships to count
     */
    public function withCount(array $relations)
    {
        $this->withCount = $relations;
        return $this;
    }

    /**
     * Get new query builder instance
     */
    protected function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } elseif (strpos($field, '_like') !== false) {
                    $actualField = str_replace('_like', '', $field);
                    $query->where($actualField, 'LIKE', "%{$value}%");
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Process individual record (override in child classes)
     */
    protected function processRecord(Model $record): void
    {
        // Override in child classes for custom processing
    }

    /**
     * Get searchable fields (override in child classes)
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
     * Clear model cache
     */
    protected function clearModelCache($id = null): void
    {
        $modelName = class_basename($this->model);
        $pattern = "{$this->cachePrefix}:{$modelName}:*";
        
        // Clear all cache keys matching the pattern
        $keys = Cache::getRedis()->keys($pattern);
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }

        // Clear specific record cache if ID provided
        if ($id) {
            $specificKey = "{$this->cachePrefix}:{$modelName}:find:" . md5(serialize([$id, ['*']]));
            Cache::forget($specificKey);
        }
    }

    /**
     * Reset repository to fresh state
     */
    public function fresh(): self
    {
        $this->with = [];
        $this->withCount = [];
        return $this;
    }

    /**
     * Enable query logging
     */
    public function enableQueryLog(): self
    {
        \DB::enableQueryLog();
        return $this;
    }

    /**
     * Get query log
     */
    public function getQueryLog(): array
    {
        return \DB::getQueryLog();
    }
} 