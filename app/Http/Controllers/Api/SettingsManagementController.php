<?php

namespace App\Http\Controllers\Api;

use App\Actions\SettingsManagement\GetModelSettings;
use App\Actions\SettingsManagement\UpdateModelSettings;
use App\Data\SettingsManagement\ModelSettingsData;
use App\Data\SettingsManagement\SettingsUpdateData;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Actions\SettingsManagement\CreateSettingsVersion;
use App\Models\SettingsVersion;
use Illuminate\Support\Facades\Log;

/**
 * Settings Management API Controller
 * 
 * RESTful API controller for managing model settings across all models
 * using Actionable architecture for clean, testable, reusable logic.
 */
class SettingsManagementController extends Controller
{
    /**
     * Get settings for a specific model
     * 
     * @param Request $request
     * @param string $modelType
     * @param int|string $modelId
     * @return JsonResponse
     */
    public function getSettings(Request $request, string $modelType, int|string $modelId): JsonResponse
    {
        try {
            // Validate the request
            $this->validateGetRequest($request, $modelType, $modelId);

            // Get settings keys filter if provided
            $settingsKeys = $request->query('keys') ? explode(',', $request->query('keys')) : null;

            // Create settings data object with cache duration
            $settingsData = ModelSettingsData::forRetrieval(
                modelType: $this->getModelClass($modelType),
                modelId: $modelId,
                settingsKeys: $settingsKeys,
            );

            // Execute the action
            $result = GetModelSettings::run($settingsData);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Settings retrieved successfully',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update settings for a specific model
     * 
     * @param Request $request
     * @param string $modelType
     * @param int|string $modelId
     * @return JsonResponse
     */
    public function updateSettings(Request $request, string $modelType, int|string $modelId): JsonResponse
    {
        try {
            // Validate the request
            $validated = $this->validateUpdateRequest($request, $modelType, $modelId);

            // Get current settings for change tracking
            $currentSettingsData = ModelSettingsData::forRetrieval(
                modelType: $this->getModelClass($modelType),
                modelId: $modelId
            );
            $currentSettingsResult = GetModelSettings::run($currentSettingsData);
            $currentSettings = $currentSettingsResult['settings'] ?? [];

            // Create update data object
            $updateData = SettingsUpdateData::fromChanges(
                modelType: $this->getModelClass($modelType),
                modelId: $modelId,
                currentSettings: $currentSettings,
                newSettings: $validated['settings'],
                userId: auth()->id(),
                updateReason: $validated['update_reason'] ?? null
            );

            // Set additional properties
            $updateData->updateStrategy = $validated['update_strategy'] ?? 'merge';
            $updateData->source = $validated['source'] ?? 'api';
            $updateData->validationEnabled = $validated['validation_enabled'] ?? true;
            $updateData->backupEnabled = $validated['backup_enabled'] ?? true;

            // Execute the action
            $result = UpdateModelSettings::run($updateData);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Settings updated successfully',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk update settings for multiple models
     * 
     * @param Request $request
     * @param string $modelType
     * @return JsonResponse
     */
    public function bulkUpdateSettings(Request $request, string $modelType): JsonResponse
    {
        try {
            // Validate bulk update request
            $validated = $this->validateBulkUpdateRequest($request, $modelType);

            $results = [];
            $errors = [];

            foreach ($validated['model_ids'] as $modelId) {
                try {
                    // Get current settings
                    $currentSettingsData = ModelSettingsData::forRetrieval(
                        modelType: $this->getModelClass($modelType),
                        modelId: $modelId
                    );
                    $currentSettingsResult = GetModelSettings::run($currentSettingsData);
                    $currentSettings = $currentSettingsResult['settings'] ?? [];

                    // Create update data
                    $updateData = SettingsUpdateData::fromChanges(
                        modelType: $this->getModelClass($modelType),
                        modelId: $modelId,
                        currentSettings: $currentSettings,
                        newSettings: $validated['settings'],
                        userId: auth()->id(),
                        updateReason: $validated['update_reason'] ?? 'Bulk update'
                    );

                    $updateData->updateStrategy = $validated['update_strategy'] ?? 'merge';
                    $updateData->source = 'bulk_api';

                    // Execute update
                    $result = UpdateModelSettings::run($updateData);
                    $results[$modelId] = $result;

                } catch (\Exception $e) {
                    $errors[$modelId] = $e->getMessage();
                }
            }

            $successCount = count($results);
            $errorCount = count($errors);
            $totalCount = count($validated['model_ids']);

            return response()->json([
                'success' => $errorCount === 0,
                'data' => [
                    'successful_updates' => $results,
                    'failed_updates' => $errors,
                    'summary' => [
                        'total' => $totalCount,
                        'successful' => $successCount,
                        'failed' => $errorCount,
                        'success_rate' => $totalCount > 0 ? round(($successCount / $totalCount) * 100, 2) : 0,
                    ],
                ],
                'message' => "Bulk update completed: {$successCount}/{$totalCount} successful",
            ], $errorCount > 0 ? 207 : 200); // 207 Multi-Status for partial success

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk update failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get settings schema for a model type
     * 
     * @param Request $request
     * @param string $modelType
     * @return JsonResponse
     */
    public function getSettingsSchema(Request $request, string $modelType): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($modelType);
            
            if (!class_exists($modelClass)) {
                return response()->json([
                    'success' => false,
                    'message' => "Model class {$modelType} not found",
                ], 404);
            }

            // Create a temporary instance to get schema information
            $tempModel = new $modelClass();

            $schema = [
                'model_type' => $modelClass,
                'model_name' => class_basename($modelClass),
                'supports_settings' => method_exists($tempModel, 'settings'),
                'default_settings' => property_exists($tempModel, 'defaultSettings') ? 
                    $tempModel->defaultSettings : null,
                'validation_rules' => property_exists($tempModel, 'settingsRules') ? 
                    $tempModel->settingsRules : null,
                'settings_categories' => $this->getSettingsCategories($tempModel),
                'settings_documentation' => $this->getSettingsDocumentation($tempModel),
            ];

            return response()->json([
                'success' => true,
                'data' => $schema,
                'message' => 'Settings schema retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings schema: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available model types that support settings
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAvailableModels(Request $request): JsonResponse
    {
        try {
            $supportedModels = [
                'user' => 'App\Models\User',
                'candidate' => 'App\Models\Candidate',
                'candidate-education' => 'App\Models\CandidateEducation',
                'candidate-experience' => 'App\Models\CandidateExperience',
                'company' => 'App\Models\Company',
                'job' => 'App\Models\Job',
                'job-category' => 'App\Models\JobCategory',
                'job-type' => 'App\Models\JobType',
                'job-application' => 'App\Models\JobApplication',
                'job-shift' => 'App\Models\JobShift',
                'skill' => 'App\Models\Skill',
            ];

            $models = [];

            foreach ($supportedModels as $alias => $class) {
                if (class_exists($class)) {
                    $tempModel = new $class();
                    $models[$alias] = [
                        'alias' => $alias,
                        'class' => $class,
                        'name' => class_basename($class),
                        'supports_settings' => method_exists($tempModel, 'settings'),
                        'has_default_settings' => property_exists($tempModel, 'defaultSettings'),
                        'has_validation_rules' => property_exists($tempModel, 'settingsRules'),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'supported_models' => $models,
                    'total_count' => count($models),
                ],
                'message' => 'Available models retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available models: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate GET request
     */
    protected function validateGetRequest(Request $request, string $modelType, int|string $modelId): void
    {
        $validator = Validator::make([
            'model_type' => $modelType,
            'model_id' => $modelId,
            'keys' => $request->query('keys'),
            'cache_duration' => $request->query('cache_duration'),
        ], [
            'model_type' => 'required|string',
            'model_id' => 'required',
            'keys' => 'nullable|string',
            'cache_duration' => 'nullable|integer|min:0|max:86400',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate update request
     */
    protected function validateUpdateRequest(Request $request, string $modelType, int|string $modelId): array
    {
        return $request->validate([
            'settings' => 'required|array',
            'update_strategy' => 'nullable|string|in:merge,replace,append,remove',
            'update_reason' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:50',
            'validation_enabled' => 'nullable|boolean',
            'backup_enabled' => 'nullable|boolean',
        ]);
    }

    /**
     * Validate bulk update request
     */
    protected function validateBulkUpdateRequest(Request $request, string $modelType): array
    {
        return $request->validate([
            'model_ids' => 'required|array|max:100', // Limit to 100 models per request
            'model_ids.*' => 'required',
            'settings' => 'required|array',
            'update_strategy' => 'nullable|string|in:merge,replace,append',
            'update_reason' => 'nullable|string|max:255',
        ]);
    }

    /**
     * Get model class from alias
     */
    protected function getModelClass(string $modelType): string
    {
        $modelMapping = [
            'user' => 'App\Models\User',
            'candidate' => 'App\Models\Candidate',
            'candidate-education' => 'App\Models\CandidateEducation',
            'candidate-experience' => 'App\Models\CandidateExperience',
            'company' => 'App\Models\Company',
            'job' => 'App\Models\Job',
            'job-category' => 'App\Models\JobCategory',
            'job-type' => 'App\Models\JobType',
            'job-application' => 'App\Models\JobApplication',
            'job-shift' => 'App\Models\JobShift',
            'skill' => 'App\Models\Skill',
        ];

        return $modelMapping[$modelType] ?? $modelType;
    }

    /**
     * Get settings categories for a model
     */
    protected function getSettingsCategories($model): array
    {
        // Default categories based on common patterns
        $categories = [
            'display' => 'Display and formatting settings',
            'privacy' => 'Privacy and visibility settings',
            'notifications' => 'Notification preferences',
            'workflow' => 'Workflow and automation settings',
            'analytics' => 'Analytics and tracking settings',
        ];

        // Add model-specific categories
        if (property_exists($model, 'settingsCategories')) {
            $categories = array_merge($categories, $model->settingsCategories);
        }

        return $categories;
    }

    /**
     * Get settings documentation for a model
     */
    protected function getSettingsDocumentation($model): array
    {
        $documentation = [
            'description' => 'Settings for ' . class_basename($model),
            'examples' => [],
            'common_patterns' => [
                'Enable/disable features using boolean values',
                'Use arrays for multiple values (tags, categories)',
                'Use nested objects for grouped settings',
            ],
        ];

        // Add model-specific documentation
        if (property_exists($model, 'settingsDocumentation')) {
            $documentation = array_merge($documentation, $model->settingsDocumentation);
        }

        return $documentation;
    }

    /**
     * Get version history for a model instance
     */
    public function getVersionHistory(Request $request, string $modelType, string $modelId)
    {
        try {
            // Validate model type
            if (!array_key_exists($modelType, $this->modelTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid model type',
                    'error' => 'MODEL_TYPE_INVALID'
                ], 400);
            }

            $limit = min($request->get('limit', 20), 100); // Max 100 versions
            $versions = CreateSettingsVersion::getVersionHistory($this->modelTypes[$modelType], $modelId, $limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'versions' => $versions->map(function ($version) {
                        return [
                            'version_id' => $version->version_id,
                            'version_number' => $version->version_number,
                            'change_type' => $version->change_type,
                            'change_reason' => $version->change_reason,
                            'change_summary' => $version->change_summary,
                            'changed_keys' => $version->changed_keys,
                            'user_id' => $version->user_id,
                            'source' => $version->source,
                            'created_at' => $version->created_at,
                            'size_kb' => $version->getSizeInKB(),
                            'is_latest' => $version->isLatestVersion(),
                        ];
                    }),
                    'total_versions' => $versions->count(),
                ],
                'message' => 'Version history retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get version history', [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve version history',
                'error' => 'VERSION_HISTORY_ERROR'
            ], 500);
        }
    }

    /**
     * Get specific version details
     */
    public function getVersion(Request $request, string $modelType, string $modelId, string $versionId)
    {
        try {
            // Validate model type
            if (!array_key_exists($modelType, $this->modelTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid model type',
                    'error' => 'MODEL_TYPE_INVALID'
                ], 400);
            }

            $version = SettingsVersion::where('version_id', $versionId)
                                    ->forModel($this->modelTypes[$modelType], $modelId)
                                    ->active()
                                    ->first();

            if (!$version) {
                return response()->json([
                    'success' => false,
                    'message' => 'Version not found',
                    'error' => 'VERSION_NOT_FOUND'
                ], 404);
            }

            // Verify checksum
            $isIntegrityValid = $version->verifyChecksum();

            return response()->json([
                'success' => true,
                'data' => [
                    'version' => [
                        'version_id' => $version->version_id,
                        'version_number' => $version->version_number,
                        'change_type' => $version->change_type,
                        'change_reason' => $version->change_reason,
                        'change_summary' => $version->change_summary,
                        'settings_data' => $version->settings_data,
                        'changed_keys' => $version->changed_keys,
                        'user_id' => $version->user_id,
                        'source' => $version->source,
                        'created_at' => $version->created_at,
                        'size_kb' => $version->getSizeInKB(),
                        'is_latest' => $version->isLatestVersion(),
                        'integrity_valid' => $isIntegrityValid,
                    ],
                    'navigation' => [
                        'previous_version' => $version->getPreviousVersion()?->version_id,
                        'next_version' => $version->getNextVersion()?->version_id,
                    ]
                ],
                'message' => 'Version retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get version details', [
                'version_id' => $versionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve version details',
                'error' => 'VERSION_DETAILS_ERROR'
            ], 500);
        }
    }

    /**
     * Rollback to a specific version
     */
    public function rollbackToVersion(Request $request, string $modelType, string $modelId, string $versionId)
    {
        try {
            // Validate model type
            if (!array_key_exists($modelType, $this->modelTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid model type',
                    'error' => 'MODEL_TYPE_INVALID'
                ], 400);
            }

            // Get the target version
            $targetVersion = SettingsVersion::where('version_id', $versionId)
                                           ->forModel($this->modelTypes[$modelType], $modelId)
                                           ->active()
                                           ->first();

            if (!$targetVersion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Target version not found',
                    'error' => 'VERSION_NOT_FOUND'
                ], 404);
            }

            // Verify checksum before rollback
            if (!$targetVersion->verifyChecksum()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Target version has integrity issues',
                    'error' => 'VERSION_INTEGRITY_ERROR'
                ], 400);
            }

            $validated = $request->validate([
                'reason' => 'required|string|max:255',
                'confirm' => 'required|boolean|accepted',
            ]);

            // Get current settings for comparison
            $currentSettings = GetModelSettings::run($this->modelTypes[$modelType], $modelId);

            // Create rollback version
            $rollbackVersion = CreateSettingsVersion::createRollback(
                $targetVersion,
                auth()->id(),
                $validated['reason']
            );

            // Apply the rollback settings to the model
            $model = $this->modelTypes[$modelType]::findOrFail($modelId);
            UpdateModelSettings::run(
                model: $model,
                newSettings: $targetVersion->settings_data,
                strategy: 'replace',
                userId: auth()->id(),
                auditReason: "Rollback to version {$targetVersion->version_number}: {$validated['reason']}",
                skipValidation: false,
                createBackup: true
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'rollback_version' => [
                        'version_id' => $rollbackVersion->version_id,
                        'version_number' => $rollbackVersion->version_number,
                        'target_version' => $targetVersion->version_number,
                        'reason' => $validated['reason'],
                        'created_at' => $rollbackVersion->created_at,
                    ],
                    'applied_settings' => $targetVersion->settings_data,
                ],
                'message' => "Successfully rolled back to version {$targetVersion->version_number}"
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to rollback to version', [
                'version_id' => $versionId,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to rollback to version',
                'error' => 'ROLLBACK_ERROR'
            ], 500);
        }
    }

    /**
     * Compare two versions
     */
    public function compareVersions(Request $request, string $modelType, string $modelId)
    {
        try {
            // Validate model type
            if (!array_key_exists($modelType, $this->modelTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid model type',
                    'error' => 'MODEL_TYPE_INVALID'
                ], 400);
            }

            $validated = $request->validate([
                'version_1' => 'required|uuid',
                'version_2' => 'required|uuid',
            ]);

            // Get both versions
            $version1 = SettingsVersion::where('version_id', $validated['version_1'])
                                      ->forModel($this->modelTypes[$modelType], $modelId)
                                      ->active()
                                      ->first();

            $version2 = SettingsVersion::where('version_id', $validated['version_2'])
                                      ->forModel($this->modelTypes[$modelType], $modelId)
                                      ->active()
                                      ->first();

            if (!$version1 || !$version2) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or both versions not found',
                    'error' => 'VERSION_NOT_FOUND'
                ], 404);
            }

            // Compare versions
            $comparison = CreateSettingsVersion::compareVersions($version1, $version2);

            return response()->json([
                'success' => true,
                'data' => [
                    'comparison' => $comparison,
                    'version_1_info' => [
                        'version_id' => $version1->version_id,
                        'version_number' => $version1->version_number,
                        'created_at' => $version1->created_at,
                        'change_type' => $version1->change_type,
                    ],
                    'version_2_info' => [
                        'version_id' => $version2->version_id,
                        'version_number' => $version2->version_number,
                        'created_at' => $version2->created_at,
                        'change_type' => $version2->change_type,
                    ],
                ],
                'message' => 'Version comparison completed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to compare versions', [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'version_1' => $validated['version_1'] ?? null,
                'version_2' => $validated['version_2'] ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to compare versions',
                'error' => 'VERSION_COMPARISON_ERROR'
            ], 500);
        }
    }
} 