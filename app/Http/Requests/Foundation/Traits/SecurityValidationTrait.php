<?php

namespace App\Http\Requests\Foundation\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Security Validation Trait
 * 
 * Provides security validation patterns across all request types:
 * - Input sanitization and injection prevention
 * - Rate limiting validation  
 * - Authorization level checking
 * - CSRF protection validation
 * - Security headers validation
 * 
 * @package App\Http\Requests\Foundation\Traits
 * @version 1.0.0
 * @since 2024-12-28
 */
trait SecurityValidationTrait
{
    /**
     * Security validation patterns by level
     */
    protected array $securityLevels = [
        'low' => ['basic_sanitization'],
        'medium' => ['basic_sanitization', 'rate_limiting'],
        'high' => ['basic_sanitization', 'rate_limiting', 'advanced_validation', 'csrf_protection'],
        'critical' => ['basic_sanitization', 'rate_limiting', 'advanced_validation', 'csrf_protection', 'enhanced_monitoring']
    ];

    /**
     * Get security validation rules based on security level
     */
    protected function getSecurityValidationRules(): array
    {
        $rules = [];
        $securityLevel = $this->getSecurityLevel();
        $patterns = $this->securityLevels[$securityLevel] ?? $this->securityLevels['medium'];

        foreach ($patterns as $pattern) {
            $rules = array_merge($rules, $this->getSecurityPatternRules($pattern));
        }

        return $rules;
    }

    /**
     * Get rules for specific security pattern
     */
    protected function getSecurityPatternRules(string $pattern): array
    {
        switch ($pattern) {
            case 'basic_sanitization':
                return $this->getBasicSanitizationRules();
            case 'rate_limiting':
                return $this->getRateLimitingRules();
            case 'advanced_validation':
                return $this->getAdvancedValidationRules();
            case 'csrf_protection':
                return $this->getCsrfProtectionRules();
            case 'enhanced_monitoring':
                return $this->getEnhancedMonitoringRules();
            default:
                return [];
        }
    }

    /**
     * Basic sanitization rules
     */
    protected function getBasicSanitizationRules(): array
    {
        return [
            '*' => ['string', function ($attribute, $value, $fail) {
                if ($this->containsMaliciousContent($value)) {
                    $fail(__('validation.security.malicious_content_detected'));
                }
            }],
        ];
    }

    /**
     * Rate limiting validation rules
     */
    protected function getRateLimitingRules(): array
    {
        $key = $this->getRateLimitKey();
        $maxAttempts = $this->getRateLimitMaxAttempts();
        $decayMinutes = $this->getRateLimitDecayMinutes();

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            abort(429, __('validation.security.rate_limit_exceeded'));
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return [];
    }

    /**
     * Advanced validation rules
     */
    protected function getAdvancedValidationRules(): array
    {
        return [
            '*' => [function ($attribute, $value, $fail) {
                // SQL injection patterns
                if ($this->containsSqlInjection($value)) {
                    $fail(__('validation.security.sql_injection_detected'));
                }
                
                // XSS patterns
                if ($this->containsXssPatterns($value)) {
                    $fail(__('validation.security.xss_detected'));
                }
                
                // Command injection patterns
                if ($this->containsCommandInjection($value)) {
                    $fail(__('validation.security.command_injection_detected'));
                }
            }],
        ];
    }

    /**
     * CSRF protection rules
     */
    protected function getCsrfProtectionRules(): array
    {
        // CSRF validation is handled by middleware, but we can add additional checks
        return [];
    }

    /**
     * Enhanced monitoring rules
     */
    protected function getEnhancedMonitoringRules(): array
    {
        // Log security events for critical operations
        \Log::info('Critical security validation', [
            'request_class' => static::class,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);

        return [];
    }

    /**
     * Check for malicious content patterns
     */
    protected function containsMaliciousContent($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $maliciousPatterns = [
            '/javascript:/i',
            '/data:text\/html/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/onclick=/i',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for SQL injection patterns
     */
    protected function containsSqlInjection($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $sqlPatterns = [
            '/(\s|^)(union|select|insert|update|delete|drop|create|alter|exec|execute)\s/i',
            '/(\s|^)(or|and)\s+\d+\s*=\s*\d+/i',
            '/\'\s*(or|and)\s*\'/i',
            '/--\s*$/m',
            '/\/\*.*\*\//s',
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for XSS patterns
     */
    protected function containsXssPatterns($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^>]*>/i',
            '/<object\b[^>]*>/i',
            '/<embed\b[^>]*>/i',
            '/on\w+\s*=\s*["\'][^"\']*["\']?/i',
        ];

        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for command injection patterns
     */
    protected function containsCommandInjection($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $commandPatterns = [
            '/[;&|`$(){}\\\\]/i',
            '/\.\.\//i',
            '/(^|\s)(cat|ls|pwd|cd|rm|cp|mv|chmod|wget|curl)\s/i',
        ];

        foreach ($commandPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get rate limiting key
     */
    protected function getRateLimitKey(): string
    {
        return 'request_validation:' . request()->ip() . ':' . static::class;
    }

    /**
     * Get rate limit max attempts
     */
    protected function getRateLimitMaxAttempts(): int
    {
        $securityLevel = $this->getSecurityLevel();
        
        return match($securityLevel) {
            'low' => 200,
            'medium' => 100,
            'high' => 50,
            'critical' => 25,
            default => 100,
        };
    }

    /**
     * Get rate limit decay minutes
     */
    protected function getRateLimitDecayMinutes(): int
    {
        $securityLevel = $this->getSecurityLevel();
        
        return match($securityLevel) {
            'low' => 1,
            'medium' => 5,
            'high' => 15,
            'critical' => 60,
            default => 5,
        };
    }

    /**
     * Apply security sanitization to data
     */
    protected function applySecuritySanitization(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                $data[$key] = $this->applySecuritySanitization($value);
            }
        }

        return $data;
    }

    /**
     * Sanitize string value
     */
    protected function sanitizeString(string $value): string
    {
        // Remove potential XSS
        $value = strip_tags($value);
        
        // Encode special characters
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        // Remove potential SQL injection characters
        $value = str_replace(['--', '/*', '*/', ';'], '', $value);
        
        return trim($value);
    }
} 