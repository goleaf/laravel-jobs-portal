<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SettingsController;

/**
 * Habr-Based Settings Management API Routes
 * 
 * Based on modern Laravel settings management patterns from Habr community
 * Provides comprehensive API for managing application settings
 */

// Public settings routes (no authentication required)
Route::prefix('settings/public')->group(function () {
    // Get all public settings
    Route::get('/', [SettingsController::class, 'public'])
        ->name('api.settings.public');
    
    // Get public settings by group
    Route::get('/{group}', [SettingsController::class, 'publicGroup'])
        ->name('api.settings.public.group')
        ->where('group', '[a-zA-Z0-9_-]+');
});

// Protected settings routes (authentication required)
Route::middleware(['auth:sanctum'])->prefix('settings')->group(function () {
    
    // Core CRUD operations
    Route::get('/', [SettingsController::class, 'index'])
        ->name('api.settings.index');
    
    Route::get('/{key}', [SettingsController::class, 'show'])
        ->name('api.settings.show')
        ->where('key', '[a-zA-Z0-9_.-]+');
    
    Route::put('/{key}', [SettingsController::class, 'update'])
        ->name('api.settings.update')
        ->where('key', '[a-zA-Z0-9_.-]+');
    
    Route::delete('/{key}', [SettingsController::class, 'destroy'])
        ->name('api.settings.destroy')
        ->where('key', '[a-zA-Z0-9_.-]+');
    
    // Bulk operations
    Route::post('/bulk-update', [SettingsController::class, 'bulkUpdate'])
        ->name('api.settings.bulk-update');
    
    // Import/Export operations
    Route::get('/export/all', [SettingsController::class, 'export'])
        ->name('api.settings.export');
    
    Route::post('/import', [SettingsController::class, 'import'])
        ->name('api.settings.import');
    
    // Utility operations
    Route::post('/{key}/reset-default', [SettingsController::class, 'resetToDefault'])
        ->name('api.settings.reset-default')
        ->where('key', '[a-zA-Z0-9_.-]+');
    
    Route::post('/cache/clear', [SettingsController::class, 'clearCache'])
        ->name('api.settings.cache.clear');
});

/**
 * API Documentation Routes
 */
Route::get('/settings/docs', function () {
    return response()->json([
        'title' => 'Habr-Based Settings API Documentation',
        'version' => '1.0.0',
        'description' => 'Comprehensive Laravel settings management API based on Habr community best practices',
        'endpoints' => [
            'public' => [
                'GET /api/settings/public' => 'Get all public settings',
                'GET /api/settings/public/{group}' => 'Get public settings by group',
            ],
            'authenticated' => [
                'GET /api/settings' => 'Get all settings (with filtering)',
                'GET /api/settings/{key}' => 'Get specific setting',
                'PUT /api/settings/{key}' => 'Update setting',
                'DELETE /api/settings/{key}' => 'Delete setting',
                'POST /api/settings/bulk-update' => 'Update multiple settings',
                'GET /api/settings/export/all' => 'Export settings',
                'POST /api/settings/import' => 'Import settings',
                'POST /api/settings/{key}/reset-default' => 'Reset setting to default',
                'POST /api/settings/cache/clear' => 'Clear settings cache',
            ]
        ],
        'parameters' => [
            'query_parameters' => [
                'group' => 'Filter settings by group (general, jobs, users, etc.)',
                'search' => 'Search settings by key or description',
            ],
            'setting_types' => ['string', 'integer', 'float', 'boolean', 'array', 'json', 'object'],
            'setting_groups' => ['general', 'jobs', 'users', 'notifications', 'seo', 'analytics', 'companies', 'system', 'api'],
        ],
        'authentication' => [
            'type' => 'Bearer Token (Sanctum)',
            'header' => 'Authorization: Bearer {token}',
            'note' => 'Required for all endpoints except public settings',
        ],
        'rate_limiting' => [
            'authenticated' => '120 requests per minute',
            'public' => '60 requests per minute',
        ],
        'examples' => [
            'get_setting' => [
                'request' => 'GET /api/settings/site_title',
                'response' => [
                    'success' => true,
                    'data' => [
                        'setting' => [
                            'key' => 'site_title',
                            'value' => 'Laravel Job Portal',
                            'type' => 'string',
                            'group' => 'general',
                            'description' => 'Main website title',
                            'is_public' => true,
                        ]
                    ]
                ]
            ],
            'update_setting' => [
                'request' => 'PUT /api/settings/site_title',
                'body' => [
                    'value' => 'New Site Title',
                    'description' => 'Updated description'
                ]
            ],
            'bulk_update' => [
                'request' => 'POST /api/settings/bulk-update',
                'body' => [
                    'settings' => [
                        'site_title' => 'New Title',
                        'jobs_per_page' => 25,
                        'email_notifications_enabled' => true
                    ]
                ]
            ]
        ],
        'habr_integration' => [
            'based_on' => 'Habr article best practices for Laravel settings management',
            'features' => [
                'Typed settings with validation',
                'Grouped organization',
                'Public/private access control',
                'Caching for performance',
                'Import/export functionality',
                'Audit trail with user tracking',
                'Default value management',
                'Bulk operations support'
            ]
        ]
    ]);
})->name('api.settings.docs'); 