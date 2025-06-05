<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Context7\TokenController;

/*
|--------------------------------------------------------------------------
| Context7 Authentication Routes
|--------------------------------------------------------------------------
| Authentication endpoints using Laravel Sanctum with Context7 patterns
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    
    // Context7 Pattern: Login endpoint
    Route::post('/login', [TokenController::class, 'login'])
        ->middleware(['throttle:5,1']) // 5 attempts per minute
        ->name('context7.auth.login');
    
    // Context7 Pattern: Protected endpoints
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Get authenticated user
        Route::get('/user', [TokenController::class, 'user'])
            ->name('context7.auth.user');
        
        // Logout current session
        Route::post('/logout', [TokenController::class, 'logout'])
            ->name('context7.auth.logout');
        
        // Logout all sessions
        Route::post('/logout-all', [TokenController::class, 'logoutAll'])
            ->name('context7.auth.logout-all');
        
        // List user tokens
        Route::get('/tokens', [TokenController::class, 'tokens'])
            ->name('context7.auth.tokens');
    });
});

// Context7 Pattern: Test endpoints
Route::prefix('test')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    
    // Test token abilities
    Route::get('/abilities', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'user' => $request->user()->only(['id', 'name', 'email']),
            'token_abilities' => $request->user()->currentAccessToken()?->abilities ?? [],
            'can_create_jobs' => $request->user()->tokenCan('jobs:create'),
            'can_update_profile' => $request->user()->tokenCan('profile:update'),
        ]);
    })->name('context7.test.abilities');
    
    // Test rate limiting
    Route::get('/rate-limit', function () {
        return response()->json([
            'message' => 'Rate limiting is working',
            'timestamp' => now()->toISOString()
        ]);
    })->middleware(['throttle:10,1'])->name('context7.test.rate-limit');
});