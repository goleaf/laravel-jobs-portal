<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Base Repository Interface - Enhanced Laravel Pattern.
 *
 * Provides standardized contract for all repository implementations
 * following Laravel best practices and modern patterns.
 */
interface RepositoryInterface
{
    /**
     * Get all records with optional filtering.
     */
    public function all(array $filters = [], array $relations = [], array $columns = ['*']): Collection;

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15, array $filters = [], array $relations = [], array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find record by ID.
     *
     * @param int|string $id
     */
    public function find($id, array $relations = [], array $columns = ['*']): ?Model;

    /**
     * Find record by ID or fail.
     *
     * @param int|string $id
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail($id, array $relations = [], array $columns = ['*']): Model;

    /**
     * Find record by specific field.
     *
     * @param mixed $value
     */
    public function findBy(string $field, $value, array $relations = [], array $columns = ['*']): ?Model;

    /**
     * Find records by specific field.
     *
     * @param mixed $value
     */
    public function findAllBy(string $field, $value, array $relations = [], array $columns = ['*']): Collection;

    /**
     * Create a new record.
     */
    public function create(array $data): Model;

    /**
     * Update record by ID.
     *
     * @param int|string $id
     */
    public function update($id, array $data): Model;

    /**
     * Update or create record.
     */
    public function updateOrCreate(array $conditions, array $data): Model;

    /**
     * Delete record by ID.
     *
     * @param int|string $id
     */
    public function delete($id): bool;

    /**
     * Bulk delete records.
     *
     * @return int Number of deleted records
     */
    public function bulkDelete(array $ids): int;

    /**
     * Restore soft deleted record.
     *
     * @param int|string $id
     */
    public function restore($id): bool;

    /**
     * Force delete record (permanent).
     *
     * @param int|string $id
     */
    public function forceDelete($id): bool;

    /**
     * Get count of records.
     */
    public function count(array $filters = []): int;

    /**
     * Check if record exists.
     *
     * @param int|string $id
     */
    public function exists($id): bool;

    /**
     * Search records with full-text search.
     */
    public function search(string $query, array $fields = [], int $limit = 50): Collection;

    /**
     * Get records with caching.
     *
     * @param int $ttl Time to live in seconds
     *
     * @return mixed
     */
    public function cache(string $cacheKey, int $ttl, callable $callback);

    /**
     * Clear specific cache.
     */
    public function clearCache(string $cacheKey): bool;

    /**
     * Clear all model cache.
     */
    public function clearModelCache(): bool;

    /**
     * Get the model instance.
     */
    public function getModel(): Model;

    /**
     * Set model instance.
     */
    public function setModel(Model $model): RepositoryInterface;

    /**
     * Begin database transaction.
     */
    public function beginTransaction(): void;

    /**
     * Commit database transaction.
     */
    public function commit(): void;

    /**
     * Rollback database transaction.
     */
    public function rollback(): void;

    /**
     * Execute in transaction.
     *
     * @return mixed
     */
    public function transaction(callable $callback);
}
