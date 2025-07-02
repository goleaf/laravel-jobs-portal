<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BrandingSliderController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\FunctionalAreaController;
use App\Http\Controllers\Admin\HeaderSliderController;
use App\Http\Controllers\Admin\ImageSliderController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\OwnershipTypeController;
use App\Http\Controllers\Admin\ReportedJobController;
use App\Http\Controllers\Admin\SalaryCurrencyController;
use App\Http\Controllers\Admin\SalaryPeriodController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Candidate\ApplicationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySizeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Front\BlogCommentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RealTimeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TranslationManagerController;
use App\Http\Controllers\Web\CandidateController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\JobController;
use App\Http\Controllers\Web\TransactionController;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\HabrViewsDemoController;

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
| Laravel Auth Routes (Traditional)
|--------------------------------------------------------------------------
| These routes handle authentication and redirect back to Vue3 SPA
*/

// Authentication routes (still handled by Laravel)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

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

// Company management routes (API will handle data, these serve the SPA)
Route::get('/companies', function () {
    return view('app', ['title' => 'Companies | Job Portal']);
})->name('companies.index');

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

Route::get('/terms', function() {
    return view('app', ['title' => 'Terms | Job Portal']);
})->name('terms');

Route::get('/privacy', function() {
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

// Admin routes
Route::get('admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login');

Route::get('admin/password/forgot', function () {
    return view('auth.admin-forgot-password');
})->name('admin.password.forgot');

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

// Main SPA routes - these serve the Vue3 app
$spaRoutes = [
    '/',
    '/jobs',
    '/jobs/{id}',
    '/companies',
    '/companies/{id}',
    '/candidate',
    '/candidate/{path}',
    '/employer',
    '/employer/{path}',
    '/admin',
    '/admin/{path}',
];

// Register SPA routes
foreach ($spaRoutes as $route) {
    Route::get($route, function () {
        return view('app');
    })->where('path', '.*')->where('id', '[0-9]+');
}

// Catch-all route for Vue3 SPA (must be last)
Route::get('/{path}', function () {
    return view('app');
})->where('path', '.*')->name('spa.catchall');
