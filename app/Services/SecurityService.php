<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class SecurityService
{
    /**
     * Check if an account is locked due to failed login attempts.
     */
    public function isAccountLocked(string $identifier): bool
    {
        $lockoutKey = $this->getLockoutKey($identifier);
        $attempts = Cache::get($lockoutKey, 0);
        
        return $attempts >= config('security.authentication.max_failed_attempts', 5);
    }

    /**
     * Get remaining lockout time in seconds.
     */
    public function getLockoutRemainingTime(string $identifier): int
    {
        $lockoutTimeKey = $this->getLockoutKey($identifier) . ':time';
        return Cache::get($lockoutTimeKey, 0);
    }

    /**
     * Record a failed login attempt.
     */
    public function recordFailedAttempt(string $identifier, Request $request): void
    {
        $lockoutKey = $this->getLockoutKey($identifier);
        $attempts = Cache::get($lockoutKey, 0) + 1;
        
        $duration = config('security.authentication.lockout_duration', 30);
        Cache::put($lockoutKey, $attempts, now()->addMinutes($duration));
        
        if ($attempts >= config('security.authentication.max_failed_attempts', 5)) {
            $lockoutTimeKey = $lockoutKey . ':time';
            Cache::put($lockoutTimeKey, $duration * 60, now()->addMinutes($duration));
            
            $this->logSecurityEvent('account_locked', $request, [
                'identifier' => $identifier,
                'attempts' => $attempts,
                'lockout_duration' => $duration
            ]);
        }

        $this->logSecurityEvent('authentication_failed', $request, [
            'identifier' => $identifier,
            'attempts' => $attempts
        ]);
    }

    /**
     * Clear failed login attempts for an identifier.
     */
    public function clearFailedAttempts(string $identifier): void
    {
        $lockoutKey = $this->getLockoutKey($identifier);
        Cache::forget($lockoutKey);
        Cache::forget($lockoutKey . ':time');
    }

    /**
     * Validate password against security policy.
     */
    public function validatePassword(string $password): array
    {
        $errors = [];
        $config = config('security.authentication');

        // Check minimum length
        if (strlen($password) < $config['password_min_length']) {
            $errors[] = "Password must be at least {$config['password_min_length']} characters long.";
        }

        // Check for uppercase letter
        if ($config['password_require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }

        // Check for lowercase letter
        if ($config['password_require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }

        // Check for numbers
        if ($config['password_require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }

        // Check for symbols
        if ($config['password_require_symbols'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }

        return $errors;
    }

    /**
     * Get Laravel Password rule based on security configuration.
     */
    public function getPasswordRule(): Password
    {
        $config = config('security.authentication');
        $rule = Password::min($config['password_min_length']);

        if ($config['password_require_uppercase']) {
            $rule->mixedCase();
        }

        if ($config['password_require_numbers']) {
            $rule->numbers();
        }

        if ($config['password_require_symbols']) {
            $rule->symbols();
        }

        // Check against common passwords
        $rule->uncompromised();

        return $rule;
    }

    /**
     * Check if password was recently used by user.
     */
    public function isPasswordRecentlyUsed($user, string $password): bool
    {
        $preventReuse = config('security.account.prevent_password_reuse', 5);
        
        if ($preventReuse <= 0) {
            return false;
        }

        // This would check against a password history table
        // For now, just check against current password
        return Hash::check($password, $user->password);
    }

    /**
     * Generate a secure session token.
     */
    public function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validate session security.
     */
    public function validateSession(Request $request, $user): bool
    {
        if (!config('security.session.validate_sessions', true)) {
            return true;
        }

        $sessionKey = 'session_security:' . $user->id;
        $storedData = Cache::get($sessionKey);

        $currentData = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_activity' => now()->timestamp
        ];

        if ($storedData && $this->detectSuspiciousActivity($storedData, $currentData)) {
            $this->logSecurityEvent('suspicious_session_activity', $request, [
                'user_id' => $user->id,
                'stored_data' => $storedData,
                'current_data' => $currentData
            ]);
            return false;
        }

        // Update session data
        Cache::put($sessionKey, $currentData, now()->addHours(2));
        return true;
    }

    /**
     * Detect suspicious session activity.
     */
    protected function detectSuspiciousActivity(array $stored, array $current): bool
    {
        // Check for IP changes
        if (!config('security.session.allow_ip_changes', false) && $stored['ip'] !== $current['ip']) {
            return true;
        }

        // Check for user agent changes
        if (!config('security.session.allow_user_agent_changes', false) && $stored['user_agent'] !== $current['user_agent']) {
            return true;
        }

        // Check for unusual time gaps
        $timeDiff = $current['last_activity'] - $stored['last_activity'];
        if ($timeDiff < 0 || $timeDiff > config('security.session.session_timeout', 120) * 60) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has required permissions.
     */
    public function hasPermission($user, string $permission): bool
    {
        // Use caching for permission checks
        $cacheKey = "user_permissions:{$user->id}:{$permission}";
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($user, $permission) {
            // Check using Laravel's built-in authorization
            if (method_exists($user, 'can')) {
                return $user->can($permission);
            }
            
            // Check using Spatie permissions if available
            if (method_exists($user, 'hasPermissionTo')) {
                return $user->hasPermissionTo($permission);
            }
            
            // Check role-based permissions
            switch ($permission) {
                case 'admin':
                    return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false;
                case 'employer':
                    return method_exists($user, 'hasRole') ? $user->hasRole('employer') : false;
                case 'candidate':
                    return method_exists($user, 'hasRole') ? $user->hasRole('candidate') : false;
                default:
                    return true; // Default allow for undefined permissions
            }
        });
    }

    /**
     * Log security events.
     */
    public function logSecurityEvent(string $event, Request $request, array $context = []): void
    {
        if (!config('security.logging.log_security_events', true)) {
            return;
        }

        $data = array_merge([
            'event' => $event,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => $request->user()?->id,
            'timestamp' => now()->toISOString(),
            'session_id' => $request->session()->getId(),
        ], $context);

        Log::channel('security')->info("Security Event: {$event}", $data);

        // Send alerts for critical events
        if ($this->isCriticalEvent($event)) {
            $this->sendSecurityAlert($event, $data);
        }
    }

    /**
     * Check if an event is critical and requires immediate attention.
     */
    protected function isCriticalEvent(string $event): bool
    {
        $criticalEvents = [
            'account_locked',
            'suspicious_session_activity',
            'unauthorized_admin_access',
            'multiple_failed_logins',
            'sql_injection_attempt',
            'xss_attempt'
        ];

        return in_array($event, $criticalEvents);
    }

    /**
     * Send security alert for critical events.
     */
    protected function sendSecurityAlert(string $event, array $data): void
    {
        if (!config('security.logging.alert_suspicious_activity', true)) {
            return;
        }

        // Log to a separate alert channel
        Log::channel('emergency')->emergency("SECURITY ALERT: {$event}", $data);

        // Could also send email, Slack notification, etc.
        // Mail::to(config('security.alert_email'))->queue(new SecurityAlert($event, $data));
    }

    /**
     * Get lockout cache key for an identifier.
     */
    protected function getLockoutKey(string $identifier): string
    {
        return 'lockout:' . hash('sha256', $identifier);
    }

    /**
     * Rate limit a specific action.
     */
    public function rateLimit(string $key, int $maxAttempts, int $decayMinutes): bool
    {
        return RateLimiter::attempt($key, $maxAttempts, function () {
            // Action is allowed
        }, $decayMinutes * 60);
    }

    /**
     * Check if action is rate limited.
     */
    public function isRateLimited(string $key, int $maxAttempts): bool
    {
        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }

    /**
     * Get remaining time until rate limit resets.
     */
    public function getRateLimitRemainingTime(string $key): int
    {
        return RateLimiter::availableIn($key);
    }

    /**
     * Sanitize input to prevent XSS attacks.
     */
    public function sanitizeInput($input): string
    {
        if (is_string($input)) {
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
        
        return $input;
    }

    /**
     * Validate file upload security.
     */
    public function validateFileUpload($file): array
    {
        $errors = [];
        $config = config('security.file_upload');

        // Check file size
        if ($file->getSize() > $config['max_file_size']) {
            $errors[] = 'File size exceeds maximum allowed size.';
        }

        // Check file type
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedTypes = array_merge(
            $config['allowed_resume_types'] ?? [],
            $config['allowed_image_types'] ?? []
        );

        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'File type not allowed.';
        }

        // Additional security checks could go here
        // - MIME type validation
        // - File content scanning
        // - Virus scanning if enabled

        return $errors;
    }
} 