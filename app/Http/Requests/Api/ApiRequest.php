<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Foundation\AbstractBaseRequest;
use App\Http\Requests\Foundation\Traits\AuditLoggingTrait;
use App\Http\Requests\Foundation\Traits\MultilingualValidationTrait;
use App\Http\Requests\Foundation\Traits\PerformanceOptimizationTrait;
use App\Http\Requests\Foundation\Traits\SecurityValidationTrait;

/**
 * API Request - Base class for API validation
 *
 * Handles validation for:
 * - REST API endpoints
 * - Authentication and authorization
 * - Rate limiting and throttling
 * - API versioning support
 * - Structured API responses
 *
 * @version 1.0.0
 *
 * @since 2024-12-28
 */
abstract class ApiRequest extends AbstractBaseRequest
{
    use AuditLoggingTrait;
    use MultilingualValidationTrait;
    use PerformanceOptimizationTrait;
    use SecurityValidationTrait;

    /**
     * Security level for API operations
     */
    protected string $securityLevel = 'high';

    /**
     * Enable performance monitoring for API operations
     */
    protected bool $performanceTracking = true;

    /**
     * Enable audit logging for API operations
     */
    protected bool $auditLoggingEnabled = true;

    /**
     * API validation modules
     */
    protected array $validationModules = [
        'api_authentication',
        'rate_limiting',
        'api_versioning',
        'structured_response',
    ];

    /**
     * Get domain-specific validation rules for API data
     */
    protected function getDomainRules(): array
    {
        return [
            'api_version' => ['sometimes', 'string', 'in:v1,v2,v3'],
            'format' => ['sometimes', 'string', 'in:json,xml'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string'],
            'order' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get domain-specific error messages for API data
     */
    protected function getDomainMessages(): array
    {
        return [
            'api_version.in' => __('validation.api.version_invalid'),
            'format.in' => __('validation.api.format_invalid'),
            'page.integer' => __('validation.api.page_integer'),
            'page.min' => __('validation.api.page_min'),
            'per_page.integer' => __('validation.api.per_page_integer'),
            'per_page.min' => __('validation.api.per_page_min'),
            'per_page.max' => __('validation.api.per_page_max'),
            'order.in' => __('validation.api.order_invalid'),
        ];
    }

    /**
     * Get domain-specific attribute names for API data
     */
    protected function getDomainAttributes(): array
    {
        return [
            'api_version' => __('validation.attributes.api_version'),
            'format' => __('validation.attributes.format'),
            'page' => __('validation.attributes.page'),
            'per_page' => __('validation.attributes.per_page'),
            'sort' => __('validation.attributes.sort'),
            'order' => __('validation.attributes.order'),
        ];
    }

    /**
     * Common validation rules for pagination
     */
    protected function getPaginationRules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'offset' => ['sometimes', 'integer', 'min:0'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Common validation rules for sorting
     */
    protected function getSortingRules(array $allowedFields = []): array
    {
        $rules = [
            'sort' => ['sometimes', 'string'],
            'order' => ['sometimes', 'string', 'in:asc,desc'],
        ];

        if (! empty($allowedFields)) {
            $rules['sort'][] = 'in:'.implode(',', $allowedFields);
        }

        return $rules;
    }

    /**
     * Common validation rules for filtering
     */
    protected function getFilteringRules(): array
    {
        return [
            'filter' => ['sometimes', 'array'],
            'search' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string'],
            'created_from' => ['sometimes', 'date'],
            'created_to' => ['sometimes', 'date', 'after_or_equal:created_from'],
        ];
    }

    /**
     * Common validation rules for API authentication
     */
    protected function getAuthenticationRules(): array
    {
        return [
            'api_key' => ['sometimes', 'string', 'size:32'],
            'access_token' => ['sometimes', 'string'],
            'client_id' => ['sometimes', 'string'],
            'client_secret' => ['sometimes', 'string'],
        ];
    }

    /**
     * Perform API-specific validation
     */
    protected function performCustomValidation($validator): void
    {
        // Log API validation attempt
        $this->logUserAction('api_validation_started', [
            'request_type' => static::class,
            'api_version' => $this->input('api_version'),
            'endpoint' => request()->path(),
        ]);

        // Validate API rate limits
        $this->validateApiRateLimits($validator);

        // Validate API version compatibility
        $this->validateApiVersionCompatibility($validator);

        // Validate pagination parameters
        $this->validatePaginationParameters($validator);

        // Validate sorting parameters
        $this->validateSortingParameters($validator);
    }

    /**
     * Validate API rate limits
     */
    protected function validateApiRateLimits($validator): void
    {
        $this->logRateLimitEvent('api_rate_limit_check', [
            'endpoint' => request()->path(),
            'method' => request()->method(),
        ]);

        // Rate limiting is typically handled by middleware
        // This method can be overridden for custom rate limit validation
    }

    /**
     * Validate API version compatibility
     */
    protected function validateApiVersionCompatibility($validator): void
    {
        $apiVersion = $this->input('api_version', 'v1');
        $supportedVersions = $this->getSupportedApiVersions();

        if (! in_array($apiVersion, $supportedVersions)) {
            $validator->errors()->add('api_version', __('validation.api.version_not_supported'));
        }
    }

    /**
     * Validate pagination parameters
     */
    protected function validatePaginationParameters($validator): void
    {
        $page = $this->input('page');
        $perPage = $this->input('per_page');

        if ($page && $perPage) {
            $maxOffset = $page * $perPage;
            $maxAllowedOffset = $this->getMaxAllowedOffset();

            if ($maxOffset > $maxAllowedOffset) {
                $validator->errors()->add('page', __('validation.api.pagination_limit_exceeded'));
            }
        }
    }

    /**
     * Validate sorting parameters
     */
    protected function validateSortingParameters($validator): void
    {
        $sort = $this->input('sort');

        if ($sort) {
            $allowedSortFields = $this->getAllowedSortFields();

            if (! empty($allowedSortFields) && ! in_array($sort, $allowedSortFields)) {
                $validator->errors()->add('sort', __('validation.api.sort_field_not_allowed'));
            }
        }
    }

    /**
     * Get supported API versions
     */
    protected function getSupportedApiVersions(): array
    {
        return ['v1', 'v2', 'v3'];
    }

    /**
     * Get maximum allowed offset for pagination
     */
    protected function getMaxAllowedOffset(): int
    {
        return 10000;
    }

    /**
     * Get allowed sort fields
     */
    protected function getAllowedSortFields(): array
    {
        // Override in specific request classes
        return [];
    }

    /**
     * Handle failed validation for API requests
     */
    protected function failedValidation($validator): void
    {
        $this->logValidationFailureAudit($validator);

        $response = response()->json([
            'success' => false,
            'message' => __('validation.api.validation_failed'),
            'errors' => $this->formatApiValidationErrors($validator),
            'meta' => [
                'api_version' => $this->input('api_version', 'v1'),
                'request_id' => $this->getRequestId(),
                'timestamp' => now()->toISOString(),
            ],
        ], 422);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Format validation errors for API response
     */
    protected function formatApiValidationErrors($validator): array
    {
        $errors = [];

        foreach ($validator->errors()->messages() as $field => $messages) {
            $errors[] = [
                'field' => $field,
                'message' => $messages[0],
                'code' => $this->getErrorCode($field),
            ];
        }

        return $errors;
    }

    /**
     * Get error code for field
     */
    protected function getErrorCode(string $field): string
    {
        $errorCodes = [
            'api_version' => 'API_VERSION_INVALID',
            'page' => 'PAGINATION_INVALID',
            'per_page' => 'PAGINATION_INVALID',
            'sort' => 'SORTING_INVALID',
            'order' => 'SORTING_INVALID',
        ];

        return $errorCodes[$field] ?? 'VALIDATION_ERROR';
    }

    /**
     * Apply API-specific sanitization
     */
    protected function applySanitization(array $data): array
    {
        $sanitized = parent::applySanitization($data);

        // API-specific sanitization
        if (isset($sanitized['sort'])) {
            $sanitized['sort'] = strtolower($sanitized['sort']);
        }

        if (isset($sanitized['order'])) {
            $sanitized['order'] = strtolower($sanitized['order']);
        }

        if (isset($sanitized['format'])) {
            $sanitized['format'] = strtolower($sanitized['format']);
        }

        return $sanitized;
    }
}
