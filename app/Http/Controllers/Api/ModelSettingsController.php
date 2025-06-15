<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\Skill;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * ModelSettingsController - Demonstrates Laravel Model Settings Integration
 * 
 * This controller showcases the full functionality of the glorand/laravel-model-settings
 * package with comprehensive CRUD operations for settings management.
 * 
 * Features demonstrated:
 * - Field-based settings (JSON column)
 * - Table-based settings (separate table)
 * - Default settings configuration
 * - Validation rules enforcement
 * - Nested settings structure
 * - Settings inheritance and overrides
 */
class ModelSettingsController extends Controller
{
    /**
     * Supported models for settings management.
     */
    protected array $supportedModels = [
        'users' => User::class,
        'companies' => Company::class,
        'jobs' => Job::class,
        'candidates' => Candidate::class,
        'job-categories' => JobCategory::class,
        'job-types' => JobType::class,
        'skills' => Skill::class,
        'posts' => Post::class,
        'settings' => Setting::class,
    ];

    /**
     * Get all settings for a specific model instance.
     */
    public function getModelSettings(string $model, int $id): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $settings = $instance->settings()->all();
            
            return response()->json([
                'success' => true,
                'model' => $model,
                'id' => $id,
                'settings' => $settings,
                'default_settings' => $instance->defaultSettings ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update settings for a specific model instance.
     */
    public function updateModelSettings(Request $request, string $model, int $id): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $settings = $request->input('settings', []);
            
            // Validate settings if rules are defined
            if (property_exists($instance, 'settingsRules') && !empty($instance->settingsRules)) {
                $validator = Validator::make($settings, $instance->settingsRules);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 422);
                }
            }
            
            // Update settings
            foreach ($settings as $key => $value) {
                $instance->settings()->set($key, $value);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'model' => $model,
                'id' => $id,
                'updated_settings' => $settings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific setting for a model instance.
     */
    public function getSpecificSetting(string $model, int $id, string $key): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $value = $instance->settings()->get($key);
            
            return response()->json([
                'success' => true,
                'model' => $model,
                'id' => $id,
                'key' => $key,
                'value' => $value,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve setting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set a specific setting for a model instance.
     */
    public function setSpecificSetting(Request $request, string $model, int $id, string $key): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $value = $request->input('value');
            
            // Validate specific setting if rules are defined
            if (property_exists($instance, 'settingsRules') && isset($instance->settingsRules[$key])) {
                $validator = Validator::make(
                    [$key => $value],
                    [$key => $instance->settingsRules[$key]]
                );
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 422);
                }
            }
            
            $instance->settings()->set($key, $value);
            
            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully',
                'model' => $model,
                'id' => $id,
                'key' => $key,
                'value' => $value,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update setting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific setting for a model instance.
     */
    public function deleteSpecificSetting(string $model, int $id, string $key): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $instance->settings()->forget($key);
            
            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully',
                'model' => $model,
                'id' => $id,
                'key' => $key,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete setting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear all settings for a model instance.
     */
    public function clearModelSettings(string $model, int $id): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $instance->settings()->clear();
            
            return response()->json([
                'success' => true,
                'message' => 'All settings cleared successfully',
                'model' => $model,
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get settings schema for a specific model.
     */
    public function getModelSchema(string $model): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = new $modelClass();
            
            return response()->json([
                'success' => true,
                'model' => $model,
                'default_settings' => $instance->defaultSettings ?? [],
                'validation_rules' => $instance->settingsRules ?? [],
                'supported_operations' => [
                    'get_all' => "GET /api/model-settings/{$model}/{id}",
                    'update_all' => "PUT /api/model-settings/{$model}/{id}",
                    'get_specific' => "GET /api/model-settings/{$model}/{id}/{key}",
                    'set_specific' => "PUT /api/model-settings/{$model}/{id}/{key}",
                    'delete_specific' => "DELETE /api/model-settings/{$model}/{id}/{key}",
                    'clear_all' => "DELETE /api/model-settings/{$model}/{id}",
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schema: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all supported models and their capabilities.
     */
    public function listSupportedModels(): JsonResponse
    {
        $models = [];
        
        foreach ($this->supportedModels as $key => $class) {
            $instance = new $class();
            $models[$key] = [
                'class' => $class,
                'has_default_settings' => property_exists($instance, 'defaultSettings'),
                'has_validation_rules' => property_exists($instance, 'settingsRules'),
                'settings_count' => is_array($instance->defaultSettings ?? null) 
                    ? count($instance->defaultSettings ?? []) 
                    : 0,
            ];
        }
        
        return response()->json([
            'success' => true,
            'supported_models' => $models,
            'total_models' => count($models),
        ]);
    }

    /**
     * Comprehensive demo showcasing all model settings features.
     */
    public function comprehensiveDemo(): JsonResponse
    {
        $results = [];
        
        try {
            // Demo for each supported model
            foreach ($this->supportedModels as $modelKey => $modelClass) {
                $instance = $modelClass::first();
                
                if ($instance) {
                    // Get current settings
                    $currentSettings = $instance->settings()->all();
                    
                    // Get default settings
                    $defaultSettings = $instance->defaultSettings ?? [];
                    
                    // Get validation rules
                    $validationRules = $instance->settingsRules ?? [];
                    
                    $results[$modelKey] = [
                        'id' => $instance->id,
                        'current_settings' => $currentSettings,
                        'default_settings' => $defaultSettings,
                        'validation_rules' => $validationRules,
                        'settings_categories' => array_keys($defaultSettings),
                        'total_settings' => count($defaultSettings),
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Comprehensive Laravel Model Settings demonstration',
                'models_demonstrated' => $results,
                'total_models' => count($results),
                'features_demonstrated' => [
                    'Multi-model settings support',
                    'Default settings configuration',
                    'Validation rules enforcement',
                    'Nested settings structure',
                    'Type-safe configuration',
                    'RESTful API endpoints',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Demo failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the model class for a given model key.
     */
    protected function getModelClass(string $model): string
    {
        if (!isset($this->supportedModels[$model])) {
            throw new \InvalidArgumentException("Model '{$model}' is not supported");
        }
        
        return $this->supportedModels[$model];
    }

    /**
     * Legacy methods for backward compatibility
     */
    public function getUserSettings(int $userId): JsonResponse
    {
        return $this->getModelSettings('users', $userId);
    }
    
    public function updateUserSettings(Request $request, int $userId): JsonResponse
    {
        return $this->updateModelSettings($request, 'users', $userId);
    }
    
    public function getCompanySettings(int $companyId): JsonResponse
    {
        return $this->getModelSettings('companies', $companyId);
    }
    
    public function updateCompanySettings(Request $request, int $companyId): JsonResponse
    {
        return $this->updateModelSettings($request, 'companies', $companyId);
    }
}
