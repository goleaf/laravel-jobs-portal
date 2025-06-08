<?php

namespace App\Foundation\Contracts;

/**
 * Query Interface
 * 
 * Represents a query in the CQRS pattern - a request for data that doesn't change state.
 * Queries can be cached and optimized for read performance.
 */
interface Query
{
    /**
     * Check if this query result can be cached
     *
     * @return bool
     */
    public function isCacheable(): bool;

    /**
     * Get the cache key for this query
     *
     * @return string
     */
    public function getCacheKey(): string;

    /**
     * Get cache TTL in seconds
     *
     * @return int
     */
    public function getCacheTtl(): int;

    /**
     * Get cache tags for this query
     *
     * @return array
     */
    public function getCacheTags(): array;

    /**
     * Convert query to array for logging/debugging
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Get the unique identifier for this query
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get filters applied to this query
     *
     * @return array
     */
    public function getFilters(): array;

    /**
     * Get sorting criteria for this query
     *
     * @return array
     */
    public function getSorting(): array;

    /**
     * Get pagination parameters
     *
     * @return array
     */
    public function getPagination(): array;
}