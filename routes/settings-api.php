<?php

use App\Http\Controllers\Api\SettingsManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Settings Management API Routes
|--------------------------------------------------------------------------
|
| Comprehensive API routes for managing model settings across all models
| using Actionable architecture. Supports individual and bulk operations,
| caching, validation, and audit trails.
|
| Base URL: /api/v1/settings
|
*/

// Settings Management API Routes with proper middleware
Route::group([
    'prefix' => 'settings',
    'middleware' => ['auth:sanctum', 'throttle:api'],
], function () {

    // Get available models that support settings
    Route::get('/', [SettingsManagementController::class, 'getAvailableModels'])
        ->name('api.settings.models');

    // Model-specific routes
    Route::group([
        'prefix' => '{modelType}',
        'where' => ['modelType' => '[a-z\-]+'],
    ], function () {

        // Get settings schema for a model type
        Route::get('schema', [SettingsManagementController::class, 'getSettingsSchema'])
            ->name('api.settings.schema');

        // Bulk operations for model type
        Route::post('bulk', [SettingsManagementController::class, 'bulkUpdateSettings'])
            ->middleware('throttle:bulk-settings,10,1') // 10 requests per minute for bulk operations
            ->name('api.settings.bulk-update');

        // Individual model instance routes
        Route::group([
            'prefix' => '{modelId}',
            'where' => ['modelId' => '[0-9]+'],
        ], function () {

            // Get settings for specific model instance
            Route::get('/', [SettingsManagementController::class, 'getSettings'])
                ->name('api.settings.get');

            // Update settings for specific model instance
            Route::put('/', [SettingsManagementController::class, 'updateSettings'])
                ->name('api.settings.update');

            Route::patch('/', [SettingsManagementController::class, 'updateSettings'])
                ->name('api.settings.patch');

        });
    });
});

/*
|--------------------------------------------------------------------------
| Public Settings API Routes (No Authentication Required)
|--------------------------------------------------------------------------
|
| Limited public endpoints for accessing non-sensitive settings information
| such as model schemas and available settings categories.
|
*/

Route::group([
    'prefix' => 'public/settings',
    'middleware' => ['throttle:public-api'],
], function () {

    // Get available model types (public endpoint for API documentation)
    Route::get('models', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'supported_models' => [
                    'user' => 'User profile and account settings',
                    'candidate' => 'Job seeker profile and preferences',
                    'candidate-education' => 'Education history settings',
                    'candidate-experience' => 'Work experience settings',
                    'company' => 'Company profile and recruitment settings',
                    'job' => 'Job posting and workflow settings',
                    'job-category' => 'Job categorization settings',
                    'job-type' => 'Employment type settings',
                    'job-application' => 'Application workflow settings',
                    'job-shift' => 'Work schedule settings',
                    'skill' => 'Skill management settings',
                ],
                'api_version' => '1.0',
                'documentation_url' => url('/api/documentation'),
            ],
            'message' => 'Public API information retrieved successfully',
        ]);
    })->name('api.public.settings.info');

    // Get basic schema information (non-sensitive parts only)
    Route::get('{modelType}/schema/public', function (Request $request, string $modelType) {
        $publicSchemas = [
            'user' => [
                'categories' => ['profile', 'preferences', 'privacy'],
                'description' => 'User account and profile settings',
            ],
            'candidate' => [
                'categories' => ['profile', 'job_preferences', 'privacy', 'notifications'],
                'description' => 'Job seeker profile and search preferences',
            ],
            'company' => [
                'categories' => ['profile', 'branding', 'recruitment', 'notifications'],
                'description' => 'Company profile and recruitment settings',
            ],
            'job' => [
                'categories' => ['display', 'workflow', 'seo', 'analytics'],
                'description' => 'Job posting and management settings',
            ],
        ];

        if (!isset($publicSchemas[$modelType])) {
            return response()->json([
                'success' => false,
                'message' => 'Model type not found or not publicly accessible',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'model_type' => $modelType,
                'schema' => $publicSchemas[$modelType],
                'api_endpoints' => [
                    'get_settings' => "GET /api/v1/settings/{$modelType}/{id}",
                    'update_settings' => "PUT /api/v1/settings/{$modelType}/{id}",
                    'bulk_update' => "POST /api/v1/settings/{$modelType}/bulk",
                    'schema' => "GET /api/v1/settings/{$modelType}/schema",
                ],
            ],
            'message' => 'Public schema information retrieved successfully',
        ]);
    })->name('api.public.settings.schema');
});

/*
|--------------------------------------------------------------------------
| Settings API Documentation Routes
|--------------------------------------------------------------------------
|
| Endpoints for accessing API documentation and examples
|
*/

Route::group([
    'prefix' => 'settings/docs',
    'middleware' => ['throttle:api'],
], function () {

    // API documentation endpoint
    Route::get('/', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'api_version' => '1.0',
                'title' => 'Settings Management API',
                'description' => 'Comprehensive API for managing model settings using Actionable architecture',
                'base_url' => url('/api/v1/settings'),
                'authentication' => 'Bearer token required (Laravel Sanctum)',
                'rate_limits' => [
                    'standard' => '60 requests per minute',
                    'bulk_operations' => '10 requests per minute',
                    'public_endpoints' => '100 requests per minute',
                ],
                'endpoints' => [
                    'GET /' => 'Get available models',
                    'GET /{modelType}/schema' => 'Get settings schema for model',
                    'GET /{modelType}/{id}' => 'Get settings for model instance',
                    'PUT /{modelType}/{id}' => 'Update settings for model instance',
                    'POST /{modelType}/bulk' => 'Bulk update settings for multiple instances',
                ],
                'supported_models' => [
                    'user', 'candidate', 'candidate-education', 'candidate-experience',
                    'company', 'job', 'job-category', 'job-type', 'job-application',
                    'job-shift', 'skill'
                ],
                'examples_url' => url('/api/v1/settings/docs/examples'),
            ],
            'message' => 'API documentation retrieved successfully',
        ]);
    })->name('api.settings.docs');

    // API examples endpoint
    Route::get('examples', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'get_settings' => [
                    'url' => 'GET /api/v1/settings/user/1',
                    'query_parameters' => [
                        'keys' => 'profile.theme,notifications.email (optional)',
                        'cache_duration' => '3600 (optional, seconds)',
                    ],
                    'response' => [
                        'success' => true,
                        'data' => [
                            'model_type' => 'App\\Models\\User',
                            'model_id' => 1,
                            'settings' => [
                                'profile' => ['theme' => 'dark'],
                                'notifications' => ['email' => true],
                            ],
                            'from_cache' => false,
                        ],
                    ],
                ],
                'update_settings' => [
                    'url' => 'PUT /api/v1/settings/user/1',
                    'body' => [
                        'settings' => [
                            'profile' => ['theme' => 'light'],
                            'notifications' => ['email' => false],
                        ],
                        'update_strategy' => 'merge', // merge, replace, append, remove
                        'update_reason' => 'User preference change',
                        'validation_enabled' => true,
                        'backup_enabled' => true,
                    ],
                    'response' => [
                        'success' => true,
                        'data' => [
                            'model_type' => 'App\\Models\\User',
                            'model_id' => 1,
                            'updated_keys' => ['profile.theme', 'notifications.email'],
                            'update_summary' => 'Updated 2 setting(s) for User #1',
                        ],
                    ],
                ],
                'bulk_update' => [
                    'url' => 'POST /api/v1/settings/user/bulk',
                    'body' => [
                        'model_ids' => [1, 2, 3],
                        'settings' => [
                            'notifications' => ['system_alerts' => true],
                        ],
                        'update_strategy' => 'merge',
                        'update_reason' => 'System-wide notification enable',
                    ],
                    'response' => [
                        'success' => true,
                        'data' => [
                            'summary' => [
                                'total' => 3,
                                'successful' => 3,
                                'failed' => 0,
                                'success_rate' => 100.0,
                            ],
                        ],
                    ],
                ],
            ],
            'message' => 'API examples retrieved successfully',
        ]);
    })->name('api.settings.examples');
});

/*
|--------------------------------------------------------------------------
| Rate Limiting Configuration
|--------------------------------------------------------------------------
|
| Custom rate limiters for different types of settings operations
|
*/

// Note: These rate limiters should be defined in RouteServiceProvider or a dedicated service provider

/*
RateLimiter::for('bulk-settings', function (Request $request) {
    return Limit::perMinute(10)->by(optional($request->user())->id ?: $request->ip());
});

RateLimiter::for('public-api', function (Request $request) {
    return Limit::perMinute(100)->by($request->ip());
});
*/ 