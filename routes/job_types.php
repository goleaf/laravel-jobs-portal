<?php

use App\Http\Controllers\Api\JobTypeController as ApiJobTypeController;
use App\Http\Controllers\Web\JobTypeController as WebJobTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Job Type Routes
|--------------------------------------------------------------------------
|
| This file contains all routes related to job type functionality
| including both web and API endpoints with proper middleware
|
*/

// API Routes
Route::prefix('api/v1')->middleware(['api', 'throttle:api'])->group(function () {
    
    // Public API routes (no authentication required)
    Route::get('job-types', [ApiJobTypeController::class, 'index'])
        ->name('api.job-types.index');
    
    Route::get('job-types/{jobType}', [ApiJobTypeController::class, 'show'])
        ->name('api.job-types.show');
    
    Route::get('job-types/{jobType}/jobs', [ApiJobTypeController::class, 'jobs'])
        ->name('api.job-types.jobs');
    
    Route::get('job-types/search', [ApiJobTypeController::class, 'search'])
        ->name('api.job-types.search');
    
    // Protected API routes (authentication required)
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // CRUD operations
        Route::post('job-types', [ApiJobTypeController::class, 'store'])
            ->middleware(['can:create,App\Models\JobType'])
            ->name('api.job-types.store');
        
        Route::put('job-types/{jobType}', [ApiJobTypeController::class, 'update'])
            ->middleware(['can:update,jobType'])
            ->name('api.job-types.update');
        
        Route::patch('job-types/{jobType}', [ApiJobTypeController::class, 'update'])
            ->middleware(['can:update,jobType'])
            ->name('api.job-types.patch');
        
        Route::delete('job-types/{jobType}', [ApiJobTypeController::class, 'destroy'])
            ->middleware(['can:delete,jobType'])
            ->name('api.job-types.destroy');
        
        // Administrative routes
        Route::get('job-types/statistics', [ApiJobTypeController::class, 'statistics'])
            ->middleware(['can:viewAny,App\Models\JobType'])
            ->name('api.job-types.statistics');
        
        Route::post('job-types/bulk-update', [ApiJobTypeController::class, 'bulkUpdate'])
            ->middleware(['can:update,App\Models\JobType'])
            ->name('api.job-types.bulk-update');
        
        Route::post('job-types/bulk-delete', [ApiJobTypeController::class, 'bulkDelete'])
            ->middleware(['can:delete,App\Models\JobType'])
            ->name('api.job-types.bulk-delete');
        
        // Status management
        Route::patch('job-types/{jobType}/activate', [ApiJobTypeController::class, 'activate'])
            ->middleware(['can:update,jobType'])
            ->name('api.job-types.activate');
        
        Route::patch('job-types/{jobType}/deactivate', [ApiJobTypeController::class, 'deactivate'])
            ->middleware(['can:update,jobType'])
            ->name('api.job-types.deactivate');
        
        Route::patch('job-types/{jobType}/feature', [ApiJobTypeController::class, 'feature'])
            ->middleware(['can:update,jobType'])
            ->name('api.job-types.feature');
        
        Route::patch('job-types/{jobType}/unfeature', [ApiJobTypeController::class, 'unfeature'])
            ->middleware(['can:update,jobType'])
            ->name('api.job-types.unfeature');
        
        // Import/Export
        Route::post('job-types/import', [ApiJobTypeController::class, 'import'])
            ->middleware(['can:create,App\Models\JobType'])
            ->name('api.job-types.import');
        
        Route::get('job-types/export', [ApiJobTypeController::class, 'export'])
            ->middleware(['can:viewAny,App\Models\JobType'])
            ->name('api.job-types.export');
        
        // Duplicate
        Route::post('job-types/{jobType}/duplicate', [ApiJobTypeController::class, 'duplicate'])
            ->middleware(['can:create,App\Models\JobType'])
            ->name('api.job-types.duplicate');
    });
});

// Web Routes
Route::middleware(['web'])->group(function () {
    
    // Public web routes
    Route::get('job-types', [WebJobTypeController::class, 'index'])
        ->name('job-types.index');
    
    Route::get('job-types/{jobType:slug}', [WebJobTypeController::class, 'show'])
        ->name('job-types.show');
    
    Route::get('job-types/{jobType:slug}/jobs', [WebJobTypeController::class, 'jobs'])
        ->name('job-types.jobs');
    
    // Admin web routes (authentication required)
    Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
        
        // Dashboard routes
        Route::get('job-types', [WebJobTypeController::class, 'adminIndex'])
            ->middleware(['can:viewAny,App\Models\JobType'])
            ->name('admin.job-types.index');
        
        Route::get('job-types/create', [WebJobTypeController::class, 'create'])
            ->middleware(['can:create,App\Models\JobType'])
            ->name('admin.job-types.create');
        
        Route::post('job-types', [WebJobTypeController::class, 'store'])
            ->middleware(['can:create,App\Models\JobType'])
            ->name('admin.job-types.store');
        
        Route::get('job-types/{jobType}/edit', [WebJobTypeController::class, 'edit'])
            ->middleware(['can:update,jobType'])
            ->name('admin.job-types.edit');
        
        Route::put('job-types/{jobType}', [WebJobTypeController::class, 'update'])
            ->middleware(['can:update,jobType'])
            ->name('admin.job-types.update');
        
        Route::delete('job-types/{jobType}', [WebJobTypeController::class, 'destroy'])
            ->middleware(['can:delete,jobType'])
            ->name('admin.job-types.destroy');
        
        // Administrative pages
        Route::get('job-types/statistics', [WebJobTypeController::class, 'statistics'])
            ->middleware(['can:viewAny,App\Models\JobType'])
            ->name('admin.job-types.statistics');
        
        Route::get('job-types/settings', [WebJobTypeController::class, 'settings'])
            ->middleware(['can:update,App\Models\JobType'])
            ->name('admin.job-types.settings');
        
        Route::post('job-types/settings', [WebJobTypeController::class, 'updateSettings'])
            ->middleware(['can:update,App\Models\JobType'])
            ->name('admin.job-types.settings.update');
        
        // Bulk operations
        Route::post('job-types/bulk-action', [WebJobTypeController::class, 'bulkAction'])
            ->middleware(['can:update,App\Models\JobType'])
            ->name('admin.job-types.bulk-action');
        
        // Import/Export
        Route::get('job-types/export', [WebJobTypeController::class, 'export'])
            ->middleware(['can:viewAny,App\Models\JobType'])
            ->name('admin.job-types.export');
        
        Route::get('job-types/import', [WebJobTypeController::class, 'importForm'])
            ->middleware(['can:create,App\Models\JobType'])
            ->name('admin.job-types.import');
        
        Route::post('job-types/import', [WebJobTypeController::class, 'import'])
            ->middleware(['can:create,App\Models\JobType'])
            ->name('admin.job-types.import.process');
    });
    
    // Employer routes (for managing job types within job postings)
    Route::middleware(['auth', 'verified', 'role:employer'])->prefix('employer')->group(function () {
        
        Route::get('job-types', [WebJobTypeController::class, 'employerIndex'])
            ->name('employer.job-types.index');
        
        Route::get('job-types/{jobType}', [WebJobTypeController::class, 'employerShow'])
            ->name('employer.job-types.show');
        
        // Suggest new job type
        Route::get('job-types/suggest', [WebJobTypeController::class, 'suggest'])
            ->name('employer.job-types.suggest');
        
        Route::post('job-types/suggest', [WebJobTypeController::class, 'submitSuggestion'])
            ->name('employer.job-types.suggest.submit');
    });
});

// Additional specialized routes
Route::middleware(['web'])->group(function () {
    
    // AJAX routes for dynamic content
    Route::get('ajax/job-types', [WebJobTypeController::class, 'ajaxIndex'])
        ->name('ajax.job-types.index');
    
    Route::get('ajax/job-types/{jobType}/jobs-count', [WebJobTypeController::class, 'ajaxJobsCount'])
        ->name('ajax.job-types.jobs-count');
    
    Route::get('ajax/job-types/popular', [WebJobTypeController::class, 'ajaxPopular'])
        ->name('ajax.job-types.popular');
    
    Route::get('ajax/job-types/trending', [WebJobTypeController::class, 'ajaxTrending'])
        ->name('ajax.job-types.trending');
    
    // Autocomplete for forms
    Route::get('api/job-types/autocomplete', [ApiJobTypeController::class, 'autocomplete'])
        ->name('api.job-types.autocomplete');
    
    // Widget endpoints
    Route::get('widgets/job-types/featured', [WebJobTypeController::class, 'widgetFeatured'])
        ->name('widgets.job-types.featured');
    
    Route::get('widgets/job-types/popular', [WebJobTypeController::class, 'widgetPopular'])
        ->name('widgets.job-types.popular');
});

// SEO and Sitemap routes
Route::middleware(['web'])->group(function () {
    
    Route::get('sitemap/job-types.xml', [WebJobTypeController::class, 'sitemap'])
        ->name('sitemap.job-types');
    
    Route::get('job-types.xml', [WebJobTypeController::class, 'sitemap'])
        ->name('job-types.sitemap');
});

// Cache management routes (admin only)
Route::middleware(['web', 'auth', 'role:admin'])->prefix('admin/cache')->group(function () {
    
    Route::delete('job-types', [WebJobTypeController::class, 'clearCache'])
        ->name('admin.cache.job-types.clear');
    
    Route::post('job-types/warm', [WebJobTypeController::class, 'warmCache'])
        ->name('admin.cache.job-types.warm');
}); 