<?php

namespace App\Contracts;

use Illuminate\Validation\ValidationException;

/**
 * Base Service Interface - Enhanced Laravel Pattern.
 *
 * Provides standardized contract for business logic services
 * following Laravel best practices and clean architecture.
 */
interface ServiceInterface
{
    /**
     * Get service name for logging and identification.
     */
    public function getServiceName(): string;

    /**
     * Validate input data.
     *
     * @return array Validated data
     *
     * @throws ValidationException
     */
    public function validate(array $data, array $rules): array;

    /**
     * Handle service exceptions.
     *
     * @throws \Exception
     */
    public function handleException(\Exception $exception, string $operation, array $context = []): void;

    /**
     * Log service activity.
     */
    public function log(string $level, string $message, array $context = []): void;
}
