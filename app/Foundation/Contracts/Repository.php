<?php

namespace App\Foundation\Contracts;

use Illuminate\Support\Collection;

/**
 * Repository Interface
 * 
 * Defines the contract for repositories providing data access abstraction
 * with specification pattern support for complex queries
 */
interface Repository
{
    /**
     * Find entity by ID
     *
     * @param mixed $id
     * @return mixed|null
     */
    public function findById(mixed $id): mixed;

    /**
     * Find entities by specification
     *
     * @param Specification $specification
     * @return Collection
     */
    public function findBySpecification(Specification $specification): Collection;

    /**
     * Find one entity by specification
     *
     * @param Specification $specification
     * @return mixed|null
     */
    public function findOneBySpecification(Specification $specification): mixed;

    /**
     * Get all entities
     *
     * @return Collection
     */
    public function findAll(): Collection;

    /**
     * Save entity
     *
     * @param mixed $entity
     * @return mixed
     */
    public function save(mixed $entity): mixed;

    /**
     * Delete entity by ID
     *
     * @param mixed $id
     * @return bool
     */
    public function delete(mixed $id): bool;

    /**
     * Check if entity exists
     *
     * @param mixed $id
     * @return bool
     */
    public function exists(mixed $id): bool;

    /**
     * Count entities by specification
     *
     * @param Specification|null $specification
     * @return int
     */
    public function count(?Specification $specification = null): int;

    /**
     * Get paginated results by specification
     *
     * @param Specification $specification
     * @param int $page
     * @param int $perPage
     * @return mixed
     */
    public function paginate(Specification $specification, int $page = 1, int $perPage = 15): mixed;

    /**
     * Clear repository cache
     *
     * @return void
     */
    public function clearCache(): void;
}