<?php

namespace App\Actions\SettingsManagement;

use App\Data\SettingsManagement\ModelSettingsData;
use App\Data\SettingsManagement\SettingsUpdateData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use LumoSolutions\Actionable\Traits\IsRunnable;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use App\Actions\SettingsManagement\CreateSettingsVersion;

/**
 * Update Model Settings Action
 * 
 * Comprehensive action for updating any model's settings using Laravel Model Settings
 * with validation, caching, audit trails, and change tracking.
 */
class UpdateModelSettings
{
    use IsRunnable, IsDispatchable;

    /**
     * Execute the settings update action
     */
    public function handle(SettingsUpdateData $updateData): array
    {
        try {
            // Validate the update data
            $this->validateUpdateData($updateData);

            // Get the model instance
            $model = $this->getModelInstance($updateData->modelType, $updateData->modelId);

            // Backup current settings if enabled
            $backup = null;
            if ($updateData->backupEnabled) {
                $backup = $this->backupCurrentSettings($model, $updateData);
            }

            // Validate new settings if enabled
            if ($updateData->validationEnabled) {
                $this->validateSettings($model, $updateData->newSettings, $updateData->validationContext);
            }

            // Apply the settings update
            $result = $this->applySettingsUpdate($model, $updateData);

            // Clear relevant caches
            $this->clearSettingsCache($model, $updateData);

            // Log the update
            $this->logSettingsUpdate($updateData, $result, $backup);

            // Trigger post-update actions
            $this->triggerPostUpdateActions($model, $updateData, $result);

            return [
                'success' => true,
                'model_type' => $updateData->modelType,
                'model_id' => $updateData->modelId,
                'updated_keys' => $updateData->changedKeys ?? [],
                'update_summary' => $updateData->getChangeSummary(),
                'impact_analysis' => $updateData->changeAnalysis,
                'backup_id' => $backup['id'] ?? null,
                'timestamp' => $updateData->updatedAt->format('Y-m-d H:i:s'),
                'user_id' => $updateData->userId,
            ];

        } catch (\Exception $e) {
            Log::error('Settings update failed', [
                'model_type' => $updateData->modelType,
                'model_id' => $updateData->modelId,
                'error' => $e->getMessage(),
                'user_id' => $updateData->userId,
            ]);

            throw $e;
        }
    }

    /**
     * Validate the update data structure
     */
    protected function validateUpdateData(SettingsUpdateData $updateData): void
    {
        $validator = Validator::make([
            'model_type' => $updateData->modelType,
            'model_id' => $updateData->modelId,
            'new_settings' => $updateData->newSettings,
            'update_strategy' => $updateData->updateStrategy,
        ], [
            'model_type' => 'required|string',
            'model_id' => 'required',
            'new_settings' => 'required|array',
            'update_strategy' => 'required|in:merge,replace,append,remove',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException('Invalid update data: ' . $validator->errors()->first());
        }

        if (!class_exists($updateData->modelType)) {
            throw new \InvalidArgumentException("Model class {$updateData->modelType} does not exist");
        }
    }

    /**
     * Get model instance
     */
    protected function getModelInstance(string $modelType, int|string $modelId): Model
    {
        $model = $modelType::find($modelId);

        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Model {$modelType} with ID {$modelId} not found");
        }

        if (!method_exists($model, 'settings')) {
            throw new \InvalidArgumentException("Model {$modelType} does not have settings capability");
        }

        return $model;
    }

    /**
     * Backup current settings
     */
    protected function backupCurrentSettings(Model $model, SettingsUpdateData $updateData): array
    {
        $currentSettings = $model->settings()->all();
        
        $backup = [
            'id' => uniqid('backup_'),
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'settings' => $currentSettings,
            'created_at' => now(),
            'user_id' => $updateData->userId,
            'reason' => 'Pre-update backup',
        ];

        // Store backup in cache for 24 hours
        Cache::put("settings_backup:{$backup['id']}", $backup, now()->addHours(24));

        return $backup;
    }

    /**
     * Validate new settings against model rules
     */
    protected function validateSettings(Model $model, array $settings, ?array $context = null): void
    {
        // Check if model has validation rules
        if (property_exists($model, 'settingsRules')) {
            $rules = $model->settingsRules;
            
            // Apply context-specific rules if provided
            if ($context && isset($context['additional_rules'])) {
                $rules = array_merge($rules, $context['additional_rules']);
            }

            $validator = Validator::make($this->flattenArray($settings), $rules);

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        }

        // Custom validation for specific models
        $this->performCustomValidation($model, $settings, $context);
    }

    /**
     * Apply the settings update using the specified strategy
     */
    protected function applySettingsUpdate(Model $model, SettingsUpdateData $updateData): array
    {
        DB::beginTransaction();

        try {
            $result = match ($updateData->updateStrategy) {
                'merge' => $this->mergeSettings($model, $updateData),
                'replace' => $this->replaceSettings($model, $updateData),
                'append' => $this->appendSettings($model, $updateData),
                'remove' => $this->removeSettings($model, $updateData),
                default => throw new \InvalidArgumentException("Unknown update strategy: {$updateData->updateStrategy}")
            };

            DB::commit();
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Merge new settings with existing ones
     */
    protected function mergeSettings(Model $model, SettingsUpdateData $updateData): array
    {
        $updatedKeys = [];

        foreach ($updateData->newSettings as $key => $value) {
            $model->settings()->set($key, $value);
            $updatedKeys[] = $key;
        }

        return ['strategy' => 'merge', 'updated_keys' => $updatedKeys];
    }

    /**
     * Replace all settings with new ones
     */
    protected function replaceSettings(Model $model, SettingsUpdateData $updateData): array
    {
        // Clear all existing settings
        $model->settings()->clear();

        // Set new settings
        foreach ($updateData->newSettings as $key => $value) {
            $model->settings()->set($key, $value);
        }

        return [
            'strategy' => 'replace', 
            'updated_keys' => array_keys($updateData->newSettings),
            'action' => 'full_replacement'
        ];
    }

    /**
     * Append to array settings
     */
    protected function appendSettings(Model $model, SettingsUpdateData $updateData): array
    {
        $updatedKeys = [];

        foreach ($updateData->newSettings as $key => $value) {
            $currentValue = $model->settings()->get($key, []);
            
            if (is_array($currentValue) && is_array($value)) {
                $newValue = array_merge($currentValue, $value);
                $model->settings()->set($key, $newValue);
                $updatedKeys[] = $key;
            } else {
                $model->settings()->set($key, $value);
                $updatedKeys[] = $key;
            }
        }

        return ['strategy' => 'append', 'updated_keys' => $updatedKeys];
    }

    /**
     * Remove specified settings
     */
    protected function removeSettings(Model $model, SettingsUpdateData $updateData): array
    {
        $removedKeys = [];

        foreach ($updateData->changedKeys ?? [] as $key) {
            if ($model->settings()->has($key)) {
                $model->settings()->forget($key);
                $removedKeys[] = $key;
            }
        }

        return ['strategy' => 'remove', 'removed_keys' => $removedKeys];
    }

    /**
     * Clear relevant caches
     */
    protected function clearSettingsCache(Model $model, SettingsUpdateData $updateData): void
    {
        $cacheKeys = [
            "model_settings:" . get_class($model) . ":" . $model->getKey(),
            "settings_cache:" . get_class($model) . ":" . $model->getKey(),
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear cache tags if available
        if (method_exists(Cache::class, 'tags')) {
            Cache::tags([
                'model_settings',
                'model_' . $model->getKey(),
                class_basename($model) . '_settings'
            ])->flush();
        }
    }

    /**
     * Log the settings update
     */
    protected function logSettingsUpdate(SettingsUpdateData $updateData, array $result, ?array $backup): void
    {
        Log::info('Model settings updated', [
            'model_type' => $updateData->modelType,
            'model_id' => $updateData->modelId,
            'user_id' => $updateData->userId,
            'update_strategy' => $updateData->updateStrategy,
            'changed_keys' => $updateData->changedKeys,
            'update_reason' => $updateData->updateReason,
            'source' => $updateData->source,
            'impact_analysis' => $updateData->changeAnalysis,
            'backup_id' => $backup['id'] ?? null,
            'result' => $result,
        ]);
    }

    /**
     * Trigger post-update actions
     */
    protected function triggerPostUpdateActions(Model $model, SettingsUpdateData $updateData, array $result): void
    {
        // Fire model events
        event('model.settings.updated', compact('model', 'updateData', 'result'));

        // Trigger notifications if this affects user-facing features
        if ($updateData->isSecuritySensitive()) {
            $this->triggerSecurityNotification($model, $updateData);
        }

        // Update search indices if needed
        if ($this->affectsSearchability($updateData)) {
            $this->updateSearchIndex($model, $updateData);
        }

        // Clear related model caches
        $this->clearRelatedCaches($model, $updateData);
    }

    /**
     * Perform custom validation for specific models
     */
    protected function performCustomValidation(Model $model, array $settings, ?array $context): void
    {
        $modelClass = get_class($model);

        // Add custom validation logic for specific models
        switch ($modelClass) {
            case 'App\Models\User':
                $this->validateUserSettings($settings, $context);
                break;
            case 'App\Models\Company':
                $this->validateCompanySettings($settings, $context);
                break;
            case 'App\Models\Job':
                $this->validateJobSettings($settings, $context);
                break;
        }
    }

    /**
     * Validate user-specific settings
     */
    protected function validateUserSettings(array $settings, ?array $context): void
    {
        // Add user-specific validation logic
        if (isset($settings['privacy']['public_profile']) && $settings['privacy']['public_profile']) {
            if (!isset($settings['profile']['completed']) || !$settings['profile']['completed']) {
                throw new \InvalidArgumentException('Public profile requires completed profile information');
            }
        }
    }

    /**
     * Validate company-specific settings
     */
    protected function validateCompanySettings(array $settings, ?array $context): void
    {
        // Add company-specific validation logic
    }

    /**
     * Validate job-specific settings
     */
    protected function validateJobSettings(array $settings, ?array $context): void
    {
        // Add job-specific validation logic
    }

    /**
     * Check if update affects searchability
     */
    protected function affectsSearchability(SettingsUpdateData $updateData): bool
    {
        $searchablePatterns = ['display.', 'seo.', 'visibility', 'public'];
        
        foreach ($updateData->changedKeys ?? [] as $key) {
            foreach ($searchablePatterns as $pattern) {
                if (str_contains($key, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Update search index
     */
    protected function updateSearchIndex(Model $model, SettingsUpdateData $updateData): void
    {
        // Implement search index update logic
        // This could integrate with Elasticsearch, Algolia, etc.
    }

    /**
     * Clear related model caches
     */
    protected function clearRelatedCaches(Model $model, SettingsUpdateData $updateData): void
    {
        // Clear caches for related models that might be affected
    }

    /**
     * Trigger security notification
     */
    protected function triggerSecurityNotification(Model $model, SettingsUpdateData $updateData): void
    {
        // Implement security notification logic
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
     * Update model settings with comprehensive validation, caching, and versioning
     */
    public function handle(
        Model $model,
        array $newSettings,
        string $strategy = 'merge',
        ?int $userId = null,
        ?string $auditReason = null,
        bool $skipValidation = false,
        bool $createBackup = true,
        bool $createVersion = true,  // NEW: Control version creation
        ?int $cacheDuration = null
    ): SettingsUpdateData {
        // ... existing validation and preparation code ...

        try {
            DB::beginTransaction();

            // Get current settings for comparison and versioning
            $currentSettings = $model->getAllSettings();
            
            // Create settings version BEFORE updating (if enabled)
            $version = null;
            if ($createVersion) {
                try {
                    $version = CreateSettingsVersion::fromModel(
                        model: $model,
                        newSettings: $newSettings,
                        previousSettings: $currentSettings,
                        changeType: 'update',
                        userId: $userId,
                        changeReason: $auditReason ?? 'Settings updated via API'
                    );

                    Log::info('Settings version created during update', [
                        'model_type' => get_class($model),
                        'model_id' => $model->getKey(),
                        'version_id' => $version->version_id,
                        'version_number' => $version->version_number,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to create settings version during update', [
                        'model_type' => get_class($model),
                        'model_id' => $model->getKey(),
                        'error' => $e->getMessage(),
                    ]);
                    
                    // Continue with update even if versioning fails
                }
            }

            // Apply settings based on strategy
            switch ($strategy) {
                case 'replace':
                    foreach ($newSettings as $category => $settings) {
                        $model->setSetting($category, $settings);
                    }
                    break;

                case 'merge':
                default:
                    foreach ($newSettings as $category => $settings) {
                        $existing = $model->getSetting($category, []);
                        $merged = array_merge($existing, $settings);
                        $model->setSetting($category, $merged);
                    }
                    break;

                case 'deep_merge':
                    foreach ($newSettings as $category => $settings) {
                        $existing = $model->getSetting($category, []);
                        $merged = $this->deepMergeArrays($existing, $settings);
                        $model->setSetting($category, $merged);
                    }
                    break;
            }

            // Clear related caches
            $this->clearRelatedCaches($model);

            // Create update data response
            $updateData = SettingsUpdateData::forRetrieval(
                model: $model,
                previousSettings: $currentSettings,
                newSettings: $model->getAllSettings(),
                strategy: $strategy,
                userId: $userId,
                auditReason: $auditReason,
                performance: $performance,
                cacheDuration: $cacheDuration ?? config('settings.cache.ttl', 3600)
            );

            // Add version information to response
            if ($version) {
                $updateData = $updateData->withVersion([
                    'version_id' => $version->version_id,
                    'version_number' => $version->version_number,
                    'created_at' => $version->created_at,
                    'change_summary' => $version->change_summary,
                ]);
            }

            DB::commit();

            // Log successful update
            Log::info('Settings updated successfully', [
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'strategy' => $strategy,
                'user_id' => $userId,
                'changes_count' => count($updateData->getChangedKeys()),
                'version_created' => $version !== null,
                'execution_time' => $performance['execution_time_ms'] . 'ms',
            ]);

            // Fire events
            event('settings.updated', [
                'model' => $model,
                'update_data' => $updateData,
                'version' => $version,
            ]);

            return $updateData;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update settings', [
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            throw $e;
        }
    }

    /**
     * Update settings without creating a version (for internal operations)
     */
    public static function updateWithoutVersioning(
        Model $model,
        array $newSettings,
        string $strategy = 'merge',
        ?int $userId = null,
        ?string $auditReason = null
    ): SettingsUpdateData {
        return self::run(
            model: $model,
            newSettings: $newSettings,
            strategy: $strategy,
            userId: $userId,
            auditReason: $auditReason,
            createVersion: false
        );
    }
} 