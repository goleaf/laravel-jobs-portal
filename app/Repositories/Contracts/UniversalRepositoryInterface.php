<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Universal Repository Interface - Enhanced Contract.
 *
 * Defines the standard contract for all repository implementations
 * following Laravel best practices and service layer patterns
 */
interface UniversalRepositoryInterface
{
    /**
     * Get all records.
     */
    public function all(array $columns = ['*'], bool $useCache = true): Collection;

    /**
     * Find record by ID.
     *
     * @param mixed $id
     */
    public function find($id, array $columns = ['*'], bool $useCache = true): ?Model;

    /**
     * Find record by ID or fail.
     *
     * @param mixed $id
     */
    public function findOrFail($id, array $columns = ['*']): Model;

    /**
     * Create new record.
     */
    public function create(array $attributes): Model;

    /**
     * Update record by ID.
     *
     * @param mixed $id
     */
    public function update($id, array $attributes): bool;

    /**
     * Delete record by ID.
     *
     * @param mixed $id
     */
    public function delete($id): bool;

    /**
     * Get paginated results.
     */
    public function paginate(
        int $perPage = 15,
        array $columns = ['*'],
        string $pageName = 'page',
        ?int $page = null,
        array $filters = [],
        bool $useCache = false
    ): LengthAwarePaginator;

    /**
     * Find records by criteria.
     */
    public function findBy(array $criteria, array $columns = ['*'], bool $useCache = true): Collection;

    /**
     * Find first record by criteria.
     */
    public function findOneBy(array $criteria, array $columns = ['*']): ?Model;

    /**
     * Count records.
     */
    public function count(array $criteria = []): int;

    /**
     * Check if record exists.
     */
    public function exists(array $criteria): bool;

    /**
     * First or create record.
     */
    public function firstOrCreate(array $attributes, array $values = []): Model;

    /**
     * Update or create record.
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;

    /**
     * Search records.
     */
    public function search(string $term, array $fields = [], array $columns = ['*']): Collection;

    /**
     * Set relationships to eager load.
     */
    public function with(array $relations);

    /**
     * Process records in chunks.
     */
    public function processInChunks(int $chunkSize = 500, ?callable $callback = null): void;
}
