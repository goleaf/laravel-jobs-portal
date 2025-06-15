<?php

namespace App\Services;

use App\Contracts\ServiceInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Base Service - Enhanced Laravel Pattern.
 *
 * Abstract base service providing common functionality for business logic services
 * including validation, logging, exception handling, and transaction management.
 */
abstract class BaseService implements ServiceInterface
{
    /**
     * Service name for logging.
     */
    protected string $serviceName;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->serviceName = class_basename(static::class);
    }

    /**
     * Get service name for logging and identification.
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * Validate input data.
     */
    public function validate(array $data, array $rules): array
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->log('warning', 'Validation failed', [
                'errors' => $validator->errors()->toArray(),
                'data' => $this->sanitizeLogData($data),
            ]);

            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Handle service exceptions.
     */
    public function handleException(\Exception $exception, string $operation, array $context = []): void
    {
        $this->log('error', "Exception in {$operation}", [
            'exception' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => $context,
        ]);

        throw $exception;
    }

    /**
     * Log service activity.
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $logContext = array_merge([
            'service' => $this->serviceName,
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::log($level, "[{$this->serviceName}] {$message}", $logContext);
    }

    /**
     * Execute operation with error handling.
     */
    protected function executeOperation(string $operation, callable $callback, array $context = [])
    {
        try {
            $this->log('info', "Starting {$operation}", $context);

            $result = $callback();

            $this->log('info', "Completed {$operation}", array_merge($context, [
                'success' => true,
            ]));

            return $result;
        } catch (\Exception $e) {
            $this->handleException($e, $operation, $context);
        }
    }

    /**
     * Validate and execute operation.
     */
    protected function validateAndExecute(array $data, array $rules, string $operation, callable $callback)
    {
        $validatedData = $this->validate($data, $rules);

        return $this->executeOperation($operation, function () use ($callback, $validatedData) {
            return $callback($validatedData);
        }, ['operation' => $operation]);
    }

    /**
     * Sanitize data for logging (remove sensitive information).
     */
    protected function sanitizeLogData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'secret',
            'api_key',
            'private_key',
            'credit_card',
            'ssn',
            'social_security_number',
        ];

        $sanitized = $data;

        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '[REDACTED]';
            }
        }

        return $sanitized;
    }

    /**
     * Generate operation context for logging.
     */
    protected function createContext(array $additional = []): array
    {
        return array_merge([
            'service' => $this->serviceName,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $additional);
    }

    /**
     * Check if user is authorized for operation.
     *
     * @param null|mixed $resource
     */
    protected function authorize(string $ability, $resource = null): bool
    {
        if (!auth()->check()) {
            $this->log('warning', 'Unauthorized access attempt', [
                'ability' => $ability,
                'resource' => $resource,
            ]);

            return false;
        }

        $authorized = auth()->user()->can($ability, $resource);

        if (!$authorized) {
            $this->log('warning', 'Authorization failed', [
                'user_id' => auth()->id(),
                'ability' => $ability,
                'resource' => $resource,
            ]);
        }

        return $authorized;
    }

    /**
     * Format success response.
     *
     * @param null|mixed $data
     */
    protected function successResponse($data = null, string $message = 'Operation completed successfully', array $meta = []): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Format error response.
     *
     * @param null|mixed $errors
     */
    protected function errorResponse(string $message, $errors = null, int $code = 400, array $meta = []): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'code' => $code,
            'meta' => $meta,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Cache operation result.
     */
    protected function cacheResult(string $key, int $ttl, callable $callback)
    {
        return cache()->remember($key, $ttl, $callback);
    }

    /**
     * Clear cache for service.
     */
    protected function clearServiceCache(array $keys = []): void
    {
        if (empty($keys)) {
            // Clear all cache for this service
            $pattern = strtolower($this->serviceName).'_*';
        // Implementation depends on cache driver
        } else {
            foreach ($keys as $key) {
                cache()->forget($key);
            }
        }
    }

    /**
     * Execute database transaction.
     */
    protected function executeInTransaction(callable $callback)
    {
        return \DB::transaction($callback);
    }

    /**
     * Dispatch event.
     *
     * @param mixed $event
     */
    protected function dispatchEvent($event): void
    {
        event($event);

        $this->log('info', 'Event dispatched', [
            'event_class' => get_class($event),
        ]);
    }

    /**
     * Queue job.
     *
     * @param mixed $job
     */
    protected function queueJob($job): void
    {
        dispatch($job);

        $this->log('info', 'Job queued', [
            'job_class' => get_class($job),
        ]);
    }

    /**
     * Send notification.
     *
     * @param mixed $notifiable
     * @param mixed $notification
     */
    protected function sendNotification($notifiable, $notification): void
    {
        $notifiable->notify($notification);

        $this->log('info', 'Notification sent', [
            'notification_class' => get_class($notification),
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id ?? null,
        ]);
    }
}
