<?php

use App\Http\Controllers\Api\JobTypeController as ApiJobTypeController;
use App\Http\Controllers\Job\JobTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Job Type Routes - FIXED FOR BLADE TEMPLATES
|--------------------------------------------------------------------------
|
| This file contains all routes related to job type functionality
| including both web admin routes and API endpoints
|
*/

// Admin Web Routes (for blade templates) - ADDED TO FIX BLADE ISSUES
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Job Type Resource Routes using existing controller
    Route::resource('job-types', JobTypeController::class);

    // Additional Operations Referenced in Blades
    Route::post('job-types/bulk-action', [JobTypeController::class, 'bulkAction'])
        ->name('job-types.bulk-action')
    ;
    Route::get('job-types/statistics', [JobTypeController::class, 'statistics'])
        ->name('job-types.statistics')
    ;
    Route::get('job-types/export', [JobTypeController::class, 'export'])
        ->name('job-types.export')
    ;
});

// Public Web Routes
Route::name('job-types.')->group(function () {
    Route::get('job-types/{jobType:slug}', [JobTypeController::class, 'showPublic'])
        ->name('show')
    ;
});

/*
|--------------------------------------------------------------------------
| API Routes (Existing)
|--------------------------------------------------------------------------
*/

// API Routes
Route::prefix('api/v1')->middleware(['api', 'throttle:api'])->group(function () {
    // Public API routes (no authentication required)
    Route::get('job-types', [ApiJobTypeController::class, 'index'])
        ->name('api.job-types.index')
    ;

    Route::get('job-types/{jobType}', [ApiJobTypeController::class, 'show'])
        ->name('api.job-types.show')
    ;

    Route::get('job-types/search', [ApiJobTypeController::class, 'search'])
        ->name('api.job-types.search')
    ;

    // Protected API routes (authentication required)
    Route::middleware(['auth:sanctum'])->group(function () {
        // CRUD operations
        Route::post('job-types', [ApiJobTypeController::class, 'store'])
            ->name('api.job-types.store')
        ;

        Route::put('job-types/{jobType}', [ApiJobTypeController::class, 'update'])
            ->name('api.job-types.update')
        ;

        Route::delete('job-types/{jobType}', [ApiJobTypeController::class, 'destroy'])
            ->name('api.job-types.destroy')
        ;

        // Administrative routes
        Route::get('job-types/statistics', [ApiJobTypeController::class, 'statistics'])
            ->name('api.job-types.statistics')
        ;

        // Additional API operations for AJAX calls
        Route::patch('job-types/{jobType}/toggle-status', [ApiJobTypeController::class, 'toggleStatus'])
            ->name('api.job-types.toggle-status')
        ;
        Route::post('job-types/{jobType}/duplicate', [ApiJobTypeController::class, 'duplicate'])
            ->name('api.job-types.duplicate');
    });
});
