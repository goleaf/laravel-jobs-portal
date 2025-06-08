<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
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
