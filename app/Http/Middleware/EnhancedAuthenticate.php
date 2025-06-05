<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EnhancedAuthenticate extends Middleware
{
    /**
     * Maximum failed login attempts before account lockout
     */
    const MAX_FAILED_ATTEMPTS = 5;

    /**
     * Account lockout duration in minutes
     */
    const LOCKOUT_DURATION = 30;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, $next, ...$guards)
    {
        // Check if user is attempting to login
        if ($this->isLoginAttempt($request)) {
            $this->checkAccountLockout($request);
        }

        // Call parent authentication
        $response = parent::handle($request, $next, ...$guards);

        // Check for suspicious activity if user is authenticated
        if (Auth::check()) {
            $this->checkSuspiciousActivity($request);
        }

        return $response;
    }

    /**
     * Check if this is a login attempt
     */
    protected function isLoginAttempt(Request $request): bool
    {
        return $request->isMethod('POST') && 
               ($request->routeIs('login') || $request->is('*/login') || $request->is('login'));
    }

    /**
     * Check if account is locked out
     */
    protected function checkAccountLockout(Request $request): void
    {
        $email = $request->input('email');
        if (!$email) return;

        $lockoutKey = 'login_lockout:' . $email;
        $attemptsKey = 'login_attempts:' . $email;

        // Check if account is currently locked
        if (Cache::has($lockoutKey)) {
            $lockoutTime = Cache::get($lockoutKey);
            $remainingTime = Carbon::parse($lockoutTime)->addMinutes(self::LOCKOUT_DURATION)->diffInMinutes(now());
            
            Log::warning('Attempted login to locked account', [
                'email' => $email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'remaining_lockout_minutes' => $remainingTime
            ]);

            abort(423, "Account is locked due to too many failed login attempts. Please try again in {$remainingTime} minutes.");
        }
    }

    /**
     * Record failed login attempt and check for lockout
     */
    public static function recordFailedAttempt(string $email, Request $request): void
    {
        $attemptsKey = 'login_attempts:' . $email;
        $attempts = Cache::get($attemptsKey, 0) + 1;
        
        // Store attempts for 1 hour
        Cache::put($attemptsKey, $attempts, 3600);

        Log::warning('Failed login attempt', [
            'email' => $email,
            'attempt_number' => $attempts,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Lock account if too many attempts
        if ($attempts >= self::MAX_FAILED_ATTEMPTS) {
            $lockoutKey = 'login_lockout:' . $email;
            Cache::put($lockoutKey, now(), self::LOCKOUT_DURATION * 60);
            Cache::forget($attemptsKey);

            Log::critical('Account locked due to failed login attempts', [
                'email' => $email,
                'total_attempts' => $attempts,
                'ip' => $request->ip(),
                'lockout_duration_minutes' => self::LOCKOUT_DURATION
            ]);
        }
    }

    /**
     * Clear failed attempts on successful login
     */
    public static function clearFailedAttempts(string $email): void
    {
        $attemptsKey = 'login_attempts:' . $email;
        $lockoutKey = 'login_lockout:' . $email;
        
        Cache::forget($attemptsKey);
        Cache::forget($lockoutKey);
    }

    /**
     * Check for suspicious activity
     */
    protected function checkSuspiciousActivity(Request $request): void
    {
        $user = Auth::user();
        if (!$user) return;

        $sessionKey = 'user_session:' . $user->id;
        $lastSession = Cache::get($sessionKey, []);

        $currentSession = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
            'route' => $request->route()?->getName() ?? $request->path()
        ];

        // Check for IP address change
        if (!empty($lastSession['ip']) && $lastSession['ip'] !== $currentSession['ip']) {
            Log::warning('User IP address changed during session', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'old_ip' => $lastSession['ip'],
                'new_ip' => $currentSession['ip'],
                'time_since_last_request' => isset($lastSession['timestamp']) 
                    ? Carbon::parse($lastSession['timestamp'])->diffInMinutes(now()) 
                    : 'unknown'
            ]);
        }

        // Check for user agent change
        if (!empty($lastSession['user_agent']) && $lastSession['user_agent'] !== $currentSession['user_agent']) {
            Log::warning('User agent changed during session', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'old_user_agent' => $lastSession['user_agent'],
                'new_user_agent' => $currentSession['user_agent'],
                'ip' => $currentSession['ip']
            ]);
        }

        // Check for unusual time gaps (more than 12 hours)
        if (!empty($lastSession['timestamp'])) {
            $timeDiff = Carbon::parse($lastSession['timestamp'])->diffInHours(now());
            if ($timeDiff > 12) {
                Log::info('User session resumed after extended absence', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'hours_since_last_activity' => $timeDiff,
                    'ip' => $currentSession['ip']
                ]);
            }
        }

        // Store current session info (expires in 2 hours)
        Cache::put($sessionKey, $currentSession, 7200);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
} 