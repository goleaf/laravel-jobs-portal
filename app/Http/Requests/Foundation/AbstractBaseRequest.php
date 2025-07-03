<?php

namespace App\Http\Requests\Foundation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Foundation\Traits\SecurityValidationTrait;
use App\Http\Requests\Foundation\Traits\MultilingualValidationTrait;
use App\Http\Requests\Foundation\Traits\PerformanceOptimizationTrait;
use App\Http\Requests\Foundation\Traits\AuditLoggingTrait;

/**
 * Abstract Base Request - Universal parent class for all request validation files
 * 
 * Implements enterprise-grade validation patterns with:
 * - Domain-specific validation inheritance
 * - Cross-cutting concerns via traits
 * - Performance optimization
 * - Multilingual error messages
 * - Security validation patterns
 * - Comprehensive audit logging
 * 
 * @package App\Http\Requests\Foundation
 * @version 1.0.0
 * @since 2024-12-28
 */
abstract class AbstractBaseRequest extends FormRequest
{
    use SecurityValidationTrait,
        MultilingualValidationTrait,
        PerformanceOptimizationTrait,
        AuditLoggingTrait;

    /**
     * Security level for this request type
     * Values: 'low', 'medium', 'high', 'critical'
     */
    protected string $securityLevel = 'medium';

    /**
     * Validation modules to load for this request
     */
    protected array $validationModules = [];

    /**
     * Domain-specific rules for this request type
     */
    protected array $domainRules = [];

    /**
     * Performance tracking enabled
     */
    protected bool $performanceTracking = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Default authorization - can be overridden in specific requests
        // Note: Authentication system will be removed as per requirements
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $startTime = microtime(true);
        
        $rules = array_merge(
            $this->getDomainRules(),
            $this->getSecurityRules(),
            $this->getBusinessLogicRules(),
            $this->getCustomRules()
        );

        if ($this->performanceTracking) {
            $executionTime = (microtime(true) - $startTime) * 1000;
            \Log::debug('Validation rules generation', [
                'request_class' => static::class,
                'execution_time_ms' => $executionTime,
                'rules_count' => count($rules)
            ]);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(
            $this->getMultilingualMessages(),
            $this->getDomainMessages(),
            $this->getCustomMessages()
        );
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return array_merge(
            $this->getMultilingualAttributes(),
            $this->getDomainAttributes(),
            $this->getCustomAttributes()
        );
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $this->performCustomValidation($validator);
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        $this->logValidationFailure($validator);

        if ($this->expectsJson()) {
            $response = response()->json([
                'status' => 'error',
                'message' => __('validation.failed'),
                'errors' => $this->formatValidationErrors($validator),
                'meta' => [
                    'request_id' => $this->getRequestId(),
                    'timestamp' => now()->toISOString(),
                    'locale' => app()->getLocale(),
                ]
            ], 422);
        } else {
            $response = redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        throw new HttpResponseException($response);
    }

    /**
     * Get domain-specific validation rules
     * Must be implemented by domain base classes
     */
    abstract protected function getDomainRules(): array;

    /**
     * Get business logic validation rules
     * Must be implemented by specific request classes
     */
    abstract protected function getBusinessLogicRules(): array;

    /**
     * Get security validation rules
     */
    protected function getSecurityRules(): array
    {
        $rules = [];

        // Basic security rules based on security level
        if (in_array($this->securityLevel, ['high', 'critical'])) {
            // Add CSRF protection validation
            // Add rate limiting validation
            // Add input sanitization validation
        }

        return $rules;
    }

    /**
     * Get custom validation rules specific to this request
     */
    protected function getCustomRules(): array
    {
        return [];
    }

    /**
     * Get multilingual error messages
     */
    protected function getMultilingualMessages(): array
    {
        $locale = app()->getLocale();
        return [
            'required' => __('validation.required', [], $locale),
            'string' => __('validation.string', [], $locale),
            'email' => __('validation.email', [], $locale),
            'max' => __('validation.max.string', [], $locale),
            'min' => __('validation.min.string', [], $locale),
            'exists' => __('validation.exists', [], $locale),
            'unique' => __('validation.unique', [], $locale),
        ];
    }

    /**
     * Get domain-specific error messages
     */
    protected function getDomainMessages(): array
    {
        return [];
    }

    /**
     * Get custom error messages specific to this request
     */
    protected function getCustomMessages(): array
    {
        return [];
    }

    /**
     * Get multilingual attribute names
     */
    protected function getMultilingualAttributes(): array
    {
        return [];
    }

    /**
     * Get domain-specific attribute names
     */
    protected function getDomainAttributes(): array
    {
        return [];
    }

    /**
     * Get custom attribute names specific to this request
     */
    protected function getCustomAttributes(): array
    {
        return [];
    }

    /**
     * Perform custom validation logic
     */
    protected function performCustomValidation(Validator $validator): void
    {
        // Custom validation logic implementation
        // Override in specific request classes as needed
    }

    /**
     * Format validation errors for API responses
     */
    protected function formatValidationErrors(Validator $validator): array
    {
        $errors = [];
        
        foreach ($validator->errors()->messages() as $field => $messages) {
            $errors[$field] = [
                'field' => $field,
                'messages' => $messages,
                'first_error' => $messages[0] ?? null,
            ];
        }

        return $errors;
    }

    /**
     * Log validation failure for audit purposes
     */
    protected function logValidationFailure(Validator $validator): void
    {
        \Log::warning('Validation failed', [
            'request_class' => static::class,
            'request_id' => $this->getRequestId(),
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', 'password_confirmation']),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get unique request ID for tracking
     */
    protected function getRequestId(): string
    {
        return request()->header('X-Request-ID') ?: (string) str()->uuid();
    }

    /**
     * Get the security level for this request
     */
    public function getSecurityLevel(): string
    {
        return $this->securityLevel;
    }

    /**
     * Get validation modules for this request
     */
    public function getValidationModules(): array
    {
        return $this->validationModules;
    }

    /**
     * Check if performance tracking is enabled
     */
    public function isPerformanceTrackingEnabled(): bool
    {
        return $this->performanceTracking;
    }

    /**
     * Get request data with sanitization
     */
    public function getSanitizedData(): array
    {
        return $this->applySanitization($this->validated());
    }

    /**
     * Apply data sanitization
     */
    protected function applySanitization(array $data): array
    {
        // Basic sanitization - can be enhanced in domain classes
        return array_map(function ($value) {
            if (is_string($value)) {
                return trim(strip_tags($value));
            }
            return $value;
        }, $data);
    }
} 