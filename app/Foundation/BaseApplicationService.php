<?php

namespace App\Foundation;

use App\Foundation\Contracts\ApplicationServiceInterface;
use App\Foundation\Contracts\Command;
use App\Foundation\Contracts\Query;
use App\Services\Cache\CacheManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

/**
 * Base Application Service.
 *
 * Implements the application service pattern with:
 * - Command/Query separation (CQRS)
 * - Transaction management
 * - Event handling
 * - Caching strategies
 * - Error handling
 */
abstract class BaseApplicationService implements ApplicationServiceInterface
{
    protected CacheManager $cacheManager;
    protected Collection $domainEvents;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
        $this->domainEvents = collect();
    }

    /**
     * Execute a command with transaction safety.
     *
     * @throws \Exception
     */
    public function executeCommand(Command $command): mixed
    {
        return DB::transaction(function () use ($command) {
            try {
                $result = $this->handleCommand($command);
                $this->dispatchEvents();
                $this->clearRelatedCache($command);

                return $result;
            } catch (\Exception $e) {
                $this->handleCommandFailure($command, $e);

                throw $e;
            }
        });
    }

    /**
     * Execute a query with caching strategy.
     */
    public function executeQuery(Query $query): mixed
    {
        if (!$query->isCacheable()) {
            return $this->handleQuery($query);
        }

        return $this->cacheManager->remember(
            $query->getCacheKey(),
            fn () => $this->handleQuery($query),
            $query->getCacheTtl()
        );
    }

    /**
     * Get service metrics for monitoring.
     */
    public function getMetrics(): array
    {
        return [
            'service' => static::class,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'pending_events' => $this->domainEvents->count(),
        ];
    }

    /**
     * Handle command execution - to be implemented by concrete services.
     */
    abstract protected function handleCommand(Command $command): mixed;

    /**
     * Handle query execution - to be implemented by concrete services.
     */
    abstract protected function handleQuery(Query $query): mixed;

    /**
     * Add domain event to be dispatched after successful command execution.
     */
    protected function addDomainEvent(mixed $event): void
    {
        $this->domainEvents->push($event);
    }

    /**
     * Dispatch all collected domain events.
     */
    protected function dispatchEvents(): void
    {
        $this->domainEvents->each(function ($event) {
            Event::dispatch($event);
        });

        $this->domainEvents = collect();
    }

    /**
     * Handle command failure - log, notify, etc.
     */
    protected function handleCommandFailure(Command $command, \Exception $exception): void
    {
        logger()->error('Command execution failed', [
            'command' => get_class($command),
            'command_data' => $command->toArray(),
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Clear cache related to the command.
     */
    protected function clearRelatedCache(Command $command): void
    {
        $tags = $command->getCacheTags();

        if (!empty($tags)) {
            $this->cacheManager->tags($tags)->flush();
        }
    }

    /**
     * Validate command before execution.
     *
     * @throws \Exception
     */
    protected function validateCommand(Command $command): bool
    {
        if (!$command->isValid()) {
            throw new \Exception('Invalid command: '.implode(', ', $command->getValidationErrors()));
        }

        return true;
    }

    /**
     * Execute multiple commands in a single transaction.
     *
     * @throws \Exception
     */
    protected function executeMultipleCommands(array $commands): array
    {
        return DB::transaction(function () use ($commands) {
            $results = [];

            foreach ($commands as $command) {
                $this->validateCommand($command);
                $results[] = $this->handleCommand($command);
            }

            $this->dispatchEvents();

            return $results;
        });
    }
}
