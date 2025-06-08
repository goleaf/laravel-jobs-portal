<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Universal Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth_universal.php';

/*
|--------------------------------------------------------------------------
| Universal API v1 Routes  
|--------------------------------------------------------------------------
*/
require __DIR__.'/api_universal.php';

// Universal Language API Routes
Route::group(['prefix' => 'i18n'], function () {
    // Get available languages
    Route::get('/languages', function () {
        $availableLanguages = config('languages.available', ['en']);
        $languageMap = [
            'en' => ['name' => 'English', 'flag' => '🇺🇸', 'native' => 'English'],
            'ar' => ['name' => 'Arabic', 'flag' => '🇸🇦', 'native' => 'العربية'],
            'de' => ['name' => 'German', 'flag' => '🇩🇪', 'native' => 'Deutsch'],
            'es' => ['name' => 'Spanish', 'flag' => '🇪🇸', 'native' => 'Español'],
            'fr' => ['name' => 'French', 'flag' => '🇫🇷', 'native' => 'Français'],
            'pt' => ['name' => 'Portuguese', 'flag' => '🇵🇹', 'native' => 'Português'],
            'ru' => ['name' => 'Russian', 'flag' => '🇷🇺', 'native' => 'Русский'],
            'tr' => ['name' => 'Turkish', 'flag' => '🇹🇷', 'native' => 'Türkçe'],
            'zh' => ['name' => 'Chinese', 'flag' => '🇨🇳', 'native' => '中文']
        ];

        $result = [];
        foreach ($availableLanguages as $lang) {
            if (isset($languageMap[$lang])) {
                $result[$lang] = $languageMap[$lang];
            }
        }

        return response()->json([
            'current' => app()->getLocale(),
            'languages' => $result,
            'rtl_languages' => config('languages.rtl_languages', ['ar']),
            'default' => config('languages.default', 'en')
        ]);
    });

    // Get translations for a specific language
    Route::get('/translations/{locale}', function ($locale) {
        $availableLanguages = config('languages.available', ['en']);
        
        if (!in_array($locale, $availableLanguages)) {
            return response()->json(['error' => 'Language not available'], 404);
        }

        try {
            $jsonPath = resource_path("lang/{$locale}_json/master.json");
            
            if (file_exists($jsonPath)) {
                $translations = json_decode(file_get_contents($jsonPath), true);
                
                return response()->json([
                    'locale' => $locale,
                    'translations' => $translations ?: [],
                    'rtl' => in_array($locale, config('languages.rtl_languages', ['ar']))
                ]);
            }

            return response()->json(['error' => 'Translations not found'], 404);
            
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to load translations'], 500);
        }
    });

    // Switch language and update session
    Route::post('/switch', function (Request $request) {
        $locale = $request->input('locale');
        $availableLanguages = config('languages.available', ['en']);

        if (!in_array($locale, $availableLanguages)) {
            return response()->json(['error' => 'Language not available'], 400);
        }

        // Update session
        session(['locale' => $locale]);
        app()->setLocale($locale);

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'rtl' => in_array($locale, config('languages.rtl_languages', ['ar'])),
            'message' => 'Language switched successfully'
        ]);
    });
});

// Legacy API endpoints for security testing
Route::get('/jobs', function () {
    return response()->json([
        'message' => 'API endpoint requires authentication',
        'status' => 'unauthorized'
    ], 401);
})->name('api.jobs.index');

// Health check endpoint for Vue.js SPA
Route::get('/v1/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is healthy',
        'timestamp' => now()->toISOString(),
        'version' => 'v1.0.0'
    ]);
})->name('api.health');

// API v1 routes
Route::prefix('v1')->group(function () {
    Route::get('/status', function () {
        return response()->json([
            'status' => 'operational',
            'api_version' => 'v1.0.0'
        ]);
    });

    // Authentication routes (public)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
        Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
        
        // Protected authentication routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', [\App\Http\Controllers\Api\V1\AuthController::class, 'user']);
            Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
            Route::post('/logout-all', [\App\Http\Controllers\Api\V1\AuthController::class, 'logoutAll']);
            Route::post('/refresh', [\App\Http\Controllers\Api\V1\AuthController::class, 'refresh']);
            Route::get('/check-role/{role}', [\App\Http\Controllers\Api\V1\AuthController::class, 'checkRole']);
        });
    });

    // Test dashboard stats without authentication
    Route::get('/test/dashboard/stats', [\App\Http\Controllers\Api\V1\DashboardController::class, 'getStats']);
    
    // Simple test endpoint
    Route::get('/test/simple', function () {
        return response()->json([
            'success' => true,
            'message' => 'Simple test works',
            'data' => [
                'timestamp' => now()->toISOString(),
                'jobs_count' => \App\Models\Job::count()
            ]
        ]);
    });

    // Dashboard API routes for admin (temporary without auth for testing)
    Route::prefix('admin/dashboard')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\Api\V1\DashboardController::class, 'getStats']);
        Route::get('/recent-jobs', [\App\Http\Controllers\Api\V1\DashboardController::class, 'getRecentJobs']);
        Route::get('/recent-applications', [\App\Http\Controllers\Api\V1\DashboardController::class, 'getRecentApplications']);
        Route::get('/application-status-distribution', [\App\Http\Controllers\Api\V1\DashboardController::class, 'getApplicationStatusDistribution']);
        Route::get('/job-posting-trends', [\App\Http\Controllers\Api\V1\DashboardController::class, 'getJobPostingTrends']);
        Route::get('/top-companies', [\App\Http\Controllers\Api\V1\DashboardController::class, 'getTopCompanies']);
    });

    // Jobs API routes
    Route::prefix('jobs')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\JobApiController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\V1\JobApiController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\V1\JobApiController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\V1\JobApiController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\V1\JobApiController::class, 'destroy']);
    });

    // Companies API routes  
    Route::prefix('companies')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\CompanyApiController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\V1\CompanyApiController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\V1\CompanyApiController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\V1\CompanyApiController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\V1\CompanyApiController::class, 'destroy']);
    });

    // Candidates API routes
    Route::prefix('candidates')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\CandidateApiController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\V1\CandidateApiController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\V1\CandidateApiController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\V1\CandidateApiController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\V1\CandidateApiController::class, 'destroy']);
    });

    // Admin users API routes
    Route::prefix('admin/users')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\AdminApiController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\V1\AdminApiController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\V1\AdminApiController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\V1\AdminApiController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\V1\AdminApiController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [\App\Http\Controllers\Api\V1\AdminApiController::class, 'toggleStatus']);
    });
});
