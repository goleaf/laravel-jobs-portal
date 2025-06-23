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
    public function getModelSettings(string $model, $id): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $settings = $instance->settings()->all();
            
            return response()->json([
                'success' => true,
                'model' => $model,
                'id' => (int) $id,
                'settings' => $settings,
                'default_settings' => $instance->defaultSettings ?? [],
                'data' => [
                    'user_id' => (int) $id,
                    'model' => $model,
                    'id' => (int) $id,
                    'settings' => $settings,
                    'default_settings' => $instance->defaultSettings ?? [],
                    'has_settings' => !empty($settings) || !empty($instance->defaultSettings ?? []),
                    'is_empty' => empty($settings) && empty($instance->defaultSettings ?? []),
                ]
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
    public function updateModelSettings(Request $request, string $model, $id): JsonResponse
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
                'id' => (int) $id,
                'updated_settings' => $settings,
                'data' => [
                    'model' => $model,
                    'id' => (int) $id,
                    'updated_settings' => $settings,
                ]
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
    public function getSpecificSetting(string $model, $id, string $key): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $value = $instance->settings()->get($key);
            
            return response()->json([
                'success' => true,
                'model' => $model,
                'id' => (int) $id,
                'key' => $key,
                'value' => $value,
                'data' => [
                    'user_id' => (int) $id,
                    'model' => $model,
                    'id' => (int) $id,
                    'key' => $key,
                    'value' => $value,
                    'has_setting' => $instance->settings()->has($key),
                ]
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
    public function setSpecificSetting(Request $request, string $model, $id, string $key): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $value = $request->input('value');
            
            // Validate specific setting if rules are defined
            if (property_exists($instance, 'settingsRules') && isset($instance->settingsRules[$key])) {
                // Convert dot notation key to nested array for Laravel validator
                $validationData = [];
                $validationRules = [];
                
                // Set nested value using dot notation
                data_set($validationData, $key, $value);
                $validationRules[$key] = $instance->settingsRules[$key];
                
                $validator = Validator::make($validationData, $validationRules);
                
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
                'id' => (int) $id,
                'key' => $key,
                'value' => $value,
                'data' => [
                    'model' => $model,
                    'id' => (int) $id,
                    'key' => $key,
                    'value' => $value,
                ]
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
    public function deleteSpecificSetting(string $model, $id, string $key): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            // Use delete method instead of forget
            $instance->settings()->delete($key);
            
            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully',
                'model' => $model,
                'id' => (int) $id,
                'key' => $key,
                'data' => [
                    'model' => $model,
                    'id' => (int) $id,
                    'key' => $key,
                ]
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
    public function clearModelSettings(string $model, $id): JsonResponse
    {
        try {
            $modelClass = $this->getModelClass($model);
            $instance = $modelClass::findOrFail($id);
            
            $instance->settings()->clear();
            
            return response()->json([
                'success' => true,
                'message' => 'All settings cleared successfully',
                'model' => $model,
                'id' => (int) $id,
                'data' => [
                    'model' => $model,
                    'id' => (int) $id,
                ]
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
                'data' => [
                    'model' => $model,
                    'default_settings' => $instance->defaultSettings ?? [],
                    'validation_rules' => $instance->settingsRules ?? [],
                ]
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
     * Demonstration endpoint showcasing Laravel Model Settings functionality.
     */
    public function demo(): JsonResponse
    {
        try {
            // Create or find a user for demonstration
            $user = User::first();
            if (!$user) {
                $user = User::factory()->create();
            }
            
            // Demonstrate settings functionality
            $demonstration = [
                'basic_operations' => [
                    'get_setting' => $user->settings()->get('profile.theme', 'light'),
                    'set_setting' => $user->settings()->set('profile.theme', 'dark'),
                    'has_setting' => $user->settings()->has('profile.theme'),
                ],
                'advanced_features' => [
                    'default_settings' => $user->defaultSettings ?? [],
                    'validation_rules' => $user->settingsRules ?? [],
                    'settings_structure' => 'Nested JSON configuration',
                ]
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Laravel Model Settings demonstration complete',
                'data' => [
                    'user_id' => $user->id,
                    'demonstration' => $demonstration,
                    'package_info' => [
                        'name' => 'glorand/laravel-model-settings',
                        'version' => '8.0.1',
                        'features' => [
                            'Field-based settings (JSON columns)',
                            'Table-based settings (separate table)',
                            'Default settings configuration',
                            'Validation rules enforcement',
                            'Nested settings structure',
                            'Cache integration',
                            'Multiple models support'
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Demo failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get settings schema for the system.
     */
    public function getSchema(): JsonResponse
    {
        try {
            $userInstance = new User();
            $companyInstance = new Company();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user_settings' => [
                        'default_settings' => $userInstance->defaultSettings ?? [],
                        'validation_rules' => $userInstance->settingsRules ?? [],
                    ],
                    'company_settings' => [
                        'default_settings' => $companyInstance->defaultSettings ?? [],
                        'validation_rules' => $companyInstance->settingsRules ?? [],
                    ],
                    'package_config' => [
                        'name' => 'glorand/laravel-model-settings',
                        'version' => '8.0.1',
                        'documentation' => 'https://github.com/glorand/laravel-model-settings'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Schema retrieval failed: ' . $e->getMessage(),
            ], 500);
        }
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
    public function getUserSettings($userId): JsonResponse
    {
        return $this->getModelSettings('users', $userId);
    }
    
    public function updateUserSettings(Request $request, $userId): JsonResponse
    {
        return $this->updateModelSettings($request, 'users', $userId);
    }
    
    public function getCompanySettings($companyId): JsonResponse
    {
        return $this->getModelSettings('companies', $companyId);
    }
    
    public function updateCompanySettings(Request $request, $companyId): JsonResponse
    {
        return $this->updateModelSettings($request, 'companies', $companyId);
    }
}
