<?php

use App\Http\Controllers\Api\JobTypeController as ApiJobTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Job Type Routes
|--------------------------------------------------------------------------
|
| This file contains all routes related to job type functionality
| focusing on API endpoints that are working
|
*/

// API Routes
Route::prefix('api/v1')->middleware(['api', 'throttle:api'])->group(function () {
    
    // Public API routes (no authentication required)
    Route::get('job-types', [ApiJobTypeController::class, 'index'])
        ->name('api.job-types.index');
    
    Route::get('job-types/{jobType}', [ApiJobTypeController::class, 'show'])
        ->name('api.job-types.show');
    
    Route::get('job-types/search', [ApiJobTypeController::class, 'search'])
        ->name('api.job-types.search');
    
    // Protected API routes (authentication required)
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // CRUD operations
        Route::post('job-types', [ApiJobTypeController::class, 'store'])
            ->name('api.job-types.store');
        
        Route::put('job-types/{jobType}', [ApiJobTypeController::class, 'update'])
            ->name('api.job-types.update');
        
        Route::delete('job-types/{jobType}', [ApiJobTypeController::class, 'destroy'])
            ->name('api.job-types.destroy');
        
        // Administrative routes
        Route::get('job-types/statistics', [ApiJobTypeController::class, 'statistics'])
            ->name('api.job-types.statistics');
    });
}); 