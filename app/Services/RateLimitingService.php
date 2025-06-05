<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class RateLimitingService
{
    protected const DEFAULT_LIMIT = 60;
    protected const DEFAULT_WINDOW = 60; // seconds

    /**
     * Rate limiting configurations for different endpoints.
     */
    protected array $rateLimits = [
        'auth.login' => ['limit' => 5, 'window' => 900], // 5 attempts per 15 minutes
        'auth.register' => ['limit' => 3, 'window' => 3600], // 3 attempts per hour
        'auth.password.reset' => ['limit' => 3, 'window' => 3600], // 3 attempts per hour
        'auth.password.email' => ['limit' => 5, 'window' => 3600], // 5 emails per hour
        'api.general' => ['limit' => 100, 'window' => 60], // 100 requests per minute
        'api.search' => ['limit' => 50, 'window' => 60], // 50 searches per minute
        'api.upload' => ['limit' => 10, 'window' => 3600], // 10 uploads per hour
        'contact.form' => ['limit' => 3, 'window' => 3600], // 3 contact submissions per hour
        'job.application' => ['limit' => 20, 'window' => 3600], // 20 applications per hour
        'company.registration' => ['limit' => 2, 'window' => 86400], // 2 company registrations per day
        'email.verification' => ['limit' => 5, 'window' => 3600], // 5 verification emails per hour
        'admin.actions' => ['limit' => 1000, 'window' => 60], // 1000 admin actions per minute
    ];

    /**
     * Check if request is allowed under rate limit.
     */
    public function isAllowed(string $key, ?int $limit = null): bool
    {
        $config = $this->getRateLimitConfig($key);
        $actualLimit = $limit ?? $config['limit'];
        
        return Cache::get($this->buildCacheKey($key), 0) < $actualLimit;
    }

    /**
     * Record a hit for the rate limit key.
     */
    public function hit(string $key, ?int $window = null): int
    {
        $config = $this->getRateLimitConfig($key);
        $actualWindow = $window ?? $config['window'];
        $cacheKey = $this->buildCacheKey($key);
        
        $current = Cache::get($cacheKey, 0);
        $new = $current + 1;
        
        Cache::put($cacheKey, $new, $actualWindow);
        
        // Log rate limit hits for monitoring
        if ($new > ($config['limit'] * 0.8)) { // Log when approaching limit
            Log::channel('security')->warning('Rate limit approaching', [
                'key' => $key,
                'attempts' => $new,
                'limit' => $config['limit'],
                'percentage' => ($new / $config['limit']) * 100
            ]);
        }
        
        return $new;
    }

    /**
     * Get current attempts for key.
     */
    public function getAttempts(string $key): int
    {
        return Cache::get($this->buildCacheKey($key), 0);
    }

    /**
     * Clear rate limit for key.
     */
    public function clear(string $key): void
    {
        Cache::forget($this->buildCacheKey($key));
        
        Log::channel('security')->info('Rate limit cleared', [
            'key' => $key,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Advanced rate limiting with sliding window.
     */
    public function slidingWindowRateLimit(string $key, int $limit, int $windowSeconds): bool
    {
        $now = time();
        $windowStart = $now - $windowSeconds;
        $cacheKey = $this->buildCacheKey($key) . ':sliding';
        
        // Get existing timestamps
        $timestamps = Cache::get($cacheKey, []);
        
        // Remove old timestamps outside the window
        $timestamps = array_filter($timestamps, fn($timestamp) => $timestamp > $windowStart);
        
        // Check if limit exceeded
        if (count($timestamps) >= $limit) {
            return false;
        }
        
        // Add current timestamp
        $timestamps[] = $now;
        
        // Store updated timestamps
        Cache::put($cacheKey, $timestamps, $windowSeconds);
        
        return true;
    }

    /**
     * Adaptive rate limiting based on user behavior.
     */
    public function adaptiveRateLimit(Request $request, string $action): bool
    {
        $user = $request->user();
        $baseKey = $this->buildKeyFromRequest($request, $action);
        
        // Get user's rate limit based on reputation/role
        $multiplier = $this->getRateLimitMultiplier($user, $request);
        $config = $this->getRateLimitConfig($action);
        
        $adaptedLimit = (int) ($config['limit'] * $multiplier);
        
        return $this->isAllowed($baseKey, $adaptedLimit);
    }

    /**
     * Get rate limit multiplier based on user characteristics.
     */
    protected function getRateLimitMultiplier($user, Request $request): float
    {
        $multiplier = 1.0;
        
        if (!$user) {
            return 0.5; // Anonymous users get lower limits
        }
        
        // Verified users get higher limits
        if (method_exists($user, 'hasVerifiedEmail') && $user->hasVerifiedEmail()) {
            $multiplier *= 1.5;
        }
        
        // Admin users get much higher limits
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            $multiplier *= 5.0;
        }
        
        // Premium/paid users get higher limits
        if (isset($user->subscription_status) && $user->subscription_status === 'active') {
            $multiplier *= 2.0;
        }
        
        // Trusted IPs get higher limits
        if ($this->isTrustedIP($request->ip())) {
            $multiplier *= 2.0;
        }
        
        return $multiplier;
    }

    /**
     * Check if IP is in trusted list.
     */
    protected function isTrustedIP(string $ip): bool
    {
        $trustedIPs = config('security.rate_limiting.trusted_ips', []);
        return in_array($ip, $trustedIPs);
    }

    /**
     * Build cache key from request.
     */
    public function buildKeyFromRequest(Request $request, string $type = "general"): string
    {
        $user = $request->user();
        
        // Use multiple identifiers for better accuracy
        $identifiers = [];
        
        if ($user) {
            $identifiers[] = "user:" . $user->id;
        } else {
            $identifiers[] = "ip:" . $request->ip();
            
            // Add user agent hash for anonymous users
            $identifiers[] = "ua:" . md5($request->userAgent() ?? '');
        }
        
        return "rate_limit:{$type}:" . implode(':', $identifiers);
    }

    /**
     * Build internal cache key.
     */
    protected function buildCacheKey(string $key): string
    {
        return "rate_limit:v2:{$key}";
    }

    /**
     * Get rate limit configuration for key.
     */
    protected function getRateLimitConfig(string $key): array
    {
        return $this->rateLimits[$key] ?? [
            'limit' => self::DEFAULT_LIMIT,
            'window' => self::DEFAULT_WINDOW
        ];
    }

    /**
     * Get remaining time until rate limit resets.
     */
    public function getRemainingTime(string $key): int
    {
        $cacheKey = $this->buildCacheKey($key);
        
        if (!Cache::has($cacheKey)) {
            return 0;
        }
        
        // Get TTL from Redis
        try {
            $ttl = Cache::getStore()->getRedis()->ttl(
                Cache::getStore()->getPrefix() . $cacheKey
            );
            return max(0, $ttl);
        } catch (\Exception $e) {
            Log::warning('Failed to get rate limit TTL', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get detailed statistics for rate limiting.
     */
    public function getDetailedStats(): array
    {
        $stats = [
            'status' => 'active',
            'cache_driver' => config('cache.default'),
            'configured_limits' => count($this->rateLimits),
            'limits' => []
        ];
        
        foreach ($this->rateLimits as $key => $config) {
            $cacheKey = $this->buildCacheKey($key);
            $currentAttempts = Cache::get($cacheKey, 0);
            $remainingTime = $this->getRemainingTime($key);
            
            $stats['limits'][$key] = [
                'limit' => $config['limit'],
                'window' => $config['window'],
                'current_attempts' => $currentAttempts,
                'remaining' => max(0, $config['limit'] - $currentAttempts),
                'percentage_used' => $config['limit'] > 0 ? ($currentAttempts / $config['limit']) * 100 : 0,
                'resets_in' => $remainingTime,
                'is_exceeded' => $currentAttempts >= $config['limit']
            ];
        }
        
        return $stats;
    }

    /**
     * Get simple statistics.
     */
    public function getStats(): array
    {
        return [
            "status" => "active",
            "cache_driver" => config("cache.default"),
            "message" => "Advanced rate limiting service operational",
            "configured_limits" => count($this->rateLimits)
        ];
    }

    /**
     * Bulk clear rate limits by pattern.
     */
    public function clearByPattern(string $pattern): int
    {
        $cleared = 0;
        $cacheStore = Cache::getStore();
        
        if (method_exists($cacheStore, 'getRedis')) {
            $redis = $cacheStore->getRedis();
            $prefix = $cacheStore->getPrefix();
            $keys = $redis->keys($prefix . 'rate_limit:*' . $pattern . '*');
            
            foreach ($keys as $key) {
                $redis->del(str_replace($prefix, '', $key));
                $cleared++;
            }
        }
        
        Log::channel('security')->info('Bulk rate limit clear', [
            'pattern' => $pattern,
            'cleared_count' => $cleared
        ]);
        
        return $cleared;
    }

    /**
     * Set custom rate limit for specific key.
     */
    public function setCustomLimit(string $key, int $limit, int $window): void
    {
        $this->rateLimits[$key] = [
            'limit' => $limit,
            'window' => $window
        ];
        
        Log::channel('security')->info('Custom rate limit set', [
            'key' => $key,
            'limit' => $limit,
            'window' => $window
        ]);
    }

    /**
     * Check if rate limit is exceeded.
     */
    public function isExceeded(string $key): bool
    {
        $config = $this->getRateLimitConfig($key);
        $attempts = $this->getAttempts($key);
        
        return $attempts >= $config['limit'];
    }

    /**
     * Get headers for rate limit response.
     */
    public function getHeaders(string $key): array
    {
        $config = $this->getRateLimitConfig($key);
        $attempts = $this->getAttempts($key);
        $remaining = max(0, $config['limit'] - $attempts);
        $resetTime = now()->addSeconds($this->getRemainingTime($key))->timestamp;
        
        return [
            'X-RateLimit-Limit' => $config['limit'],
            'X-RateLimit-Remaining' => $remaining,
            'X-RateLimit-Reset' => $resetTime,
            'X-RateLimit-Retry-After' => $this->getRemainingTime($key),
        ];
    }

    /**
     * Test rate limiting configuration.
     */
    public function testConfiguration(): array
    {
        $results = [
            'status' => 'ok',
            'tests' => []
        ];
        
        // Test cache connectivity
        try {
            Cache::put('rate_limit_test', 'test', 60);
            $value = Cache::get('rate_limit_test');
            Cache::forget('rate_limit_test');
            
            $results['tests']['cache_connectivity'] = $value === 'test' ? 'ok' : 'failed';
        } catch (\Exception $e) {
            $results['tests']['cache_connectivity'] = 'failed: ' . $e->getMessage();
            $results['status'] = 'error';
        }
        
        // Test rate limit logic
        try {
            $testKey = 'test_' . time();
            $allowed1 = $this->isAllowed($testKey, 2);
            $this->hit($testKey, 60);
            $this->hit($testKey, 60);
            $allowed2 = $this->isAllowed($testKey, 2);
            $this->clear($testKey);
            
            $results['tests']['rate_limit_logic'] = ($allowed1 && !$allowed2) ? 'ok' : 'failed';
        } catch (\Exception $e) {
            $results['tests']['rate_limit_logic'] = 'failed: ' . $e->getMessage();
            $results['status'] = 'error';
        }
        
        return $results;
    }
}