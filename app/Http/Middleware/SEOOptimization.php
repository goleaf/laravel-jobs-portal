<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class SEOOptimization
{
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        // Add SEO-friendly headers
        $response->headers->set('X-Robots-Tag', 'index, follow');

        // Add canonical header for JSON responses
        if ($request->expectsJson()) {
            $response->headers->set('Link', '<'.$request->url().'>; rel="canonical"');
        }

        // Remove sensitive headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
