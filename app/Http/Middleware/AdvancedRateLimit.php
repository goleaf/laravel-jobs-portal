<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdvancedRateLimit
{
    public function handle(Request $request, \Closure $next, string $limitType = 'api.general')
    {
        $limits = [
            'api.general' => 60,  // 60 requests per minute
            'api.auth' => 10,     // 10 auth requests per minute
            'api.search' => 30,   // 30 search requests per minute
            'api.upload' => 5,    // 5 upload requests per minute
        ];

        $limit = $limits[$limitType] ?? 60;
        $identifier = $request->user() ? 'user:'.$request->user()->id : 'ip:'.$request->ip();
        $key = "rate_limit:{$limitType}:{$identifier}";

        $attempts = Cache::get($key, 0);

        if ($attempts >= $limit) {
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => 60,
                'limit' => $limit,
            ], 429)->withHeaders([
                'X-RateLimit-Limit' => $limit,
                'X-RateLimit-Remaining' => 0,
                'X-RateLimit-Reset' => now()->addMinute()->timestamp,
                'Retry-After' => 60,
            ]);
        }

        Cache::put($key, $attempts + 1, 60);

        $response = $next($request);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $limit,
            'X-RateLimit-Remaining' => max(0, $limit - $attempts - 1),
            'X-RateLimit-Reset' => now()->addMinute()->timestamp,
        ]);
    }
}
