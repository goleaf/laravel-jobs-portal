<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Base Repository Interface - Enhanced Laravel Pattern
 * 
 * Provides standardized contract for all repository implementations
 * following Laravel best practices and modern patterns.
 */
interface RepositoryInterface
{
    /**
     * Get all records with optional filtering
     * 
     * @param array $filters
     * @param array $relations
     * @param array $columns
     * @return Collection
     */
    public function all(array $filters = [], array $relations = [], array $columns = ['*']): Collection;

    /**
     * Get paginated records
     * 
     * @param int $perPage
     * @param array $filters
     * @param array $relations
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = [], array $relations = [], array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find record by ID
     * 
     * @param int|string $id
     * @param array $relations
     * @param array $columns
     * @return Model|null
     */
    public function find($id, array $relations = [], array $columns = ['*']): ?Model;

    /**
     * Find record by ID or fail
     * 
     * @param int|string $id
     * @param array $relations
     * @param array $columns
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, array $relations = [], array $columns = ['*']): Model;

    /**
     * Find record by specific field
     * 
     * @param string $field
     * @param mixed $value
     * @param array $relations
     * @param array $columns
     * @return Model|null
     */
    public function findBy(string $field, $value, array $relations = [], array $columns = ['*']): ?Model;

    /**
     * Find records by specific field
     * 
     * @param string $field
     * @param mixed $value
     * @param array $relations
     * @param array $columns
     * @return Collection
     */
    public function findAllBy(string $field, $value, array $relations = [], array $columns = ['*']): Collection;

    /**
     * Create a new record
     * 
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update record by ID
     * 
     * @param int|string $id
     * @param array $data
     * @return Model
     */
    public function update($id, array $data): Model;

    /**
     * Update or create record
     * 
     * @param array $conditions
     * @param array $data
     * @return Model
     */
    public function updateOrCreate(array $conditions, array $data): Model;

    /**
     * Delete record by ID
     * 
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool;

    /**
     * Bulk delete records
     * 
     * @param array $ids
     * @return int Number of deleted records
     */
    public function bulkDelete(array $ids): int;

    /**
     * Restore soft deleted record
     * 
     * @param int|string $id
     * @return bool
     */
    public function restore($id): bool;

    /**
     * Force delete record (permanent)
     * 
     * @param int|string $id
     * @return bool
     */
    public function forceDelete($id): bool;

    /**
     * Get count of records
     * 
     * @param array $filters
     * @return int
     */
    public function count(array $filters = []): int;

    /**
     * Check if record exists
     * 
     * @param int|string $id
     * @return bool
     */
    public function exists($id): bool;

    /**
     * Search records with full-text search
     * 
     * @param string $query
     * @param array $fields
     * @param int $limit
     * @return Collection
     */
    public function search(string $query, array $fields = [], int $limit = 50): Collection;

    /**
     * Get records with caching
     * 
     * @param string $cacheKey
     * @param int $ttl Time to live in seconds
     * @param callable $callback
     * @return mixed
     */
    public function cache(string $cacheKey, int $ttl, callable $callback);

    /**
     * Clear specific cache
     * 
     * @param string $cacheKey
     * @return bool
     */
    public function clearCache(string $cacheKey): bool;

    /**
     * Clear all model cache
     * 
     * @return bool
     */
    public function clearModelCache(): bool;

    /**
     * Get the model instance
     * 
     * @return Model
     */
    public function getModel(): Model;

    /**
     * Set model instance
     * 
     * @param Model $model
     * @return RepositoryInterface
     */
    public function setModel(Model $model): RepositoryInterface;

    /**
     * Begin database transaction
     * 
     * @return void
     */
    public function beginTransaction(): void;

    /**
     * Commit database transaction
     * 
     * @return void
     */
    public function commit(): void;

    /**
     * Rollback database transaction
     * 
     * @return void
     */
    public function rollback(): void;

    /**
     * Execute in transaction
     * 
     * @param callable $callback
     * @return mixed
     */
    public function transaction(callable $callback);
} 