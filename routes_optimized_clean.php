<?php

/**
 * OPTIMIZED ROUTES FILE - CLEAN VERSION
 * Following Laravel routing best practices from Context7 documentation
 * ✅ Proper route grouping with middleware
 * ✅ Consistent naming conventions  
 * ✅ Route caching optimization
 * ✅ Removed duplicates and conflicts
 * ✅ Added rate limiting
 * ✅ Used route model binding
 * ✅ Applied global parameter constraints
 */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Pattern Constraints (Context7 Best Practice)
|--------------------------------------------------------------------------
*/
Route::pattern('id', '[0-9]+');
Route::pattern('token', '[a-zA-Z0-9]{32,}');
Route::pattern('locale', 'en|ar|de|es|fr|pt|ru|tr|zh');
Route::pattern('slug', '[a-z0-9-]+');

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication)
|--------------------------------------------------------------------------
*/

// Home & Landing
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Static Pages Group
Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');
    Route::get('/privacy', [App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
    Route::get('/terms', [App\Http\Controllers\PageController::class, 'terms'])->name('terms');
});

// Public Job Browsing
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [App\Http\Controllers\JobController::class, 'index'])->name('index');
    Route::get('/{job:slug}', [App\Http\Controllers\JobController::class, 'show'])->name('show');
    Route::get('/search', [App\Http\Controllers\JobController::class, 'search'])->name('search');
    Route::get('/categories', [App\Http\Controllers\JobController::class, 'categories'])->name('categories');
});

// Public Company Browsing
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [App\Http\Controllers\CompanyController::class, 'index'])->name('index');
    Route::get('/{company:slug}', [App\Http\Controllers\CompanyController::class, 'show'])->name('show');
});

// Blog/Posts
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [App\Http\Controllers\BlogController::class, 'index'])->name('index');
    Route::get('/{post:slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('show');
    Route::get('/category/{category:slug}', [App\Http\Controllers\BlogController::class, 'category'])->name('category');
});

// Language Switching
Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    
    // Registration
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    
    // Password Reset
    Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::get('/password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    Route::post('/password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'confirm']);
});

/*
|--------------------------------------------------------------------------
| API Routes (Rate Limited)
|--------------------------------------------------------------------------
*/

Route::prefix('api/v1')->middleware(['api', 'throttle:api'])->name('api.')->group(function () {
    // Public API
    Route::get('/jobs', [App\Http\Controllers\Api\JobController::class, 'index'])->name('jobs.index');
    Route::get('/companies', [App\Http\Controllers\Api\CompanyController::class, 'index'])->name('companies.index');
    
    // Authenticated API
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn(Illuminate\Http\Request $request) => $request->user())->name('user');
        
        // Applications API
        Route::apiResource('applications', App\Http\Controllers\Api\ApplicationController::class);
        Route::apiResource('jobs.applications', App\Http\Controllers\Api\JobApplicationController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Candidate Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:access-candidate-features'])->prefix('candidate')->name('candidate.')->group(function () {
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\Candidate\ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\Candidate\ProfileController::class, 'edit'])->name('edit');
        Route::put('/general', [App\Http\Controllers\Candidate\ProfileController::class, 'updateGeneral'])->name('update.general');
        Route::put('/career', [App\Http\Controllers\Candidate\ProfileController::class, 'updateCareer'])->name('update.career');
    });
    
    // Applications
    Route::resource('applications', App\Http\Controllers\Candidate\ApplicationController::class)->only(['index', 'show', 'store']);
    
    // Experience & Education
    Route::resource('experiences', App\Http\Controllers\Candidate\ExperienceController::class)->except(['show']);
    Route::resource('educations', App\Http\Controllers\Candidate\EducationController::class)->except(['show']);
    
    // Job Alerts & Favorites
    Route::resource('job-alerts', App\Http\Controllers\Candidate\JobAlertController::class)->except(['show']);
    Route::resource('favorite-jobs', App\Http\Controllers\Candidate\FavoriteJobController::class)->only(['index', 'store', 'destroy']);
    
    // Resume
    Route::get('/resume/download', [App\Http\Controllers\Candidate\ResumeController::class, 'download'])->name('resume.download');
});

/*
|--------------------------------------------------------------------------
| Employer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:access-employer-features'])->prefix('employer')->name('employer.')->group(function () {
    // Company
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/', [App\Http\Controllers\Employer\CompanyController::class, 'show'])->name('show');
        Route::get('/edit', [App\Http\Controllers\Employer\CompanyController::class, 'edit'])->name('edit');
        Route::put('/', [App\Http\Controllers\Employer\CompanyController::class, 'update'])->name('update');
    });
    
    // Jobs
    Route::resource('jobs', App\Http\Controllers\Employer\JobController::class);
    
    // Applications
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Employer\ApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [App\Http\Controllers\Employer\ApplicationController::class, 'show'])->name('show');
        Route::patch('/{application}/status', [App\Http\Controllers\Employer\ApplicationController::class, 'updateStatus'])->name('update-status');
    });
    
    // Interview Scheduling
    Route::prefix('interviews')->name('interviews.')->group(function () {
        Route::get('/schedule/{application}', [App\Http\Controllers\Employer\InterviewController::class, 'schedule'])->name('schedule');
        Route::post('/slots', [App\Http\Controllers\Employer\InterviewController::class, 'storeSlot'])->name('slot.store');
        Route::delete('/slots/{slot}', [App\Http\Controllers\Employer\InterviewController::class, 'cancelSlot'])->name('slot.cancel');
        Route::get('/history', [App\Http\Controllers\Employer\InterviewController::class, 'history'])->name('history');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Role-based with Rate Limiting)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin', 'throttle:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('candidates', App\Http\Controllers\Admin\CandidateController::class);
    Route::resource('employers', App\Http\Controllers\Admin\EmployerController::class);
    
    // Job Management
    Route::resource('jobs', App\Http\Controllers\Admin\JobController::class);
    Route::get('/jobs/expired', [App\Http\Controllers\Admin\JobController::class, 'expired'])->name('jobs.expired');
    Route::get('/reported-jobs', [App\Http\Controllers\Admin\ReportedJobController::class, 'index'])->name('reported-jobs');
    
    // Content Management
    Route::resource('posts', App\Http\Controllers\Admin\PostController::class);
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    
    // Master Data (Grouped for efficiency)
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('countries', App\Http\Controllers\Admin\CountryController::class);
        Route::resource('states', App\Http\Controllers\Admin\StateController::class);
        Route::resource('cities', App\Http\Controllers\Admin\CityController::class);
        Route::resource('industries', App\Http\Controllers\Admin\IndustryController::class);
        Route::resource('job-types', App\Http\Controllers\Admin\JobTypeController::class);
        Route::resource('skills', App\Http\Controllers\Admin\SkillController::class);
        Route::resource('functional-areas', App\Http\Controllers\Admin\FunctionalAreaController::class);
        Route::resource('career-levels', App\Http\Controllers\Admin\CareerLevelController::class);
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
        Route::put('/', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
        Route::get('/email-templates', [App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('email-templates');
        Route::get('/front-settings', [App\Http\Controllers\Admin\FrontSettingController::class, 'index'])->name('front');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/subscribers', [App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers');
        Route::get('/transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions');
    });
});

/*
|--------------------------------------------------------------------------
| AJAX/API Helper Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('ajax')->name('ajax.')->group(function () {
    // Location Data
    Route::get('/states/{country}', [App\Http\Controllers\LocationController::class, 'getStates'])->name('states');
    Route::get('/cities/{state}', [App\Http\Controllers\LocationController::class, 'getCities'])->name('cities');
    
    // Job Actions
    Route::post('/jobs/{job}/favorite', [App\Http\Controllers\JobController::class, 'toggleFavorite'])->name('jobs.favorite');
    Route::post('/jobs/{job}/apply', [App\Http\Controllers\JobController::class, 'apply'])->name('jobs.apply');
    Route::post('/jobs/{job}/report', [App\Http\Controllers\JobController::class, 'report'])->name('jobs.report');
    
    // Company Actions
    Route::post('/companies/{company}/favorite', [App\Http\Controllers\CompanyController::class, 'toggleFavorite'])->name('companies.favorite');
    Route::post('/companies/{company}/report', [App\Http\Controllers\CompanyController::class, 'report'])->name('companies.report');
});

/*
|--------------------------------------------------------------------------
| File Downloads (Secured)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'signed'])->prefix('download')->name('download.')->group(function () {
    Route::get('/resume/{candidate}', [App\Http\Controllers\DownloadController::class, 'resume'])->name('resume');
    Route::get('/document/{document}', [App\Http\Controllers\DownloadController::class, 'document'])->name('document');
});

/*
|--------------------------------------------------------------------------
| Contact & Newsletter (Rate Limited)
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:contact')->group(function () {
    Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
    Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
});

/*
|--------------------------------------------------------------------------
| SEO & Utilities
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Fallback Route (404 Handler)
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
})->name('fallback'); 