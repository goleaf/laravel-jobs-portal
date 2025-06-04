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
        });
    }

    /**
     * Cache company data
     */
    public static function cacheCompanyData($companyId): array
    {
        $cacheKey = "company:{$companyId}";
        
        return Cache::remember($cacheKey, self::COMPANY_CACHE_TTL, function () use ($companyId) {
            return app('App\Models\Company')::with(['jobs:id,company_id,title,status'])
                ->find($companyId);
        });
    }

    /**
     * Cache user dashboard stats
     */
    public static function cacheUserStats($userId, $userType): array
    {
        $cacheKey = "user:stats:{$userId}:{$userType}";
        
        return Cache::remember($cacheKey, self::STATS_CACHE_TTL, function () use ($userId, $userType) {
            return app('App\Services\QueryOptimizer')::optimizeUserDashboard($userId, $userType);
        });
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
        });
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
        });
    }

    /**
     * Clear related caches
     */
    public static function clearJobCaches($jobId = null): void
    {
        Cache::tags(['jobs'])->flush();
        
        if ($jobId) {
            Cache::forget("job:{$jobId}");
        }
    }

    /**
     * Clear user caches
     */
    public static function clearUserCaches($userId): void
    {
        $patterns = [
            "user:stats:{$userId}:*",
            "user:profile:{$userId}",
            "user:applications:{$userId}"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }
}