<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CollectionForgetUtility
{
    /**
     * Clean user input based on role and subscription
     */
    public static function sanitizeUserInput(
        array $input, 
        ?string $userRole = null, 
        bool $hasSubscription = false
    ): array {
        $data = collect($input);
        
        // Always remove these security fields
        $alwaysRemove = ['_token', '_method', 'password_confirmation', 'csrf_token'];
        $data->forget($alwaysRemove);
        
        // Role-based field removal
        if ($userRole !== 'admin') {
            $adminOnlyFields = ['is_featured', 'admin_notes', 'priority_score', 'internal_flags', 'is_active'];
            $data->forget($adminOnlyFields);
        }
        
        // Subscription-based field removal
        if (!$hasSubscription) {
            $premiumFields = ['premium_features', 'advanced_analytics', 'priority_support', 'premium_branding'];
            $data->forget($premiumFields);
        }
        
        return $data->toArray();
    }
    
    /**
     * Clean API response based on multiple conditions
     */
    public static function prepareApiResponse(array $response, array $conditions): array
    {
        $data = collect($response);
        
        foreach ($conditions as $condition => $fieldsToRemove) {
            if (self::evaluateCondition($condition)) {
                $data->forget($fieldsToRemove);
            }
        }
        
        return $data->toArray();
    }
    
    /**
     * Remove temporary or deprecated fields based on patterns
     */
    public static function cleanupTemporaryData(array $data, array $patterns = []): array
    {
        $collection = collect($data);
        
        $defaultPatterns = ['temp_', 'cache_', 'session_', 'debug_', 'draft_'];
        $allPatterns = array_merge($defaultPatterns, $patterns);
        
        $keysToRemove = $collection->keys()->filter(function ($key) use ($allPatterns) {
            return collect($allPatterns)->contains(fn($pattern) => str_starts_with($key, $pattern));
        });
        
        $collection->forget($keysToRemove->toArray());
        
        return $collection->toArray();
    }
    
    /**
     * Clean form request data with role-based restrictions
     */
    public static function cleanFormRequestData(array $data, ?string $userRole = null): array
    {
        $collection = collect($data);
        
        // Remove form metadata
        $formMetadata = ['_token', '_method', 'submit', 'csrf_token'];
        $collection->forget($formMetadata);
        
        // Remove sensitive fields for non-admin users
        if ($userRole !== 'admin') {
            $sensitiveFields = ['is_active', 'is_featured', 'admin_only_field', 'priority_score'];
            $collection->forget($sensitiveFields);
        }
        
        return $collection->toArray();
    }
    
    /**
     * Clean test data by removing specified fields
     */
    public static function cleanTestData(array $data, array $fieldsToRemove): array
    {
        $collection = collect($data);
        $collection->forget($fieldsToRemove);
        return $collection->toArray();
    }
    
    /**
     * Remove fields based on user subscription level
     */
    public static function filterBySubscription(array $data, string $subscriptionLevel = 'basic'): array
    {
        $collection = collect($data);
        
        $subscriptionRestrictions = [
            'basic' => ['premium_features', 'advanced_analytics', 'priority_support', 'premium_branding'],
            'premium' => ['enterprise_features', 'white_label_options'],
            'enterprise' => [] // Enterprise has access to all features
        ];
        
        if (isset($subscriptionRestrictions[$subscriptionLevel])) {
            $collection->forget($subscriptionRestrictions[$subscriptionLevel]);
        }
        
        return $collection->toArray();
    }
    
    /**
     * Advanced cleanup with multiple criteria
     */
    public static function advancedCleanup(array $data, array $options = []): array
    {
        $collection = collect($data);
        
        // Remove empty values if requested
        if ($options['remove_empty'] ?? false) {
            $emptyKeys = $collection->filter(fn($value) => empty($value))->keys();
            $collection->forget($emptyKeys->toArray());
        }
        
        // Remove null values if requested
        if ($options['remove_null'] ?? false) {
            $nullKeys = $collection->filter(fn($value) => is_null($value))->keys();
            $collection->forget($nullKeys->toArray());
        }
        
        // Remove specific patterns
        if (!empty($options['remove_patterns'])) {
            $patternKeys = $collection->keys()->filter(function ($key) use ($options) {
                return collect($options['remove_patterns'])->contains(fn($pattern) => str_contains($key, $pattern));
            });
            $collection->forget($patternKeys->toArray());
        }
        
        return $collection->toArray();
    }
    
    /**
     * Evaluate condition for field removal
     */
    private static function evaluateCondition(string $condition): bool
    {
        return match($condition) {
            'is_guest' => !auth()->check(),
            'is_mobile' => request()->header('User-Agent-Type') === 'mobile',
            'is_basic_user' => !auth()->user()?->hasActiveSubscription(),
            'is_public_api' => request()->is('api/public/*'),
            'is_admin' => auth()->user()?->hasRole('admin'),
            'is_employer' => auth()->user()?->hasRole('employer'),
            'is_candidate' => auth()->user()?->hasRole('candidate'),
            'non_owner' => !self::isResourceOwner(),
            default => false,
        };
    }
    
    /**
     * Check if current user is resource owner
     */
    private static function isResourceOwner(): bool
    {
        // This would need to be implemented based on your specific resource ownership logic
        return auth()->user()?->hasRole('admin') ?? false;
    }
} 