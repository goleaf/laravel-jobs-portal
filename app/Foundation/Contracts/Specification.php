<?php

namespace App\Foundation\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Specification Interface.
 *
 * Implements the specification pattern for building complex queries
 * that can be composed and reused across different contexts
 */
interface Specification
{
    /**
     * Apply specification to query builder.
     */
    public function toQuery(Builder $query): Builder;

    /**
     * Check if this specification is satisfied by an entity.
     */
    public function isSatisfiedBy(mixed $entity): bool;

    /**
     * Combine this specification with another using AND logic.
     */
    public function and(Specification $specification): Specification;

    /**
     * Combine this specification with another using OR logic.
     */
    public function or(Specification $specification): Specification;

    /**
     * Negate this specification.
     */
    public function not(): Specification;

    /**
     * Get cache key for this specification.
     */
    public function getCacheKey(): string;

    /**
     * Get cache tags for this specification.
     */
    public function getCacheTags(): array;

    /**
     * Convert specification to array for debugging.
     */
    public function toArray(): array;
}
