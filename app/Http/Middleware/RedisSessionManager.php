<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redis Session Management Middleware
 * Enhanced session handling with Redis backend
 */
class RedisSessionManager
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure Redis connection is available
        try {
            $redis = Redis::connection('sessions');
            $redis->ping();
        } catch (\Exception $e) {
            // Fallback to file sessions if Redis is unavailable
            config(['session.driver' => 'file']);
        }

        $response = $next($request);

        // Add Redis session headers for debugging
        if (app()->environment('local', 'development')) {
            $response->headers->set('X-Session-Driver', config('session.driver'));
            $response->headers->set('X-Redis-Connection', 'active');
        }

        return $response;
    }
}