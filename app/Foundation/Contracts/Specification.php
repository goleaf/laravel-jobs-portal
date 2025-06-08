<?php

namespace App\Foundation\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Specification Interface
 * 
 * Implements the specification pattern for building complex queries
 * that can be composed and reused across different contexts
 */
interface Specification
{
    /**
     * Apply specification to query builder
     *
     * @param Builder $query
     * @return Builder
     */
    public function toQuery(Builder $query): Builder;

    /**
     * Check if this specification is satisfied by an entity
     *
     * @param mixed $entity
     * @return bool
     */
    public function isSatisfiedBy(mixed $entity): bool;

    /**
     * Combine this specification with another using AND logic
     *
     * @param Specification $specification
     * @return Specification
     */
    public function and(Specification $specification): Specification;

    /**
     * Combine this specification with another using OR logic
     *
     * @param Specification $specification
     * @return Specification
     */
    public function or(Specification $specification): Specification;

    /**
     * Negate this specification
     *
     * @return Specification
     */
    public function not(): Specification;

    /**
     * Get cache key for this specification
     *
     * @return string
     */
    public function getCacheKey(): string;

    /**
     * Get cache tags for this specification
     *
     * @return array
     */
    public function getCacheTags(): array;

    /**
     * Convert specification to array for debugging
     *
     * @return array
     */
    public function toArray(): array;
}