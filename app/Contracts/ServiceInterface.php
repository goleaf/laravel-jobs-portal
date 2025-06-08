<?php

namespace App\Contracts;

/**
 * Base Service Interface - Context7 Laravel Pattern
 * 
 * Provides standardized contract for business logic services
 * following Laravel best practices and clean architecture.
 */
interface ServiceInterface
{
    /**
     * Get service name for logging and identification
     * 
     * @return string
     */
    public function getServiceName(): string;

    /**
     * Validate input data
     * 
     * @param array $data
     * @param array $rules
     * @return array Validated data
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $data, array $rules): array;

    /**
     * Handle service exceptions
     * 
     * @param \Exception $exception
     * @param string $operation
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public function handleException(\Exception $exception, string $operation, array $context = []): void;

    /**
     * Log service activity
     * 
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log(string $level, string $message, array $context = []): void;
} 