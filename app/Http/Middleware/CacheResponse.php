<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheResponse
{
    public function handle(Request $request, \Closure $next, $ttl = 3600)
    {
        if ($request->isMethod('GET')) {
            $cacheKey = 'response:'.md5($request->fullUrl());

            $response = Cache::remember($cacheKey, $ttl, function () use ($request, $next) {
                return $next($request);
            });

            if (is_object($response)) {
                $response->headers->set('X-Cache', 'HIT');
            }

            return $response;
        }

        return $next($request);
    }
}
