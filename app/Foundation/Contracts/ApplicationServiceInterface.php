<?php

namespace App\Foundation\Contracts;

/**
 * Application Service Interface.
 *
 * Defines the contract for application services that orchestrate
 * business operations using command and query patterns
 */
interface ApplicationServiceInterface
{
    /**
     * Execute a command with transaction safety.
     */
    public function executeCommand(Command $command): mixed;

    /**
     * Execute a query with caching strategy.
     */
    public function executeQuery(Query $query): mixed;

    /**
     * Get service metrics for monitoring.
     */
    public function getMetrics(): array;
}
