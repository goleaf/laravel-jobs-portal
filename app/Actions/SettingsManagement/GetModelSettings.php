<?php

namespace App\Actions\SettingsManagement;

use App\Data\SettingsManagement\ModelSettingsData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Action;
use LumoSolutions\Actionable\Traits\IsDispatchable;

/**
 * Get Model Settings Action
 * 
 * Comprehensive action for retrieving model settings with intelligent caching,
 * performance optimization, and flexible retrieval options.
 */
class GetModelSettings extends Action
{
    use IsDispatchable;

    /**
     * Execute the settings retrieval action
     */
    public function handle(ModelSettingsData $settingsData): array
    {
        try {
            // Validate the request data
            $this->validateRequestData($settingsData);

            // Get the model instance
            $model = $this->getModelInstance($settingsData->modelType, $settingsData->modelId);

            // Determine cache strategy
            $cacheKey = $this->getCacheKey($settingsData);
            $cacheDuration = $settingsData->cacheDuration ?? 3600;

            // Try to get from cache first
            if ($cacheDuration > 0) {
                $cachedResult = Cache::get($cacheKey);
                if ($cachedResult) {
                    $this->logCacheHit($settingsData, $cacheKey);
                    return $this->enhanceResult($cachedResult, $settingsData, true);
                }
            }

            // Retrieve settings from model
            $settings = $this->retrieveSettings($model, $settingsData);

            // Process and format the result
            $result = $this->processSettingsResult($model, $settings, $settingsData);

            // Cache the result if caching is enabled
            if ($cacheDuration > 0) {
                Cache::put($cacheKey, $result, now()->addSeconds($cacheDuration));
                $this->logCacheStore($settingsData, $cacheKey);
            }

            return $this->enhanceResult($result, $settingsData, false);

        } catch (\Exception $e) {
            Log::error('Settings retrieval failed', [
                'model_type' => $settingsData->modelType,
                'model_id' => $settingsData->modelId,
                'error' => $e->getMessage(),
                'settings_keys' => $settingsData->settingsKeys,
            ]);

            throw $e;
        }
    }

    /**
     * Validate the request data structure
     */
    protected function validateRequestData(ModelSettingsData $settingsData): void
    {
        if (empty($settingsData->modelType)) {
            throw new \InvalidArgumentException('Model type is required');
        }

        if (!class_exists($settingsData->modelType)) {
            throw new \InvalidArgumentException("Model class {$settingsData->modelType} does not exist");
        }

        if ($settingsData->requiresModelInstance() && empty($settingsData->modelId)) {
            throw new \InvalidArgumentException('Model ID is required for this operation');
        }
    }

    /**
     * Get model instance
     */
    protected function getModelInstance(string $modelType, int|string $modelId): Model
    {
        $model = $modelType::find($modelId);

        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                "Model {$modelType} with ID {$modelId} not found"
            );
        }

        if (!method_exists($model, 'settings')) {
            throw new \InvalidArgumentException("Model {$modelType} does not have settings capability");
        }

        return $model;
    }

    /**
     * Get cache key for this settings request
     */
    protected function getCacheKey(ModelSettingsData $settingsData): string
    {
        $keyParts = [
            'model_settings',
            class_basename($settingsData->modelType),
            $settingsData->modelId,
            'get'
        ];

        // Add specific keys to cache key if only requesting certain settings
        if ($settingsData->settingsKeys) {
            $keyParts[] = md5(implode(',', $settingsData->settingsKeys));
        }

        return implode(':', $keyParts);
    }

    /**
     * Retrieve settings from the model
     */
    protected function retrieveSettings(Model $model, ModelSettingsData $settingsData): array
    {
        // If specific keys are requested, get only those
        if ($settingsData->settingsKeys) {
            return $this->getSpecificSettings($model, $settingsData->settingsKeys);
        }

        // Otherwise get all settings
        return $model->settings()->all();
    }

    /**
     * Get specific settings by keys
     */
    protected function getSpecificSettings(Model $model, array $keys): array
    {
        $settings = [];

        foreach ($keys as $key) {
            $value = $model->settings()->get($key);
            if ($value !== null) {
                $this->setNestedArrayValue($settings, $key, $value);
            }
        }

        return $settings;
    }

    /**
     * Process and format the settings result
     */
    protected function processSettingsResult(Model $model, array $settings, ModelSettingsData $settingsData): array
    {
        $result = [
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'settings' => $settings,
            'retrieved_at' => now()->toISOString(),
            'cache_enabled' => ($settingsData->cacheDuration ?? 0) > 0,
        ];

        // Add default settings if requested
        if (property_exists($model, 'defaultSettings')) {
            $result['default_settings'] = $model->defaultSettings;
            $result['has_custom_settings'] = !empty($settings);
        }

        // Add settings metadata
        $result['metadata'] = $this->getSettingsMetadata($model, $settings, $settingsData);

        // Add validation rules if available
        if (property_exists($model, 'settingsRules')) {
            $result['validation_rules'] = $model->settingsRules;
        }

        return $result;
    }

    /**
     * Get settings metadata
     */
    protected function getSettingsMetadata(Model $model, array $settings, ModelSettingsData $settingsData): array
    {
        $metadata = [
            'settings_count' => count($this->flattenArray($settings)),
            'categories' => $this->getSettingsCategories($settings),
            'last_updated' => $this->getLastUpdatedTimestamp($model),
            'is_complete' => $this->checkSettingsCompleteness($model, $settings),
        ];

        // Add model-specific metadata
        $metadata = array_merge($metadata, $this->getModelSpecificMetadata($model, $settings));

        return $metadata;
    }

    /**
     * Get settings categories from the settings structure
     */
    protected function getSettingsCategories(array $settings): array
    {
        $categories = [];

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $categories[] = $key;
            }
        }

        return array_unique($categories);
    }

    /**
     * Get last updated timestamp for settings
     */
    protected function getLastUpdatedTimestamp(Model $model): ?string
    {
        // Try to get from model's updated_at if available
        if ($model->updated_at) {
            return $model->updated_at->toISOString();
        }

        return null;
    }

    /**
     * Check if settings are complete based on default settings
     */
    protected function checkSettingsCompleteness(Model $model, array $settings): bool
    {
        if (!property_exists($model, 'defaultSettings')) {
            return true; // No defaults to compare against
        }

        $defaultFlat = $this->flattenArray($model->defaultSettings);
        $currentFlat = $this->flattenArray($settings);

        foreach ($defaultFlat as $key => $defaultValue) {
            if (!array_key_exists($key, $currentFlat)) {
                return false; // Missing a default setting
            }
        }

        return true;
    }

    /**
     * Get model-specific metadata
     */
    protected function getModelSpecificMetadata(Model $model, array $settings): array
    {
        $modelClass = get_class($model);

        return match ($modelClass) {
            'App\Models\User' => $this->getUserSettingsMetadata($model, $settings),
            'App\Models\Company' => $this->getCompanySettingsMetadata($model, $settings),
            'App\Models\Job' => $this->getJobSettingsMetadata($model, $settings),
            'App\Models\Candidate' => $this->getCandidateSettingsMetadata($model, $settings),
            default => []
        };
    }

    /**
     * Get user-specific settings metadata
     */
    protected function getUserSettingsMetadata(Model $user, array $settings): array
    {
        return [
            'profile_completion_percentage' => $this->calculateProfileCompletion($user, $settings),
            'privacy_level' => $this->calculatePrivacyLevel($settings),
            'notification_preferences_set' => $this->hasNotificationPreferences($settings),
        ];
    }

    /**
     * Get company-specific settings metadata
     */
    protected function getCompanySettingsMetadata(Model $company, array $settings): array
    {
        return [
            'branding_configured' => $this->hasBrandingConfiguration($settings),
            'recruitment_preferences_set' => $this->hasRecruitmentPreferences($settings),
            'feature_flags_count' => $this->countFeatureFlags($settings),
        ];
    }

    /**
     * Get job-specific settings metadata
     */
    protected function getJobSettingsMetadata(Model $job, array $settings): array
    {
        return [
            'seo_optimized' => $this->isSeoOptimized($settings),
            'workflow_configured' => $this->hasWorkflowConfiguration($settings),
            'analytics_enabled' => $this->hasAnalyticsEnabled($settings),
        ];
    }

    /**
     * Get candidate-specific settings metadata
     */
    protected function getCandidateSettingsMetadata(Model $candidate, array $settings): array
    {
        return [
            'job_preferences_configured' => $this->hasJobPreferences($settings),
            'privacy_settings_configured' => $this->hasPrivacySettings($settings),
            'profile_visibility' => $this->getProfileVisibility($settings),
        ];
    }

    /**
     * Enhance the result with additional information
     */
    protected function enhanceResult(array $result, ModelSettingsData $settingsData, bool $fromCache): array
    {
        $enhanced = $result;
        $enhanced['from_cache'] = $fromCache;
        $enhanced['request_timestamp'] = now()->toISOString();

        // Add request context if provided
        if ($settingsData->metadata) {
            $enhanced['request_metadata'] = $settingsData->metadata;
        }

        // Add performance metrics
        $enhanced['performance'] = [
            'cache_hit' => $fromCache,
            'retrieval_method' => $fromCache ? 'cache' : 'database',
            'specific_keys_requested' => !empty($settingsData->settingsKeys),
            'keys_count' => count($settingsData->settingsKeys ?? []),
        ];

        return $enhanced;
    }

    /**
     * Log cache hit
     */
    protected function logCacheHit(ModelSettingsData $settingsData, string $cacheKey): void
    {
        Log::debug('Settings cache hit', [
            'model_type' => $settingsData->modelType,
            'model_id' => $settingsData->modelId,
            'cache_key' => $cacheKey,
            'specific_keys' => $settingsData->settingsKeys,
        ]);
    }

    /**
     * Log cache store
     */
    protected function logCacheStore(ModelSettingsData $settingsData, string $cacheKey): void
    {
        Log::debug('Settings cached', [
            'model_type' => $settingsData->modelType,
            'model_id' => $settingsData->modelId,
            'cache_key' => $cacheKey,
            'cache_duration' => $settingsData->cacheDuration,
        ]);
    }

    // Helper methods for metadata calculation

    protected function calculateProfileCompletion(Model $user, array $settings): int
    {
        // Implement profile completion calculation logic
        return 0;
    }

    protected function calculatePrivacyLevel(array $settings): string
    {
        // Implement privacy level calculation
        return 'medium';
    }

    protected function hasNotificationPreferences(array $settings): bool
    {
        return isset($settings['notifications']) && !empty($settings['notifications']);
    }

    protected function hasBrandingConfiguration(array $settings): bool
    {
        return isset($settings['branding']) && !empty($settings['branding']);
    }

    protected function hasRecruitmentPreferences(array $settings): bool
    {
        return isset($settings['recruitment']) && !empty($settings['recruitment']);
    }

    protected function countFeatureFlags(array $settings): int
    {
        return isset($settings['features']) ? count($settings['features']) : 0;
    }

    protected function isSeoOptimized(array $settings): bool
    {
        return isset($settings['seo']) && !empty($settings['seo']);
    }

    protected function hasWorkflowConfiguration(array $settings): bool
    {
        return isset($settings['workflow']) && !empty($settings['workflow']);
    }

    protected function hasAnalyticsEnabled(array $settings): bool
    {
        return isset($settings['analytics']['enabled']) && $settings['analytics']['enabled'];
    }

    protected function hasJobPreferences(array $settings): bool
    {
        return isset($settings['job_preferences']) && !empty($settings['job_preferences']);
    }

    protected function hasPrivacySettings(array $settings): bool
    {
        return isset($settings['privacy']) && !empty($settings['privacy']);
    }

    protected function getProfileVisibility(array $settings): string
    {
        return $settings['privacy']['visibility'] ?? 'private';
    }

    /**
     * Helper method to flatten array with dot notation
     */
    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $flattened = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value) && !empty($value)) {
                $flattened = array_merge($flattened, $this->flattenArray($value, $newKey));
            } else {
                $flattened[$newKey] = $value;
            }
        }

        return $flattened;
    }

    /**
     * Helper method to set nested array value
     */
    protected function setNestedArrayValue(array &$array, string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $keyPart) {
            if (!isset($current[$keyPart]) || !is_array($current[$keyPart])) {
                $current[$keyPart] = [];
            }
            $current = &$current[$keyPart];
        }

        $current = $value;
    }
} 