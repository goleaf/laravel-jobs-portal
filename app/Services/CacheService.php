<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    const JOB_CACHE_TTL = 3600; // 1 hour
    const COMPANY_CACHE_TTL = 7200; // 2 hours
    const USER_CACHE_TTL = 1800; // 30 minutes
    const STATS_CACHE_TTL = 900; // 15 minutes

    /**
     * Cache job listings
     */
    public static function cacheJobListings($page = 1, $filters = []): string
    {
        $cacheKey = 'jobs:list:' . md5(serialize([$page, $filters]));
        
        return Cache::remember($cacheKey, self::JOB_CACHE_TTL, function () use ($page, $filters) {
            return app('App\Services\JobService')->getJobListings($page, $filters);
        
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
});
    
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

    /**
     * Cache company data
     */
    public static function cacheCompanyData($companyId): array
    {
        $cacheKey = "company:{$companyId
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}";
        
        return Cache::remember($cacheKey, self::COMPANY_CACHE_TTL, function () use ($companyId) {
            return app('App\Models\Company')::with(['jobs:id,company_id,title,status'])
                ->find($companyId);
        
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
});
    
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

    /**
     * Cache user dashboard stats
     */
    public static function cacheUserStats($userId, $userType): array
    {
        $cacheKey = "user:stats:{$userId
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}:{$userType
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}";
        
        return Cache::remember($cacheKey, self::STATS_CACHE_TTL, function () use ($userId, $userType) {
            return app('App\Services\QueryOptimizer')::optimizeUserDashboard($userId, $userType);
        
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
});
    
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

    /**
     * Cache popular searches
     */
    public static function cachePopularSearches(): array
    {
        return Cache::remember('popular:searches', 86400, function () {
            return [
                'PHP Developer',
                'Laravel Developer',
                'Frontend Developer',
                'Project Manager',
                'Data Analyst'
            ];
        
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
});
    
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

    /**
     * Cache application statistics
     */
    public static function cacheApplicationStats(): array
    {
        return Cache::remember('app:stats', self::STATS_CACHE_TTL, function () {
            return [
                'total_jobs' => app('App\Models\Job')::where('status', 'active')->count(),
                'total_companies' => app('App\Models\Company')::where('is_active', true)->count(),
                'total_candidates' => app('App\Models\User')::where('user_type', 'candidate')->count(),
                'total_applications' => app('App\Models\JobApplication')::count(),
            ];
        
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
});
    
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

    /**
     * Clear related caches
     */
    public static function clearJobCaches($jobId = null): void
    {
        Cache::tags(['jobs'])->flush();
        
        if ($jobId) {
            Cache::forget("job:{$jobId
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}");
        
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
    
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

    /**
     * Clear user caches
     */
    public static function clearUserCaches($userId): void
    {
        $patterns = [
            "user:stats:{$userId
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}:*",
            "user:profile:{$userId
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}",
            "user:applications:{$userId
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
    
    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

    /**
     * Redis-specific bulk operations
     */
    public function bulkForget(array $keys): void
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection('cache');
            $redis->del($keys);
        } catch (\Exception $e) {
            // Fallback to individual deletions
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Check Redis connection status
     */
    public function isRedisAvailable(): bool
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $redis->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}