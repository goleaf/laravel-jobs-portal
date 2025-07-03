<?php

namespace App\Http\Requests\Foundation\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Performance Optimization Trait
 * 
 * Provides performance optimization features for validation:
 * - Validation rule caching
 * - Lazy loading of complex rules
 * - Query optimization
 * - Performance monitoring
 * - Memory usage optimization
 * 
 * @package App\Http\Requests\Foundation\Traits
 * @version 1.0.0
 * @since 2024-12-28
 */
trait PerformanceOptimizationTrait
{
    /**
     * Performance tracking data
     */
    protected array $performanceMetrics = [];

    /**
     * Cache duration for validation rules (in seconds)
     */
    protected int $ruleCacheDuration = 3600; // 1 hour

    /**
     * Enable performance monitoring
     */
    protected bool $enablePerformanceMonitoring = true;

    /**
     * Memory usage threshold for warnings (in MB)
     */
    protected int $memoryThresholdMB = 50;

    /**
     * Execution time threshold for warnings (in ms)
     */
    protected int $executionThresholdMs = 50;

    /**
     * Get cached validation rules
     */
    protected function getCachedValidationRules(): array
    {
        if (!$this->enablePerformanceMonitoring) {
            return $this->generateValidationRules();
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $cacheKey = $this->getValidationRulesCacheKey();
        
        $rules = Cache::remember($cacheKey, $this->ruleCacheDuration, function () {
            return $this->generateValidationRules();
        });

        $this->trackPerformanceMetrics('rule_generation', $startTime, $startMemory);

        return $rules;
    }

    /**
     * Generate validation rules with performance optimization
     */
    protected function generateValidationRules(): array
    {
        $rules = [];

        // Use lazy loading for complex rules
        $rules = array_merge($rules, $this->getLazyLoadedRules());

        // Optimize database queries
        $rules = array_merge($rules, $this->getOptimizedDatabaseRules());

        // Apply rule caching
        $rules = $this->applyCachedRuleProcessing($rules);

        return $rules;
    }

    /**
     * Get lazy-loaded validation rules
     */
    protected function getLazyLoadedRules(): array
    {
        $rules = [];

        // Only load expensive rules when needed
        if ($this->needsComplexValidation()) {
            $rules = array_merge($rules, $this->getComplexValidationRules());
        }

        if ($this->needsDatabaseValidation()) {
            $rules = array_merge($rules, $this->getDatabaseValidationRules());
        }

        return $rules;
    }

    /**
     * Get optimized database validation rules
     */
    protected function getOptimizedDatabaseRules(): array
    {
        $rules = [];

        // Batch database queries for exists/unique validation
        $existsRules = $this->getBatchedExistsRules();
        $uniqueRules = $this->getBatchedUniqueRules();

        return array_merge($rules, $existsRules, $uniqueRules);
    }

    /**
     * Get batched exists validation rules
     */
    protected function getBatchedExistsRules(): array
    {
        $rules = [];
        $existsFields = $this->getExistsValidationFields();

        if (empty($existsFields)) {
            return $rules;
        }

        // Pre-load all required records in batch
        $this->preloadExistsValidationData($existsFields);

        foreach ($existsFields as $field => $table) {
            $rules[$field] = ['required', 'exists:' . $table . ',id'];
        }

        return $rules;
    }

    /**
     * Get batched unique validation rules
     */
    protected function getBatchedUniqueRules(): array
    {
        $rules = [];
        $uniqueFields = $this->getUniqueValidationFields();

        if (empty($uniqueFields)) {
            return $rules;
        }

        foreach ($uniqueFields as $field => $table) {
            $rules[$field] = ['required', 'unique:' . $table . ',' . $field];
        }

        return $rules;
    }

    /**
     * Apply cached rule processing
     */
    protected function applyCachedRuleProcessing(array $rules): array
    {
        $processedRules = [];

        foreach ($rules as $field => $fieldRules) {
            $processedRules[$field] = $this->optimizeFieldRules($fieldRules);
        }

        return $processedRules;
    }

    /**
     * Optimize individual field rules
     */
    protected function optimizeFieldRules(array $rules): array
    {
        // Remove duplicate rules
        $rules = array_unique($rules);

        // Reorder rules for optimal performance (cheaper rules first)
        usort($rules, function ($a, $b) {
            return $this->getRulePriority($a) <=> $this->getRulePriority($b);
        });

        return $rules;
    }

    /**
     * Get rule priority for optimization (lower number = higher priority)
     */
    protected function getRulePriority($rule): int
    {
        $rule = is_string($rule) ? $rule : 'custom';

        $priorities = [
            'required' => 1,
            'string' => 2,
            'integer' => 2,
            'numeric' => 2,
            'boolean' => 2,
            'min' => 3,
            'max' => 3,
            'email' => 4,
            'url' => 4,
            'regex' => 5,
            'exists' => 8,
            'unique' => 9,
            'custom' => 10,
        ];

        return $priorities[$rule] ?? 10;
    }

    /**
     * Track performance metrics
     */
    protected function trackPerformanceMetrics(string $operation, float $startTime, int $startMemory): void
    {
        if (!$this->enablePerformanceMonitoring) {
            return;
        }

        $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = (memory_get_usage() - $startMemory) / 1024 / 1024; // Convert to MB

        $this->performanceMetrics[$operation] = [
            'execution_time_ms' => round($executionTime, 2),
            'memory_usage_mb' => round($memoryUsage, 2),
            'timestamp' => now()->toISOString(),
        ];

        // Log warnings for performance issues
        if ($executionTime > $this->executionThresholdMs) {
            Log::warning('Slow validation operation detected', [
                'operation' => $operation,
                'execution_time_ms' => $executionTime,
                'request_class' => static::class,
            ]);
        }

        if ($memoryUsage > $this->memoryThresholdMB) {
            Log::warning('High memory usage in validation', [
                'operation' => $operation,
                'memory_usage_mb' => $memoryUsage,
                'request_class' => static::class,
            ]);
        }
    }

    /**
     * Get validation rules cache key
     */
    protected function getValidationRulesCacheKey(): string
    {
        $requestClass = static::class;
        $inputHash = md5(serialize($this->all()));
        
        return "validation_rules:{$requestClass}:{$inputHash}";
    }

    /**
     * Check if complex validation is needed
     */
    protected function needsComplexValidation(): bool
    {
        // Override in specific request classes
        return false;
    }

    /**
     * Check if database validation is needed
     */
    protected function needsDatabaseValidation(): bool
    {
        $databaseFields = ['exists', 'unique'];
        $rules = $this->rules();

        foreach ($rules as $fieldRules) {
            foreach ($fieldRules as $rule) {
                if (is_string($rule)) {
                    foreach ($databaseFields as $dbField) {
                        if (str_starts_with($rule, $dbField . ':')) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get complex validation rules
     */
    protected function getComplexValidationRules(): array
    {
        // Override in specific request classes
        return [];
    }

    /**
     * Get database validation rules
     */
    protected function getDatabaseValidationRules(): array
    {
        // Override in specific request classes
        return [];
    }

    /**
     * Get fields requiring exists validation
     */
    protected function getExistsValidationFields(): array
    {
        // Override in specific request classes
        return [];
    }

    /**
     * Get fields requiring unique validation
     */
    protected function getUniqueValidationFields(): array
    {
        // Override in specific request classes
        return [];
    }

    /**
     * Preload data for exists validation
     */
    protected function preloadExistsValidationData(array $fields): void
    {
        // Override in specific request classes to implement batch loading
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        return $this->performanceMetrics;
    }

    /**
     * Get total execution time
     */
    public function getTotalExecutionTime(): float
    {
        $total = 0;
        foreach ($this->performanceMetrics as $metrics) {
            $total += $metrics['execution_time_ms'] ?? 0;
        }
        return $total;
    }

    /**
     * Get total memory usage
     */
    public function getTotalMemoryUsage(): float
    {
        $total = 0;
        foreach ($this->performanceMetrics as $metrics) {
            $total += $metrics['memory_usage_mb'] ?? 0;
        }
        return $total;
    }

    /**
     * Clear performance metrics
     */
    public function clearPerformanceMetrics(): void
    {
        $this->performanceMetrics = [];
    }

    /**
     * Enable performance monitoring
     */
    public function enablePerformanceMonitoring(): void
    {
        $this->enablePerformanceMonitoring = true;
    }

    /**
     * Disable performance monitoring
     */
    public function disablePerformanceMonitoring(): void
    {
        $this->enablePerformanceMonitoring = false;
    }
} 