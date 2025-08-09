<?php

use App\Http\Controllers\Candidate\ApplicationController;
use App\Http\Controllers\Enhanced\CompanyController as EnhancedCompanyController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RealTimeController;
use App\Http\Controllers\Web\TransactionController;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Vue3 SPA Frontend with Laravel API Backend
| All frontend routes serve the Vue3 SPA
*/

// API Test Route
Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Laravel API is working!',
        'timestamp' => now(),
        'memory_usage' => memory_get_usage(true) / 1024 / 1024 .' MB',
    ]);
})->name('test');

/*
|--------------------------------------------------------------------------
| Locale/Language Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'locale', 'as' => 'locale.'], function () {
    Route::post('switch', [LocaleController::class, 'switch'])->name('switch');
    Route::get('current', [LocaleController::class, 'current'])->name('current');
    Route::get('available', [LocaleController::class, 'available'])->name('available');
    Route::get('translations/{locale?}', [LocaleController::class, 'translations'])->name('translations');
    Route::post('clear-cache', [LocaleController::class, 'clearCache'])->name('clear-cache');
});

/*
|--------------------------------------------------------------------------
| System Routes (No Authentication Required)
|--------------------------------------------------------------------------
| All routes are public as authentication system has been removed
*/

/*
|--------------------------------------------------------------------------
| Legacy Routes (Temporary - for backward compatibility)
|--------------------------------------------------------------------------
*/

Route::get('/about-us', function () {
    return view('app', ['title' => 'About Us | Job Portal']);
})->name('about-us');

Route::get('/aboutus', function () {
    return view('app', ['title' => 'About Us | Job Portal']);
})->name('aboutus.index');

Route::get('/contact', function () {
    return view('app', ['title' => 'Contact Us | Job Portal']);
})->name('contact');

Route::post('/contact', function (Request $request) {
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'email' => 'required|email',
        'phone' => 'nullable|string|max:20',
        'subject' => 'required|string',
        'message' => 'required|string|min:10',
    ]);

    // Process contact form submission
    // In a real application, you would send email notification here

    return back()->with('success', 'Thank you for your message! We will get back to you soon.');
})->name('contact.submit');

// Company management routes
Route::get('/companies', [EnhancedCompanyController::class, 'index'])->name('companies.index');

Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');

Route::get('/company', function () {
    return view('app', ['title' => 'Company | Job Portal']);
})->name('company.index');

// Additional common routes
Route::get('/terms-and-conditions', function () {
    return view('app', ['title' => 'Terms & Conditions | Job Portal']);
})->name('terms.conditions.list');

Route::get('/privacy-policy-page', function () {
    return view('app', ['title' => 'Privacy Policy | Job Portal']);
})->name('privacy.policy.list');

Route::get('/help', function () {
    return view('app', ['title' => 'Help | Job Portal']);
})->name('help.index');

Route::get('/terms', function () {
    return view('app', ['title' => 'Terms | Job Portal']);
})->name('terms');

Route::get('/privacy', function () {
    return view('app', ['title' => 'Privacy | Job Portal']);
})->name('privacy');

Route::get('/posts', function () {
    return view('app', ['title' => 'Posts | Job Portal']);
})->name('posts.index');

// Dashboard routes (serve Vue3 SPA)
Route::get('/dashboard', function () {
    return view('app', ['title' => 'Dashboard | Job Portal']);
})->name('dashboard');

Route::get('/candidate-dashboard', function () {
    return view('app', ['title' => 'Candidate Dashboard | Job Portal']);
})->name('candidate.dashboard');

// Admin routes (no authentication required)
// Admin routes are now public and handled by the SPA

// API routes for AJAX requests
Route::post('/real-time-validation', [RealTimeController::class, 'validateField'])->name('real-time-validation');
Route::get('/get-states', [LocationController::class, 'getStates'])->name('get-states');
Route::get('/get-cities', [LocationController::class, 'getCities'])->name('get-cities');

// Legacy API routes (will be moved to routes/api.php)
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::post('transactions/store', [TransactionController::class, 'store'])->name('transactions.store');
Route::post('job-applications', [ApplicationController::class, 'store'])->name('job-applications.store');

/*
|--------------------------------------------------------------------------
| Vue3 SPA Routes
|--------------------------------------------------------------------------
| All frontend routes serve the Vue3 Single Page Application
*/

// Home page
Route::get('/', function () {
    return view('front_web.home.home');
})->name('home');

// Jobs
Route::get('/jobs', function () {
    return view('jobs.index');
})->name('jobs.index');
Route::get('/jobs/{id}', function ($id) {
    return view('jobs.show', ['id' => $id]);
})->name('jobs.show');

// Companies (handled above)
Route::get('/companies/{id}', function ($id) {
    return view('companies.show', ['id' => $id]);
})->name('companies.show');

// Candidate dashboard
Route::get('/candidate', function () {
    return view('candidate.dashboard');
})->name('candidate.dashboard');

// Employer dashboard
Route::get('/employer', function () {
    return view('employer.dashboard');
})->name('employer.dashboard');

// Admin dashboard
Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// Language Switching Routes
Route::post('/language/switch', [App\Http\Controllers\LanguageController::class, 'switch'])
    ->name('language.switch');
Route::get('/language/supported', [App\Http\Controllers\LanguageController::class, 'getSupportedLanguages'])
    ->name('language.supported');
