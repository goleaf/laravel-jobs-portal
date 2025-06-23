<?php

use App\Http\Controllers\Api\Universal\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Universal Authentication Routes
|--------------------------------------------------------------------------
| Authentication endpoints using Laravel Sanctum with Universal patterns
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    // Universal Pattern: Login endpoint
    Route::post('/login', [TokenController::class, 'login'])
        ->middleware(['throttle:5,1']) // 5 attempts per minute
        ->name('universal.auth.login')
    ;

    // Universal Pattern: Protected endpoints
    Route::middleware(['auth:sanctum'])->group(function () {
        // Get authenticated user
        Route::get('/user', [TokenController::class, 'user'])
            ->name('universal.auth.user')
        ;

        // Logout current session
        Route::post('/logout', [TokenController::class, 'logout'])
            ->name('universal.auth.logout')
        ;

        // Logout all sessions
        Route::post('/logout-all', [TokenController::class, 'logoutAll'])
            ->name('universal.auth.logout-all')
        ;

        // List user tokens
        Route::get('/tokens', [TokenController::class, 'tokens'])
            ->name('universal.auth.tokens')
        ;
    });
});

// Universal Pattern: Test endpoints
Route::prefix('test')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Test token abilities
    Route::get('/abilities', function (Request $request) {
        return response()->json([
            'user' => $request->user()->only(['id', 'name', 'email']),
            'token_abilities' => $request->user()->currentAccessToken()?->abilities ?? [],
            'can_create_jobs' => $request->user()->tokenCan('jobs:create'),
            'can_update_profile' => $request->user()->tokenCan('profile:update'),
        ]);
    })->name('universal.test.abilities');

    // Test rate limiting
    Route::get('/rate-limit', function () {
        return response()->json([
            'message' => 'Rate limiting is working',
            'timestamp' => now()->toISOString(),
        ]);
    })->middleware(['throttle:10,1'])->name('universal.test.rate-limit');
});
