<?php

namespace App\Foundation\Contracts;

use Illuminate\Support\Collection;

/**
 * Repository Interface.
 *
 * Defines the contract for repositories providing data access abstraction
 * with specification pattern support for complex queries
 */
interface Repository
{
    /**
     * Find entity by ID.
     *
     * @return null|mixed
     */
    public function findById(mixed $id): mixed;

    /**
     * Find entities by specification.
     */
    public function findBySpecification(Specification $specification): Collection;

    /**
     * Find one entity by specification.
     *
     * @return null|mixed
     */
    public function findOneBySpecification(Specification $specification): mixed;

    /**
     * Get all entities.
     */
    public function findAll(): Collection;

    /**
     * Save entity.
     */
    public function save(mixed $entity): mixed;

    /**
     * Delete entity by ID.
     */
    public function delete(mixed $id): bool;

    /**
     * Check if entity exists.
     */
    public function exists(mixed $id): bool;

    /**
     * Count entities by specification.
     */
    public function count(?Specification $specification = null): int;

    /**
     * Get paginated results by specification.
     */
    public function paginate(Specification $specification, int $page = 1, int $perPage = 15): mixed;

    /**
     * Clear repository cache.
     */
    public function clearCache(): void;
}
