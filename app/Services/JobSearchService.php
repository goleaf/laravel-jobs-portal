<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\User;
use Carbon\Carbon;

class JobSearchService
{
    /**
     * Process advanced search filters using forget() for cleanup
     */
    public function processAdvancedFilters(Request $request): Collection
    {
        $filters = collect($request->all());
        
        // Remove meta fields using forget()
        $metaFields = ['page', 'per_page', '_token', '_method', 'submit'];
        $filters->forget($metaFields);
        
        // Remove empty filters
        $emptyFilters = $filters->filter(fn($value) => empty($value))->keys();
        $filters->forget($emptyFilters->toArray());
        
        // Remove premium filters for basic users
        if (!auth()->user()?->hasActiveSubscription()) {
            $premiumFilters = ['salary_range_advanced', 'remote_work_options', 'company_benefits_filter'];
            $filters->forget($premiumFilters);
        }
        
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
} 