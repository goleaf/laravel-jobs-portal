<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Universal Sanctum Configuration Middleware
 * Handles Sanctum-specific configurations and security enhancements
 */
class UniversalSanctumConfig
{
    /**
     * Handle an incoming request with Universal security patterns
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Universal Pattern: Add security headers for API responses
        $response = $next($request);
        
        if ($request->is('api/*')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // Universal Pattern: API versioning header
            $response->headers->set('X-API-Version', '1.0.0');
            
            // Universal Pattern: Rate limit info (if available)
            if ($request->user() && $request->user()->currentAccessToken()) {
                $response->headers->set('X-Token-Name', $request->user()->currentAccessToken()->name);
                $response->headers->set('X-Token-Abilities', implode(',', $request->user()->currentAccessToken()->abilities));
            }
        }
        
        return $response;
    }
}