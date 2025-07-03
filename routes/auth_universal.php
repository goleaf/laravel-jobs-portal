<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Universal Public Routes (Authentication Removed)
|--------------------------------------------------------------------------
| All routes are now public as authentication system has been removed
| These routes provide basic API structure without authentication
*/

// Public API routes (no authentication required)
Route::prefix('auth')->group(function () {
    // Universal Pattern: Public endpoints for backward compatibility
    Route::post('/login', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'message' => 'Authentication system disabled - all access is public',
            'data' => [
                'user' => [
                    'id' => 1,
                    'name' => 'Public User',
                    'email' => 'public@example.com',
                    'role' => 'public',
                ],
                'token' => 'public-access-token',
                'abilities' => ['*'], // All abilities for public access
            ],
        ]);
    })
        ->middleware(['throttle:5,1']) // Keep rate limiting for security
        ->name('universal.auth.login');

    // Public endpoints (no authentication required)
    Route::group([], function () {
        // Get public user info
        Route::get('/user', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => 1,
                    'name' => 'Public User',
                    'email' => 'public@example.com',
                    'role' => 'public',
                    'permissions' => ['*'], // All permissions
                ],
            ]);
        })
            ->name('universal.auth.user');

        // Public logout (for backward compatibility)
        Route::post('/logout', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logout successful (authentication disabled)',
            ]);
        })
            ->name('universal.auth.logout');

        // Public logout all (for backward compatibility)
        Route::post('/logout-all', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'message' => 'All sessions logged out (authentication disabled)',
            ]);
        })
            ->name('universal.auth.logout-all');

        // Public tokens list (for backward compatibility)
        Route::get('/tokens', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'public-access-token',
                        'abilities' => ['*'],
                        'created_at' => now()->toISOString(),
                        'last_used_at' => now()->toISOString(),
                    ],
                ],
            ]);
        })
            ->name('universal.auth.tokens');
    });
});

// Universal Pattern: Public test endpoints
Route::prefix('test')->middleware(['throttle:60,1'])->group(function () {
    // Test public abilities
    Route::get('/abilities', function (Request $request) {
        return response()->json([
            'user' => [
                'id' => 1,
                'name' => 'Public User',
                'email' => 'public@example.com',
            ],
            'token_abilities' => ['*'], // All abilities for public access
            'can_create_jobs' => true,
            'can_update_profile' => true,
            'authentication_disabled' => true,
        ]);
    })->name('universal.test.abilities');

    // Test rate limiting (keeping for security)
    Route::get('/rate-limit', function () {
        return response()->json([
            'message' => 'Rate limiting is working (authentication disabled)',
            'timestamp' => now()->toISOString(),
            'public_access' => true,
        ]);
    })->middleware(['throttle:10,1'])->name('universal.test.rate-limit');
});
