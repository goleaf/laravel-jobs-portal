<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user has admin role (assuming you have a role system)
        if (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('Admin')) {
            return $next($request);
        }

        // Alternative check if you have a simple is_admin field
        if (isset(auth()->user()->user_type) && auth()->user()->user_type == 1) {
            return $next($request);
        }

        // If no role system, check if user_type is admin (1) or if there's an is_admin field
        if (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin()) {
            return $next($request);
        }

        abort(403, 'Access denied. Admin privileges required.');
    }
}
