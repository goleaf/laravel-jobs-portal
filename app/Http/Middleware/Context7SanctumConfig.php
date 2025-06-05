<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Context7 Sanctum Configuration Middleware
 * Handles Sanctum-specific configurations and security enhancements
 */
class Context7SanctumConfig
{
    /**
     * Handle an incoming request with Context7 security patterns
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Context7 Pattern: Add security headers for API responses
        $response = $next($request);
        
        if ($request->is('api/*')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // Context7 Pattern: API versioning header
            $response->headers->set('X-API-Version', '1.0.0');
            
            // Context7 Pattern: Rate limit info (if available)
            if ($request->user() && $request->user()->currentAccessToken()) {
                $response->headers->set('X-Token-Name', $request->user()->currentAccessToken()->name);
                $response->headers->set('X-Token-Abilities', implode(',', $request->user()->currentAccessToken()->abilities));
            }
        }
        
        return $response;
    }
}