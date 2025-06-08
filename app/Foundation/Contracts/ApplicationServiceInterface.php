<?php

namespace App\Foundation\Contracts;

/**
 * Application Service Interface
 * 
 * Defines the contract for application services that orchestrate
 * business operations using command and query patterns
 */
interface ApplicationServiceInterface
{
    /**
     * Execute a command with transaction safety
     *
     * @param Command $command
     * @return mixed
     */
    public function executeCommand(Command $command): mixed;

    /**
     * Execute a query with caching strategy
     *
     * @param Query $query
     * @return mixed
     */
    public function executeQuery(Query $query): mixed;

    /**
     * Get service metrics for monitoring
     *
     * @return array
     */
    public function getMetrics(): array;
}