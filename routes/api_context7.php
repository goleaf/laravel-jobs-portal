<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Context7 API Routes
|--------------------------------------------------------------------------
| Modern API routes implementing Context7 MCP best practices
*/

// Context7 API v1 Routes with comprehensive middleware
Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('v1')->group(function () {
    
    // Context7 Pattern: API Resource routes with caching headers
    Route::middleware(['cache.headers:public;max_age=300'])->group(function () {
        
        // User API Resource
        Route::apiResource('user', App\Http\Controllers\Api\Context7\UserApiController::class);
        // Job API Resource
        Route::apiResource('job', App\Http\Controllers\Api\Context7\JobApiController::class);
        // Company API Resource
        Route::apiResource('company', App\Http\Controllers\Api\Context7\CompanyApiController::class)->names('api.company');
        // Candidate API Resource
        Route::apiResource('candidate', App\Http\Controllers\Api\Context7\CandidateApiController::class);
        // JobApplication API Resource
        Route::apiResource('jobapplication', App\Http\Controllers\Api\Context7\JobApplicationApiController::class);
        // Skill API Resource
        Route::apiResource('skill', App\Http\Controllers\Api\Context7\SkillApiController::class);
    });
    
    // Context7 Pattern: Public endpoints with longer caching
    Route::middleware(['cache.headers:public;max_age=1800'])->group(function () {
        Route::get('/stats', function () {
            return response()->json([
                'jobs_count' => \App\Models\Job::count(),
                'companies_count' => \App\Models\Company::count(),
                'candidates_count' => \App\Models\Candidate::count(),
                'applications_count' => \App\Models\JobApplication::count(),
            ]);
        });
        
        Route::get('/health', function () {
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0')
            ]);
        });
    });
});

// Context7 Pattern: Guest API endpoints (no authentication required)
Route::middleware(['throttle:60,1'])->prefix('v1/public')->group(function () {
    Route::get('/jobs', [App\Http\Controllers\Api\Context7\JobApiController::class, 'index']);
    Route::get('/jobs/{job}', [App\Http\Controllers\Api\Context7\JobApiController::class, 'show']);
    Route::get('/companies', [App\Http\Controllers\Api\Context7\CompanyApiController::class, 'index']);
    Route::get('/companies/{company}', [App\Http\Controllers\Api\Context7\CompanyApiController::class, 'show']);
});

// Legacy API endpoints for testing
Route::get('/jobs', function () {
    return response()->json([
        'message' => 'API endpoint requires authentication',
        'status' => 'unauthorized'
    ], 401);
})->name('api.jobs.index');