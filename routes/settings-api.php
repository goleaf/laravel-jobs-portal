<?php

use App\Http\Controllers\Api\SettingsManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Settings Management API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['throttle:api'])->prefix('api/settings')->group(function () {
    Route::get('/', [SettingsManagementController::class, 'getAvailableModels']);
    Route::get('/docs', [SettingsManagementController::class, 'getApiDocumentation']);
    Route::get('/{modelType}/schema', [SettingsManagementController::class, 'getModelSchema']);
    Route::get('/{modelType}/{modelId}', [SettingsManagementController::class, 'getSettings']);
    Route::put('/{modelType}/{modelId}', [SettingsManagementController::class, 'updateSettings']);
    Route::post('/{modelType}/bulk', [SettingsManagementController::class, 'bulkOperations']);
    
    // Version History Routes
    Route::get('/{modelType}/{modelId}/history', [SettingsManagementController::class, 'getVersionHistory']);
    Route::get('/{modelType}/{modelId}/version/{versionId}', [SettingsManagementController::class, 'getVersion']);
    Route::post('/{modelType}/{modelId}/rollback/{versionId}', [SettingsManagementController::class, 'rollbackToVersion']);
    Route::post('/{modelType}/{modelId}/compare', [SettingsManagementController::class, 'compareVersions']);
}); 