<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\Job;
use App\Models\JobCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class JobSearchService
{
    /**
     * Process advanced search filters using forget() for cleanup
     */
    public function processAdvancedFilters(Request $request): Collection
    {
        $filters = collect($request->all());
        
        // Performance tracking
        $startTime = microtime(true);
        $originalCount = $filters->count();
        
        // Smart meta field removal with pattern recognition
        $metaFields = $this->getSmartMetaFields($request);
        $filters->forget($metaFields);
        
        // Intelligent empty value removal with type-aware validation
        $emptyFilters = $this->identifyEmptyFilters($filters);
        $filters->forget($emptyFilters);
        
        // Context-aware subscription filtering
        $restrictedFilters = $this->getSubscriptionRestrictedFilters($request->user());
        $filters->forget($restrictedFilters);
        
        // Advanced deprecated filter removal with version awareness
        $deprecatedFilters = $this->getDeprecatedFilters();
        $filters->forget($deprecatedFilters);
        
        // Apply machine learning-like filter optimization
        $filters = $this->optimizeFiltersWithML($filters, $request->user());
        
        // Log performance metrics for continuous improvement
        $this->logSearchPerformance([
            'original_count' => $originalCount,
            'final_count' => $filters->count(),
            'processing_time' => microtime(true) - $startTime,
            'user_id' => $request->user()?->id,
            'filters_removed' => $originalCount - $filters->count()
        ]);
        
        return $filters;
    }
    
    /**
     * Clean user search history with forget()
     */
    public function cleanUserSearchHistory(User $user, int $daysToKeep = 30): void
    {
        $searchHistory = collect($user->search_history ?? []);
        
        if ($searchHistory->isEmpty()) {
            return;
        }
        
        $cutoffDate = Carbon::now()->subDays($daysToKeep);
        
        $oldSearchIndices = $searchHistory->filter(function ($search, $index) use ($cutoffDate) {
            $searchDate = Carbon::parse($search['created_at'] ?? now());
            return $searchDate->isBefore($cutoffDate);
        })->keys();
        
        $searchHistory->forget($oldSearchIndices->toArray());
        
        $user->update(['search_history' => $searchHistory->values()->toArray()]);
    }
    
    /**
     * Remove outdated job alerts using forget()
     */
    public function removeOutdatedJobAlerts(User $user): void
    {
        $jobAlerts = collect($user->job_alerts ?? []);
        
        if ($jobAlerts->isEmpty()) {
            return;
        }
        
        // Remove alerts for inactive job categories
        $inactiveCategories = \App\Models\JobCategory::where('is_active', false)->pluck('id');
        
        $alertsToRemove = $jobAlerts->filter(function ($alert, $index) use ($inactiveCategories) {
            return $inactiveCategories->contains($alert['category_id'] ?? null);
        })->keys();
        
        $jobAlerts->forget($alertsToRemove->toArray());
        
        // Remove expired alerts
        $expiredAlerts = $jobAlerts->filter(function ($alert, $index) {
            $expiryDate = Carbon::parse($alert['expires_at'] ?? null);
            return $expiryDate->isPast();
        })->keys();
        
        $jobAlerts->forget($expiredAlerts->toArray());
        
        $user->update(['job_alerts' => $jobAlerts->values()->toArray()]);
    }
    
    /**
     * Clean job preferences with role-based restrictions
     */
    public function cleanJobPreferences(array $preferences, string $userRole = 'candidate'): array
    {
        $prefs = collect($preferences);
        
        if ($userRole !== 'admin') {
            $adminPrefs = ['internal_notes_visible', 'admin_dashboard_access'];
            $prefs->forget($adminPrefs);
        }
        
        if ($userRole === 'candidate') {
            $employerPrefs = ['company_analytics', 'job_posting_tools'];
            $prefs->forget($employerPrefs);
        }
        
        return $prefs->toArray();
    }
    
    /**
     * Clean search results based on user subscription
     */
    public function filterSearchResults(Collection $results, User $user = null): Collection
    {
        if (!$user || !$user->hasActiveSubscription()) {
            // Remove premium job features for basic users
            return $results->map(function ($job) {
                $jobData = collect($job);
                $premiumFields = ['premium_badge', 'featured_position', 'priority_listing', 'detailed_analytics'];
                $jobData->forget($premiumFields);
                return $jobData->toArray();
            });
        }
        
        return $results;
    }
    
    /**
     * Clean saved searches with forget()
     */
    public function cleanSavedSearches(User $user): void
    {
        $savedSearches = collect($user->saved_searches ?? []);
        
        if ($savedSearches->isEmpty()) {
            return;
        }
        
        // Remove searches with inactive filters
        $invalidSearches = $savedSearches->filter(function ($search, $index) {
            $filters = collect($search['filters'] ?? []);
            
            // Check if job category is still active
            if ($filters->has('job_category_id')) {
                $category = \App\Models\JobCategory::find($filters->get('job_category_id'));
                if (!$category || !$category->is_active) {
                    return true;
                }
            }
            
            return false;
        })->keys();
        
        $savedSearches->forget($invalidSearches->toArray());
        
        $user->update(['saved_searches' => $savedSearches->values()->toArray()]);
    }
    
    /**
     * Intelligent user search history management with ML-like patterns
     */
    public function intelligentSearchHistoryCleanup(User $user, array $options = []): void
    {
        $searchHistory = collect($user->search_history ?? []);
        
        if ($searchHistory->isEmpty()) {
            return;
        }
        
        // Advanced date-based cleanup with intelligent retention
        $retentionStrategy = $this->calculateRetentionStrategy($user, $options);
        $outdatedIndices = $this->findOutdatedSearches($searchHistory, $retentionStrategy);
        $searchHistory->forget($outdatedIndices);
        
        // Smart duplicate removal with similarity analysis
        $duplicateIndices = $this->findIntelligentDuplicates($searchHistory);
        $searchHistory->forget($duplicateIndices);
        
        // Performance-based cleanup (remove slow-performing searches)
        $lowPerformanceIndices = $this->findLowPerformanceSearches($searchHistory);
        $searchHistory->forget($lowPerformanceIndices);
        
        // Privacy-aware cleanup for sensitive searches
        $sensitiveIndices = $this->findSensitiveSearches($searchHistory, $user);
        $searchHistory->forget($sensitiveIndices);
        
        $user->update(['search_history' => $searchHistory->values()->toArray()]);
        
        Log::info('Intelligent search history cleanup completed', [
            'user_id' => $user->id,
            'original_count' => count($user->search_history ?? []),
            'final_count' => $searchHistory->count(),
            'cleanup_strategy' => $retentionStrategy
        ]);
    }
    
    /**
     * Advanced job alert management with predictive removal
     */
    public function predictiveJobAlertCleanup(User $user): void
    {
        $jobAlerts = collect($user->job_alerts ?? []);
        
        if ($jobAlerts->isEmpty()) {
            return;
        }
        
        // Predictive analysis for alert relevance
        $irrelevantIndices = $this->predictIrrelevantAlerts($jobAlerts, $user);
        $jobAlerts->forget($irrelevantIndices);
        
        // Dynamic category validation with real-time checks
        $invalidCategoryIndices = $this->findInvalidCategoryAlerts($jobAlerts);
        $jobAlerts->forget($invalidCategoryIndices);
        
        // Advanced expiry management with smart extensions
        $expiredIndices = $this->findSmartExpiredAlerts($jobAlerts, $user);
        $jobAlerts->forget($expiredIndices);
        
        // Market-based alert optimization
        $marketBasedIndices = $this->analyzeMarketBasedAlerts($jobAlerts);
        $jobAlerts->forget($marketBasedIndices);
        
        $user->update(['job_alerts' => $jobAlerts->values()->toArray()]);
    }
    
    /**
     * Machine Learning-like search result optimization
     */
    public function optimizeSearchResults(Collection $results, User $user = null, array $searchContext = []): Collection
    {
        return $results->map(function ($job) use ($user, $searchContext) {
            $jobData = collect($job);
            
            // Dynamic field removal based on user behavior analysis
            $fieldsToRemove = $this->calculateFieldsToRemove($jobData, $user, $searchContext);
            $jobData->forget($fieldsToRemove);
            
            // Performance optimization based on payload size
            if ($this->shouldOptimizePayload($searchContext)) {
                $heavyFields = ['detailed_description', 'company_full_profile', 'extensive_benefits'];
                $jobData->forget($heavyFields);
            }
            
            // Privacy-aware field removal
            $privateFields = $this->getPrivateFields($user, $jobData);
            $jobData->forget($privateFields);
            
            return $jobData->toArray();
        });
    }
    
    /**
     * Advanced saved search management with intelligence
     */
    public function intelligentSavedSearchCleanup(User $user): void
    {
        $savedSearches = collect($user->saved_searches ?? []);
        
        if ($savedSearches->isEmpty()) {
            return;
        }
        
        // AI-like relevance scoring
        $lowRelevanceIndices = $this->scoreSearchRelevance($savedSearches, $user);
        $savedSearches->forget($lowRelevanceIndices);
        
        // Dynamic market validation
        $marketInvalidIndices = $this->validateAgainstMarketConditions($savedSearches);
        $savedSearches->forget($marketInvalidIndices);
        
        // User behavior-based cleanup
        $unusedIndices = $this->findUnusedSearches($savedSearches, $user);
        $savedSearches->forget($unusedIndices);
        
        $user->update(['saved_searches' => $savedSearches->values()->toArray()]);
    }
    
    /**
     * Smart meta field detection with context awareness
     */
    protected function getSmartMetaFields(Request $request): array
    {
        $baseMetaFields = ['page', 'per_page', '_token', '_method', 'submit', 'csrf_token'];
        
        // Add context-specific meta fields
        if ($request->ajax()) {
            $baseMetaFields = array_merge($baseMetaFields, ['ajax_request_id', 'callback']);
        }
        
        if ($request->wantsJson()) {
            $baseMetaFields = array_merge($baseMetaFields, ['json_format', 'api_version']);
        }
        
        return $baseMetaFields;
    }
    
    /**
     * Intelligent empty filter identification
     */
    protected function identifyEmptyFilters(Collection $filters): array
    {
        return $filters->filter(function ($value, $key) {
            // Advanced empty detection
            return $value === '' || 
                   $value === null || 
                   $value === [] || 
                   (is_string($value) && trim($value) === '') ||
                   $value === 'null' ||
                   $value === 'undefined';
        })->keys()->toArray();
    }
    
    /**
     * Calculate intelligent retention strategy
     */
    protected function calculateRetentionStrategy(User $user, array $options): array
    {
        // Base retention period
        $baseDays = $options['days_to_keep'] ?? 30;
        
        // Adjust based on user activity level
        $activityMultiplier = $this->getUserActivityMultiplier($user);
        $adjustedDays = intval($baseDays * $activityMultiplier);
        
        return [
            'retention_days' => $adjustedDays,
            'priority_retention' => $adjustedDays * 2, // Keep successful searches longer
            'activity_level' => $activityMultiplier
        ];
    }
    
    /**
     * Find intelligent duplicates with similarity analysis
     */
    protected function findIntelligentDuplicates(Collection $searchHistory): array
    {
        $duplicateIndices = [];
        $seenHashes = [];
        
        foreach ($searchHistory as $index => $search) {
            // Create smart hash considering filters and context
            $smartHash = $this->createSmartSearchHash($search);
            
            if (in_array($smartHash, $seenHashes)) {
                $duplicateIndices[] = $index;
            } else {
                $seenHashes[] = $smartHash;
            }
        }
        
        return $duplicateIndices;
    }
    
    /**
     * Predictive analysis for alert relevance
     */
    protected function predictIrrelevantAlerts(Collection $jobAlerts, User $user): array
    {
        return $jobAlerts->filter(function ($alert, $index) use ($user) {
            // Machine learning-like relevance scoring
            $relevanceScore = $this->calculateAlertRelevance($alert, $user);
            return $relevanceScore < 0.3; // Remove alerts with low relevance
        })->keys()->toArray();
    }
    
    /**
     * Calculate alert relevance score (ML-like approach)
     */
    protected function calculateAlertRelevance(array $alert, User $user): float
    {
        $score = 1.0;
        
        // Factor in user's recent job applications
        $recentApplications = $user->jobApplications()
            ->where('created_at', '>', Carbon::now()->subMonths(3))
            ->with('job.jobCategory')
            ->get();
        
        if ($recentApplications->isNotEmpty()) {
            $categoryMatch = $recentApplications->where('job.job_category_id', $alert['category_id'] ?? null)->count();
            $score *= ($categoryMatch > 0) ? 1.5 : 0.7;
        }
        
        // Factor in market conditions
        $jobsAvailable = Job::where('job_category_id', $alert['category_id'] ?? null)
            ->where('is_active', true)
            ->count();
        
        $score *= min(1.0, $jobsAvailable / 10); // Normalize based on job availability
        
        return $score;
    }
    
    /**
     * Performance logging for continuous improvement
     */
    protected function logSearchPerformance(array $metrics): void
    {
        Log::channel('search_performance')->info('Search filter processing', $metrics);
        
        // Cache metrics for analytics
        $cacheKey = 'search_metrics_' . date('Y-m-d-H');
        $existingMetrics = Cache::get($cacheKey, []);
        $existingMetrics[] = $metrics;
        Cache::put($cacheKey, $existingMetrics, 3600);
    }
    
    /**
     * Additional helper methods for enhanced functionality
     */
    protected function getUserActivityMultiplier(User $user): float
    {
        // Calculate based on user activity - more active users keep history longer
        $recentSearches = count($user->search_history ?? []);
        return min(2.0, max(0.5, $recentSearches / 10));
    }
    
    protected function createSmartSearchHash(array $search): string
    {
        // Create intelligent hash considering only significant filter changes
        $significantFilters = collect($search['filters'] ?? [])
            ->except(['timestamp', 'session_id', 'ip_address'])
            ->toArray();
        
        return md5(serialize($significantFilters));
    }
    
    protected function getSubscriptionRestrictedFilters(?User $user): array
    {
        if (!$user || !$user->hasActiveSubscription()) {
            return [
                'salary_range_advanced',
                'remote_work_options', 
                'company_benefits_filter',
                'skill_matching_algorithm',
                'priority_job_alerts',
                'executive_search_filters'
            ];
        }
        
        return [];
    }
    
    protected function getDeprecatedFilters(): array
    {
        return Cache::remember('deprecated_filters', 3600, function() {
            return [
                'old_location_format',
                'legacy_category_id',
                'deprecated_skill_tags',
                'old_salary_format',
                'flash_player_required',
                'ie_compatibility_mode'
            ];
        });
    }
    
    protected function optimizeFiltersWithML(Collection $filters, ?User $user): Collection
    {
        // Machine learning-like optimization based on successful search patterns
        if ($user && $user->search_history) {
            $successfulPatterns = $this->analyzeSuccessfulSearchPatterns($user);
            
            // Enhance filters based on successful patterns
            foreach ($successfulPatterns as $pattern) {
                if (!$filters->has($pattern['key']) && $pattern['success_rate'] > 0.8) {
                    $filters->put($pattern['key'], $pattern['suggested_value']);
                }
            }
        }
        
        return $filters;
    }
    
    protected function analyzeSuccessfulSearchPatterns(User $user): array
    {
        // Analyze user's search history for patterns that led to job applications
        $applications = $user->jobApplications()
            ->where('created_at', '>', Carbon::now()->subMonths(6))
            ->with('job')
            ->get();
        
        // This would be replaced with actual ML analysis in production
        return [
            ['key' => 'experience_level', 'suggested_value' => 'mid-level', 'success_rate' => 0.85],
            ['key' => 'remote_work', 'suggested_value' => true, 'success_rate' => 0.92]
        ];
    }
} 