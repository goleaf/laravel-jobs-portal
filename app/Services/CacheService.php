<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    const CACHE_TAGS = [
        'jobs' => 'jobs',
        'companies' => 'companies',
        'users' => 'users',
        'translations' => 'translations',
        'settings' => 'settings'
    ];
    
    const CACHE_DURATIONS = [
        'short' => 300,    // 5 minutes
        'medium' => 3600,  // 1 hour
        'long' => 86400,   // 24 hours
        'extended' => 604800 // 1 week
    ];
    
    /**
     * Cache jobs with intelligent tagging
     */
    public static function cacheJobs($key, $callback, $duration = 'medium')
    {
        return Cache::tags([self::CACHE_TAGS['jobs']])
            ->remember($key, self::CACHE_DURATIONS[$duration], $callback);
    }
    
    /**
     * Cache companies data
     */
    public static function cacheCompanies($key, $callback, $duration = 'medium')
    {
        return Cache::tags([self::CACHE_TAGS['companies']])
            ->remember($key, self::CACHE_DURATIONS[$duration], $callback);
    }
    
    /**
     * Cache user data
     */
    public static function cacheUsers($key, $callback, $duration = 'short')
    {
        return Cache::tags([self::CACHE_TAGS['users']])
            ->remember($key, self::CACHE_DURATIONS[$duration], $callback);
    }
    
    /**
     * Cache translations with extended duration
     */
    public static function cacheTranslations($key, $callback, $duration = 'extended')
    {
        return Cache::tags([self::CACHE_TAGS['translations']])
            ->remember($key, self::CACHE_DURATIONS[$duration], $callback);
    }
    
    /**
     * Cache application settings
     */
    public static function cacheSettings($key, $callback, $duration = 'long')
    {
        return Cache::tags([self::CACHE_TAGS['settings']])
            ->remember($key, self::CACHE_DURATIONS[$duration], $callback);
    }
    
    /**
     * Invalidate cache by tags
     */
    public static function invalidate($tags)
    {
        if (is_string($tags)) {
            $tags = [$tags];
        }
        
        Cache::tags($tags)->flush();
    }
    
    /**
     * Get cache statistics
     */
    public static function getStats()
    {
        try {
            $redis = Redis::connection();
            
            return [
                'total_keys' => $redis->dbsize(),
                'memory_usage' => $redis->info('memory')['used_memory_human'] ?? 'N/A',
                'hit_rate' => $redis->info('stats')['keyspace_hits'] ?? 0,
                'miss_rate' => $redis->info('stats')['keyspace_misses'] ?? 0,
            ];
        } catch (\Exception $e) {
            return ['error' => 'Redis not available'];
        }
    }
    
    /**
     * Warm up critical caches
     */
    public static function warmUp()
    {
        // Warm up job categories
        self::cacheJobs('job_categories', function() {
            return \App\Models\JobCategory::all();
        });
        
        // Warm up featured companies
        self::cacheCompanies('featured_companies', function() {
            return \App\Models\Company::where('is_featured', true)->limit(10)->get();
        });
        
        // Warm up active jobs count
        self::cacheJobs('active_jobs_count', function() {
            return \App\Models\Job::where('status', 'active')->count();
        });
        
        // Warm up translations
        self::cacheTranslations('app_translations', function() {
            $translations = [];
            $languages = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
            
            foreach ($languages as $lang) {
                $path = "lang/{$lang}.json";
                if (file_exists($path)) {
                    $translations[$lang] = json_decode(file_get_contents($path), true);
                }
            }
            
            return $translations;
        });
    }
}