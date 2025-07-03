<?php

namespace App\Http\Middleware;

use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class EnhancedAuthorization
{
    protected SecurityService $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next, ...$permissions): SymfonyResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check account status
        if (! $this->isAccountActive($user)) {
            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'Account inactive']);
        }

        // Check permissions
        if (! empty($permissions) && ! $this->hasPermissions($user, $permissions)) {
            return abort(403, 'Unauthorized access');
        }

        // Check route access
        if (! $this->hasRouteAccess($request, $user)) {
            return abort(403, 'Unauthorized access');
        }

        return $next($request);
    }

    /**
     * Check if user account is active.
     *
     * @param  mixed  $user
     */
    protected function isAccountActive($user): bool
    {
        // Check basic account status
        if (method_exists($user, 'isActive') && ! $user->isActive()) {
            return false;
        }

        // Check if account is suspended
        if (method_exists($user, 'isSuspended') && $user->isSuspended()) {
            return false;
        }

        // Check if account is banned
        if (method_exists($user, 'isBanned') && $user->isBanned()) {
            return false;
        }

        // Check email verification for sensitive operations
        if (method_exists($user, 'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
            // Allow access to email verification routes
            $allowedRoutes = ['verification.notice', 'verification.send', 'verification.verify'];
            if (! request()->routeIs($allowedRoutes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has required permissions.
     *
     * @param  mixed  $user
     */
    protected function hasPermissions($user, array $permissions): bool
    {
        // Cache permissions for performance
        $cacheKey = 'user_permissions:'.$user->id;
        $userPermissions = Cache::remember($cacheKey, 3600, function () use ($user) {
            return $this->getUserPermissions($user);
        });

        foreach ($permissions as $permission) {
            if (! in_array($permission, $userPermissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get user permissions based on roles.
     *
     * @param  mixed  $user
     */
    protected function getUserPermissions($user): array
    {
        $permissions = [];

        // Get role-based permissions
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin')) {
                $permissions = array_merge($permissions, [
                    'admin.access', 'admin.dashboard', 'admin.users.manage',
                    'admin.jobs.manage', 'admin.companies.manage',
                ]);
            }
            if ($user->hasRole('employer')) {
                $permissions = array_merge($permissions, [
                    'employer.access', 'employer.dashboard', 'employer.jobs.manage',
                    'employer.applications.view',
                ]);
            }
            if ($user->hasRole('candidate')) {
                $permissions = array_merge($permissions, [
                    'candidate.access', 'candidate.dashboard', 'candidate.jobs.apply',
                ]);
            }
        }

        return array_unique($permissions);
    }

    /**
     * Check route-based access control.
     *
     * @param  mixed  $user
     */
    protected function hasRouteAccess(Request $request, $user): bool
    {
        $routeName = $request->route()?->getName() ?? '';

        // Admin routes
        if (str_starts_with($routeName, 'admin.')) {
            return method_exists($user, 'hasRole') && $user->hasRole('admin');
        }

        // Employer routes
        if (str_starts_with($routeName, 'employer.')) {
            return method_exists($user, 'hasRole')
                   && ($user->hasRole('employer') || $user->hasRole('admin'));
        }

        // Candidate routes
        if (str_starts_with($routeName, 'candidate.')) {
            return method_exists($user, 'hasRole')
                   && ($user->hasRole('candidate') || $user->hasRole('admin'));
        }

        return true;
    }

    /**
     * Log authorization events.
     *
     * @param  null|mixed  $user
     */
    protected function logAuthorizationEvent(string $event, Request $request, $user = null, array $context = []): void
    {
        Log::channel('security')->info("Authorization Event: {$event}", [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'route' => $request->route()?->getName(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'required_permissions' => $context,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
