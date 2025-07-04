<?php

namespace App\Http\Requests\Foundation\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Audit Logging Trait
 *
 * Provides comprehensive audit logging for validation events:
 * - Validation success/failure logging
 * - Security event logging
 * - Performance metrics logging
 * - Compliance audit trails
 * - Error tracking and monitoring
 *
 * @version 1.0.0
 *
 * @since 2024-12-28
 */
trait AuditLoggingTrait
{
    /**
     * Enable audit logging
     */
    protected bool $auditLoggingEnabled = true;

    /**
     * Audit log levels
     */
    protected array $auditLogLevels = [
        'info' => 'info',
        'warning' => 'warning',
        'error' => 'error',
        'security' => 'warning',
        'performance' => 'info',
        'compliance' => 'info',
    ];

    /**
     * Log validation success
     */
    protected function logValidationSuccess(): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        Log::info('Validation successful', $this->getBaseAuditData());
    }

    /**
     * Log validation failure with details
     */
    protected function logValidationFailureAudit($validator): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        $auditData = array_merge($this->getBaseAuditData(), [
            'validation_errors' => $validator->errors()->toArray(),
            'failed_rules' => $this->extractFailedRules($validator),
            'input_data_hash' => md5(serialize($this->except(['password', 'password_confirmation']))),
        ]);

        Log::error('Validation failed', $auditData);
    }

    /**
     * Log security validation events
     */
    protected function logSecurityEvent(string $event, array $details = []): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        $auditData = array_merge($this->getBaseAuditData(), [
            'security_event' => $event,
            'security_details' => $details,
            'security_level' => $this->getSecurityLevel(),
        ]);

        Log::warning('Security validation event', $auditData);
    }

    /**
     * Log performance metrics
     */
    protected function logPerformanceMetrics(array $metrics): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        $auditData = array_merge($this->getBaseAuditData(), [
            'performance_metrics' => $metrics,
            'performance_thresholds' => $this->getPerformanceThresholds(),
        ]);

        // Log as warning if performance thresholds exceeded
        $logLevel = $this->isPerformanceThresholdExceeded($metrics) ? 'warning' : 'info';

        Log::log($logLevel, 'Validation performance metrics', $auditData);
    }

    /**
     * Log compliance events
     */
    protected function logComplianceEvent(string $event, array $data = []): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        $auditData = array_merge($this->getBaseAuditData(), [
            'compliance_event' => $event,
            'compliance_data' => $data,
            'compliance_timestamp' => now()->toISOString(),
        ]);

        Log::info('Compliance audit event', $auditData);
    }

    /**
     * Log user action for audit trail
     */
    protected function logUserAction(string $action, array $context = []): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        $auditData = array_merge($this->getBaseAuditData(), [
            'user_action' => $action,
            'action_context' => $context,
            'action_timestamp' => now()->toISOString(),
        ]);

        Log::info('User action audit', $auditData);
    }

    /**
     * Get base audit data for all logs
     */
    protected function getBaseAuditData(): array
    {
        return [
            'request_id' => $this->getRequestId(),
            'request_class' => static::class,
            'request_method' => request()->method(),
            'request_url' => request()->url(),
            'request_route' => request()->route()?->getName(),
            'client_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
            'locale' => app()->getLocale(),
            'environment' => app()->environment(),
        ];
    }

    /**
     * Extract failed validation rules
     */
    protected function extractFailedRules($validator): array
    {
        $failedRules = [];

        foreach ($validator->failed() as $field => $rules) {
            $failedRules[$field] = array_keys($rules);
        }

        return $failedRules;
    }

    /**
     * Get performance thresholds
     */
    protected function getPerformanceThresholds(): array
    {
        return [
            'execution_time_ms' => 50,
            'memory_usage_mb' => 50,
            'database_queries' => 5,
        ];
    }

    /**
     * Check if performance threshold exceeded
     */
    protected function isPerformanceThresholdExceeded(array $metrics): bool
    {
        $thresholds = $this->getPerformanceThresholds();

        foreach ($metrics as $operation => $operationMetrics) {
            if (isset($operationMetrics['execution_time_ms']) &&
                $operationMetrics['execution_time_ms'] > $thresholds['execution_time_ms']) {
                return true;
            }

            if (isset($operationMetrics['memory_usage_mb']) &&
                $operationMetrics['memory_usage_mb'] > $thresholds['memory_usage_mb']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log data sanitization events
     */
    protected function logDataSanitization(array $originalData, array $sanitizedData): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        $changes = [];
        foreach ($originalData as $key => $value) {
            if (isset($sanitizedData[$key]) && $value !== $sanitizedData[$key]) {
                $changes[$key] = [
                    'original_length' => is_string($value) ? strlen($value) : 'non-string',
                    'sanitized_length' => is_string($sanitizedData[$key]) ? strlen($sanitizedData[$key]) : 'non-string',
                    'changed' => true,
                ];
            }
        }

        if (! empty($changes)) {
            $auditData = array_merge($this->getBaseAuditData(), [
                'sanitization_changes' => $changes,
                'total_fields_changed' => count($changes),
            ]);

            Log::info('Data sanitization applied', $auditData);
        }
    }

    /**
     * Log authorization events
     */
    protected function logAuthorizationEvent(string $result, array $context = []): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        $auditData = array_merge($this->getBaseAuditData(), [
            'authorization_result' => $result,
            'authorization_context' => $context,
        ]);

        $logLevel = $result === 'denied' ? 'warning' : 'info';
        Log::log($logLevel, 'Authorization validation', $auditData);
    }

    /**
     * Log rate limiting events
     */
    protected function logRateLimitEvent(string $event, array $details = []): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        $auditData = array_merge($this->getBaseAuditData(), [
            'rate_limit_event' => $event,
            'rate_limit_details' => $details,
        ]);

        $logLevel = $event === 'exceeded' ? 'warning' : 'info';
        Log::log($logLevel, 'Rate limit validation', $auditData);
    }

    /**
     * Create audit summary for completed validation
     */
    protected function createAuditSummary(bool $success, array $metrics = []): void
    {
        if (! $this->auditLoggingEnabled) {
            return;
        }

        $auditData = array_merge($this->getBaseAuditData(), [
            'validation_summary' => [
                'success' => $success,
                'total_fields' => count($this->all()),
                'security_level' => $this->getSecurityLevel(),
                'validation_modules' => $this->getValidationModules(),
            ],
            'performance_summary' => $metrics,
        ]);

        Log::info('Validation audit summary', $auditData);
    }

    /**
     * Enable audit logging
     */
    public function enableAuditLogging(): void
    {
        $this->auditLoggingEnabled = true;
    }

    /**
     * Disable audit logging
     */
    public function disableAuditLogging(): void
    {
        $this->auditLoggingEnabled = false;
    }

    /**
     * Check if audit logging is enabled
     */
    public function isAuditLoggingEnabled(): bool
    {
        return $this->auditLoggingEnabled;
    }
}
