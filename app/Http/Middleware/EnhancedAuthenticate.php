<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class EnhancedAuthenticate extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Check for account lockout
        if ($this->isAccountLocked($request)) {
            return $this->handleAccountLocked($request);
        }

        // Check session security
        if ($this->shouldValidateSession($request)) {
            $this->validateSessionSecurity($request);
        }

        // Continue with standard authentication
        $this->authenticate($request, $guards);

        // Log successful authentication
        if ($request->user()) {
            $this->logSecurityEvent('authentication_success', $request);
        }

        return $next($request);
    }

    /**
     * Check if account is locked due to failed attempts.
     */
    protected function isAccountLocked(Request $request): bool
    {
        $lockoutKey = 'lockout:' . $request->ip();
        $attempts = Cache::get($lockoutKey, 0);
        
        return $attempts >= config('security.max_failed_attempts', 5);
    }

    /**
     * Handle account lockout response.
     */
    protected function handleAccountLocked(Request $request)
    {
        $lockoutKey = 'lockout:' . $request->ip();
        $remainingTime = Cache::get($lockoutKey . ':time', 0);
        
        $this->logSecurityEvent('account_locked', $request, [
            'attempts' => Cache::get($lockoutKey, 0),
            'remaining_time' => $remainingTime
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Account temporarily locked',
                'message' => 'Too many failed login attempts. Please try again later.',
                'retry_after' => $remainingTime
            ], 429);
        }

        return redirect()->route('login')->withErrors([
            'email' => 'Account temporarily locked due to too many failed attempts. Please try again later.'
        ]);
    }

    /**
     * Check if session validation is needed.
     */
    protected function shouldValidateSession(Request $request): bool
    {
        return $request->user() && 
               config('security.validate_sessions', true) &&
               !$request->is('api/*');
    }

    /**
     * Validate session security.
     */
    protected function validateSessionSecurity(Request $request): void
    {
        $user = $request->user();
        $sessionKey = 'session_security:' . $user->id;
        $storedData = Cache::get($sessionKey);

        $currentData = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_activity' => now()->timestamp
        ];

        if ($storedData) {
            // Check for suspicious activity
            if ($this->detectSuspiciousActivity($storedData, $currentData)) {
                $this->handleSuspiciousActivity($request, $user);
                return;
            }
        }

        // Update session data
        Cache::put($sessionKey, $currentData, now()->addHours(2));
    }

    /**
     * Detect suspicious session activity.
     */
    protected function detectSuspiciousActivity(array $stored, array $current): bool
    {
        // Check for IP changes (unless configured to allow)
        if (!config('security.allow_ip_changes', false) && $stored['ip'] !== $current['ip']) {
            return true;
        }

        // Check for user agent changes
        if (!config('security.allow_user_agent_changes', false) && $stored['user_agent'] !== $current['user_agent']) {
            return true;
        }

        // Check for session hijacking patterns
        $timeDiff = $current['last_activity'] - $stored['last_activity'];
        if ($timeDiff < 0 || $timeDiff > 3600) { // 1 hour max gap
            return true;
        }

        return false;
    }

    /**
     * Handle suspicious activity.
     */
    protected function handleSuspiciousActivity(Request $request, $user): void
    {
        $this->logSecurityEvent('suspicious_activity', $request, [
            'user_id' => $user->id,
            'reason' => 'Session validation failed'
        ]);

        // Force logout
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Optional: Send security notification to user
        if (config('security.notify_suspicious_activity', true)) {
            // Queue notification email
            // Mail::to($user)->queue(new SuspiciousActivityNotification());
        }
    }

    /**
     * Log security events.
     */
    protected function logSecurityEvent(string $event, Request $request, array $context = []): void
    {
        Log::channel('security')->info("Security Event: {$event}", array_merge([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => $request->user()?->id,
            'timestamp' => now()->toISOString()
        ], $context));
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
     * Handle failed authentication attempt.
     */
    public function handleFailedAttempt(Request $request): void
    {
        $lockoutKey = 'lockout:' . $request->ip();
        $attempts = Cache::get($lockoutKey, 0) + 1;
        
        Cache::put($lockoutKey, $attempts, now()->addMinutes(30));
        
        if ($attempts >= config('security.max_failed_attempts', 5)) {
            Cache::put($lockoutKey . ':time', 1800, now()->addMinutes(30)); // 30 min lockout
        }

        $this->logSecurityEvent('authentication_failed', $request, [
            'attempts' => $attempts,
            'email' => $request->input('email')
        ]);
    }

    /**
     * Handle successful authentication.
     */
    public function handleSuccessfulAttempt(Request $request): void
    {
        // Clear lockout on successful login
        $lockoutKey = 'lockout:' . $request->ip();
        Cache::forget($lockoutKey);
        Cache::forget($lockoutKey . ':time');

        // Update last login
        if ($user = $request->user()) {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
        }

        $this->logSecurityEvent('login_success', $request);
    }
} 