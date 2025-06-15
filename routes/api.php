<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobTypeController;
use App\Http\Controllers\Api\V1\AdminApiController;
use App\Http\Controllers\Api\V1\CandidateApiController;
use App\Http\Controllers\Api\V1\CompanyApiController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\JobApiController;
use App\Models\Job;
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
    /*
    |--------------------------------------------------------------------------
    | Login Information API Routes (for Vue components)
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::get('/login-info', [AuthController::class, 'getLoginInfo'])->name('api.auth.login-info');
        Route::post('/verify-credentials', [AuthController::class, 'verifyCredentials'])->name('api.auth.verify-credentials');
    });

    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Login Information API Routes (for Vue components)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::get('/login-info', [AuthController::class, 'getLoginInfo'])->name('api.auth.login-info');
    Route::post('/verify-credentials', [AuthController::class, 'verifyCredentials'])->name('api.auth.verify-credentials');
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
            'zh' => ['name' => 'Chinese', 'flag' => '🇨🇳', 'native' => '中文'],
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
            'default' => config('languages.default', 'en'),
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
                    'rtl' => in_array($locale, config('languages.rtl_languages', ['ar'])),
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
            'message' => 'Language switched successfully',
        ]);
    });
});

// Legacy API endpoints for security testing
Route::get('/jobs', function () {
    return response()->json([
        'message' => 'API endpoint requires authentication',
        'status' => 'unauthorized',
    ], 401);
})->name('api.jobs.index');

// Health check endpoint for Vue.js SPA
Route::get('/v1/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is healthy',
        'timestamp' => now()->toISOString(),
        'version' => 'v1.0.0',
    ]);
})->name('api.health');

// API v1 routes
Route::prefix('v1')->group(function () {
    Route::get('/status', function () {
        return response()->json([
            'status' => 'operational',
            'api_version' => 'v1.0.0',
        ]);
    });

    // Authentication routes (public)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);
        Route::post('/register', [App\Http\Controllers\Api\V1\AuthController::class, 'register']);

        // Protected authentication routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', [App\Http\Controllers\Api\V1\AuthController::class, 'user']);
            Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
            Route::post('/logout-all', [App\Http\Controllers\Api\V1\AuthController::class, 'logoutAll']);
            Route::post('/refresh', [App\Http\Controllers\Api\V1\AuthController::class, 'refresh']);
            Route::get('/check-role/{role}', [App\Http\Controllers\Api\V1\AuthController::class, 'checkRole']);
        });
    });

    // Test dashboard stats without authentication
    Route::get('/test/dashboard/stats', [DashboardController::class, 'getStats']);

    // Simple test endpoint
    Route::get('/test/simple', function () {
        return response()->json([
            'success' => true,
            'message' => 'Simple test works',
            'data' => [
                'timestamp' => now()->toISOString(),
                'jobs_count' => Job::count(),
            ],
        ]);
    });

    // Dashboard API routes for admin (temporary without auth for testing)
    Route::prefix('admin/dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats']);
        Route::get('/recent-jobs', [DashboardController::class, 'getRecentJobs']);
        Route::get('/recent-applications', [DashboardController::class, 'getRecentApplications']);
        Route::get('/application-status-distribution', [DashboardController::class, 'getApplicationStatusDistribution']);
        Route::get('/job-posting-trends', [DashboardController::class, 'getJobPostingTrends']);
        Route::get('/top-companies', [DashboardController::class, 'getTopCompanies']);
    });

    // Jobs API routes
    Route::prefix('jobs')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [JobApiController::class, 'index']);
        Route::post('/', [JobApiController::class, 'store']);
        Route::get('/{id}', [JobApiController::class, 'show']);
        Route::put('/{id}', [JobApiController::class, 'update']);
        Route::delete('/{id}', [JobApiController::class, 'destroy']);
    });

    // Companies API routes
    Route::prefix('companies')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [CompanyApiController::class, 'index']);
        Route::post('/', [CompanyApiController::class, 'store']);
        Route::get('/{id}', [CompanyApiController::class, 'show']);
        Route::put('/{id}', [CompanyApiController::class, 'update']);
        Route::delete('/{id}', [CompanyApiController::class, 'destroy']);
    });

    // Candidates API routes
    Route::prefix('candidates')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [CandidateApiController::class, 'index']);
        Route::post('/', [CandidateApiController::class, 'store']);
        Route::get('/{id}', [CandidateApiController::class, 'show']);
        Route::put('/{id}', [CandidateApiController::class, 'update']);
        Route::delete('/{id}', [CandidateApiController::class, 'destroy']);
    });

    // Admin users API routes
    Route::prefix('admin/users')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [AdminApiController::class, 'index']);
        Route::post('/', [AdminApiController::class, 'store']);
        Route::get('/{id}', [AdminApiController::class, 'show']);
        Route::put('/{id}', [AdminApiController::class, 'update']);
        Route::delete('/{id}', [AdminApiController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [AdminApiController::class, 'toggleStatus']);
    });

    // Job Types API Routes
    Route::get('job-types', [JobTypeController::class, 'index']);
    Route::get('job-types/{jobType}', [JobTypeController::class, 'show']);
    Route::get('job-types/search', [JobTypeController::class, 'search']);

    // Protected job types routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('job-types', [JobTypeController::class, 'store']);
        Route::put('job-types/{jobType}', [JobTypeController::class, 'update']);
        Route::delete('job-types/{jobType}', [JobTypeController::class, 'destroy']);
        Route::get('job-types/statistics', [JobTypeController::class, 'statistics']);
    });

    /**
     * ELOQUENT HAS MANY DEEP INTEGRATION ROUTES
     * 
     * Package: staudenmeir/eloquent-has-many-deep v1.21
     * Source: https://github.com/staudenmeir/eloquent-has-many-deep
     * Reference: https://madewithlaravel.com/eloquent-has-many-deep
     * 
     * These routes demonstrate complex multi-level relationships
     * that replace multiple queries with single optimized calls.
     */
    Route::middleware(['auth:sanctum'])->prefix('deep-relationships')->group(function () {
        // User Location Jobs: User -> Country -> State -> City -> Jobs
        Route::get('/location-jobs', [\App\Http\Controllers\Enhanced\DeepRelationshipController::class, 'getUserLocationJobs'])
            ->name('api.deep.location-jobs');
        
        // Company Applications: User -> Company -> Jobs -> JobApplications  
        Route::get('/company-applications', [\App\Http\Controllers\Enhanced\DeepRelationshipController::class, 'getCompanyApplications'])
            ->name('api.deep.company-applications');
        
        // Region Candidates: User -> Country -> State -> City -> Users (Candidates)
        Route::get('/region-candidates', [\App\Http\Controllers\Enhanced\DeepRelationshipController::class, 'getRegionCandidates'])
            ->name('api.deep.region-candidates');
        
        // Applied Skills: User -> JobApplications -> Jobs -> JobSkills -> Skills
        Route::get('/applied-skills', [\App\Http\Controllers\Enhanced\DeepRelationshipController::class, 'getCandidateAppliedSkills'])
            ->name('api.deep.applied-skills');
        
        // Similar Candidates: User -> JobApplications -> Jobs -> JobApplications -> Users
        Route::get('/similar-candidates', [\App\Http\Controllers\Enhanced\DeepRelationshipController::class, 'getSimilarCandidates'])
            ->name('api.deep.similar-candidates');
        
        // Comprehensive Analytics using multiple deep relationships
        Route::get('/analytics', [\App\Http\Controllers\Enhanced\DeepRelationshipController::class, 'getDeepAnalytics'])
            ->name('api.deep.analytics');
    });
});

/*
|--------------------------------------------------------------------------
| Laravel Model Settings API Routes
|--------------------------------------------------------------------------
| These routes demonstrate the full functionality of the glorand/laravel-model-settings
| package with comprehensive CRUD operations for settings management.
*/

Route::prefix('model-settings')->name('model-settings.')->group(function () {
    // User Settings Routes
    Route::prefix('users/{userId}')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ModelSettingsController::class, 'getUserSettings'])->name('get');
        Route::put('/', [App\Http\Controllers\Api\ModelSettingsController::class, 'updateUserSettings'])->name('update');
        Route::delete('/', [App\Http\Controllers\Api\ModelSettingsController::class, 'clearUserSettings'])->name('clear');
        
        Route::get('/{key}', [App\Http\Controllers\Api\ModelSettingsController::class, 'getUserSetting'])->name('get-setting');
        Route::put('/{key}', [App\Http\Controllers\Api\ModelSettingsController::class, 'setUserSetting'])->name('set-setting');
        Route::delete('/{key}', [App\Http\Controllers\Api\ModelSettingsController::class, 'deleteUserSetting'])->name('delete-setting');
    });
    
    // Company Settings Routes
    Route::prefix('companies/{companyId}')->name('companies.')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ModelSettingsController::class, 'getCompanySettings'])->name('get');
        Route::put('/', [App\Http\Controllers\Api\ModelSettingsController::class, 'updateCompanySettings'])->name('update');
    });
    
    // Demonstration and Schema Routes
    Route::get('/demo', [App\Http\Controllers\Api\ModelSettingsController::class, 'demonstrateFeatures'])->name('demo');
    Route::get('/schema', [App\Http\Controllers\Api\ModelSettingsController::class, 'getSettingsSchema'])->name('schema');
});
