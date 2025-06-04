<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class RateLimitingService
{
    public function isAllowed(string $key, int $limit): bool
    {
        return Cache::get($key, 0) < $limit;
    }
    
    public function hit(string $key, int $window): int
    {
        $current = Cache::get($key, 0);
        $new = $current + 1;
        Cache::put($key, $new, $window);
        return $new;
    }
    
    public function getAttempts(string $key): int
    {
        return Cache::get($key, 0);
    }
    
    public function clear(string $key): void
    {
        Cache::forget($key);
    }
    
    public function buildKey(Request $request, string $type = "general"): string
    {
        $identifier = $request->user() 
            ? "user:" . $request->user()->id 
            : "ip:" . $request->ip();
            
        return "rate_limit:{$type}:{$identifier}";
    }
    
    public function getStats(): array
    {
        return [
            "status" => "active",
            "cache_driver" => config("cache.default"),
            "message" => "Rate limiting service operational"
        ];
    }
}