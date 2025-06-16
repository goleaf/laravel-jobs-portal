<?php

use App\Http\Controllers\Api\Universal\UniversalUniqueValueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Universal Unique Values API Routes
|--------------------------------------------------------------------------
|
| REST API endpoints for generating unique values using the Laravel
| Unique Values package integration with concurrency support.
|
*/

Route::prefix('unique-values')->name('unique-values.')->group(function () {
    
    // Individual unique value generators
    Route::post('job-reference', [UniversalUniqueValueController::class, 'generateJobReference'])
        ->name('job-reference');
    
    Route::post('application-code', [UniversalUniqueValueController::class, 'generateApplicationCode'])
        ->name('application-code');
    
    Route::post('candidate-code', [UniversalUniqueValueController::class, 'generateCandidateCode'])
        ->name('candidate-code');
    
    Route::post('company-code', [UniversalUniqueValueController::class, 'generateCompanyCode'])
        ->name('company-code');
    
    Route::post('slug', [UniversalUniqueValueController::class, 'generateSlug'])
        ->name('slug');
    
    // Batch operations
    Route::post('batch', [UniversalUniqueValueController::class, 'generateBatch'])
        ->name('batch');
    
    // Custom generation
    Route::post('custom', [UniversalUniqueValueController::class, 'generateCustom'])
        ->name('custom');
    
    // Statistics and monitoring
    Route::get('stats', [UniversalUniqueValueController::class, 'getStats'])
        ->name('stats');
}); 