<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Job;
use App\Models\UserSettings;
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
     * Get user settings with defaults.
     * 
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function getUserSettings(Request $request, int $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            
            // Get all settings with defaults applied
            $settings = $user->settings()->all();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user_id' => $user->id,
                    'settings' => $settings,
                    'has_settings' => $user->settings()->exist(),
                    'is_empty' => $user->settings()->empty(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user settings with validation.
     * 
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function updateUserSettings(Request $request, int $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            $settings = $request->input('settings', []);
            
            // Apply settings with automatic validation
            $user->settings()->apply($settings);
            
            return response()->json([
                'success' => true,
                'message' => 'User settings updated successfully',
                'data' => [
                    'user_id' => $user->id,
                    'updated_settings' => $user->settings()->all()
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific setting value.
     * 
     * @param Request $request
     * @param int $userId
     * @param string $key
     * @return JsonResponse
     */
    public function getUserSetting(Request $request, int $userId, string $key): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            $default = $request->input('default');
            
            $value = $user->settings()->get($key, $default);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user_id' => $user->id,
                    'key' => $key,
                    'value' => $value,
                    'has_setting' => $user->settings()->has($key)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set specific setting value.
     * 
     * @param Request $request
     * @param int $userId
     * @param string $key
     * @return JsonResponse
     */
    public function setUserSetting(Request $request, int $userId, string $key): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            $value = $request->input('value');
            
            $user->settings()->set($key, $value);
            
            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully',
                'data' => [
                    'user_id' => $user->id,
                    'key' => $key,
                    'value' => $value
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete specific setting.
     * 
     * @param Request $request
     * @param int $userId
     * @param string $key
     * @return JsonResponse
     */
    public function deleteUserSetting(Request $request, int $userId, string $key): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            
            $user->settings()->delete($key);
            
            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully',
                'data' => [
                    'user_id' => $user->id,
                    'deleted_key' => $key
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all user settings.
     * 
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function clearUserSettings(Request $request, int $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            
            $user->settings()->clear();
            
            return response()->json([
                'success' => true,
                'message' => 'All user settings cleared successfully',
                'data' => [
                    'user_id' => $user->id,
                    'settings' => $user->settings()->all() // Should show defaults
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company settings.
     * 
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     */
    public function getCompanySettings(Request $request, int $companyId): JsonResponse
    {
        try {
            $company = Company::findOrFail($companyId);
            
            $settings = $company->settings()->all();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                    'settings' => $settings,
                    'has_settings' => $company->settings()->exist(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve company settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update company settings.
     * 
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     */
    public function updateCompanySettings(Request $request, int $companyId): JsonResponse
    {
        try {
            $company = Company::findOrFail($companyId);
            $settings = $request->input('settings', []);
            
            $company->settings()->apply($settings);
            
            return response()->json([
                'success' => true,
                'message' => 'Company settings updated successfully',
                'data' => [
                    'company_id' => $company->id,
                    'updated_settings' => $company->settings()->all()
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update company settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Demonstrate multiple settings operations.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function demonstrateFeatures(Request $request): JsonResponse
    {
        try {
            // Create a test user if needed
            $user = User::first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users found for demonstration'
                ], 404);
            }

            $demo = [];

            // 1. Check if settings exist
            $demo['settings_exist'] = $user->settings()->exist();
            $demo['settings_empty'] = $user->settings()->empty();

            // 2. Get all settings (with defaults)
            $demo['all_settings'] = $user->settings()->all();

            // 3. Get specific setting with default
            $demo['theme'] = $user->settings()->get('profile.theme', 'light');
            $demo['language'] = $user->settings()->get('profile.language', 'en');

            // 4. Get multiple settings
            $demo['multiple_settings'] = $user->settings()->getMultiple([
                'profile.theme',
                'profile.language',
                'job_preferences.job_alerts'
            ], 'default_value');

            // 5. Check if specific setting exists
            $demo['has_theme'] = $user->settings()->has('profile.theme');
            $demo['has_custom_setting'] = $user->settings()->has('custom.non_existent');

            // 6. Set some test settings
            $user->settings()->set('demo.test_setting', 'test_value');
            $user->settings()->setMultiple([
                'demo.setting1' => 'value1',
                'demo.setting2' => 'value2'
            ]);

            // 7. Get updated settings
            $demo['updated_settings'] = $user->settings()->get('demo');

            return response()->json([
                'success' => true,
                'message' => 'Laravel Model Settings demonstration completed',
                'data' => [
                    'user_id' => $user->id,
                    'demonstration' => $demo,
                    'package_info' => [
                        'name' => 'glorand/laravel-model-settings',
                        'version' => '8.0.1',
                        'features' => [
                            'Field-based settings (JSON column)',
                            'Table-based settings (separate table)',
                            'Redis-based settings',
                            'Default settings configuration',
                            'Validation rules enforcement',
                            'Nested settings structure',
                            'Multiple get/set operations',
                            'Settings persistence control'
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Demonstration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get settings schema/structure.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getSettingsSchema(Request $request): JsonResponse
    {
        try {
            $userSettings = new UserSettings();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user_settings' => [
                        'default_settings' => $userSettings->defaultSettings,
                        'validation_rules' => $userSettings->settingsRules,
                    ],
                    'package_config' => [
                        'settings_field_name' => config('model_settings.settings_field_name'),
                        'settings_table_name' => config('model_settings.settings_table_name'),
                        'settings_persistent' => config('model_settings.settings_persistent'),
                        'settings_table_use_cache' => config('model_settings.settings_table_use_cache'),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings schema',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
