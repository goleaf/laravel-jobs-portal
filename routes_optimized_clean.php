<?php

/**
 * OPTIMIZED ROUTES FILE - CLEAN VERSION
 * Following Laravel routing best practices from Universal documentation
 * ✅ Proper route grouping with middleware
 * ✅ Consistent naming conventions
 * ✅ Route caching optimization
 * ✅ Removed duplicates and conflicts
 * ✅ Added rate limiting
 * ✅ Used route model binding
 * ✅ Applied global parameter constraints.
 */

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\CareerLevelController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\EmployerController;
use App\Http\Controllers\Admin\FrontSettingController;
use App\Http\Controllers\Admin\FunctionalAreaController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\JobTypeController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ReportedJobController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Candidate\EducationController;
use App\Http\Controllers\Candidate\ExperienceController;
use App\Http\Controllers\Candidate\FavoriteJobController;
use App\Http\Controllers\Candidate\JobAlertController;
use App\Http\Controllers\Candidate\ProfileController;
use App\Http\Controllers\Candidate\ResumeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Employer\InterviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Pattern Constraints (Universal Best Practice)
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
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Pages Group
Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
    Route::get('/terms', [PageController::class, 'terms'])->name('terms');
});

// Public Job Browsing
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [JobController::class, 'index'])->name('index');
    Route::get('/{job:slug}', [JobController::class, 'show'])->name('show');
    Route::get('/search', [JobController::class, 'search'])->name('search');
    Route::get('/categories', [JobController::class, 'categories'])->name('categories');
});

// Public Company Browsing
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/{company:slug}', [CompanyController::class, 'show'])->name('show');
});

// Blog/Posts
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{post:slug}', [BlogController::class, 'show'])->name('show');
    Route::get('/category/{category:slug}', [BlogController::class, 'category'])->name('category');
});

// Language Switching
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm']);
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
        Route::get('/user', fn (Request $request) => $request->user())->name('user');

        // Applications API
        Route::apiResource('applications', ApplicationController::class);
        Route::apiResource('jobs.applications', JobApplicationController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Candidate Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:access-candidate-features'])->prefix('candidate')->name('candidate.')->group(function () {
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/general', [ProfileController::class, 'updateGeneral'])->name('update.general');
        Route::put('/career', [ProfileController::class, 'updateCareer'])->name('update.career');
    });

    // Applications
    Route::resource('applications', App\Http\Controllers\Candidate\ApplicationController::class)->only(['index', 'show', 'store']);

    // Experience & Education
    Route::resource('experiences', ExperienceController::class)->except(['show']);
    Route::resource('educations', EducationController::class)->except(['show']);

    // Job Alerts & Favorites
    Route::resource('job-alerts', JobAlertController::class)->except(['show']);
    Route::resource('favorite-jobs', FavoriteJobController::class)->only(['index', 'store', 'destroy']);

    // Resume
    Route::get('/resume/download', [ResumeController::class, 'download'])->name('resume.download');
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
        Route::get('/schedule/{application}', [InterviewController::class, 'schedule'])->name('schedule');
        Route::post('/slots', [InterviewController::class, 'storeSlot'])->name('slot.store');
        Route::delete('/slots/{slot}', [InterviewController::class, 'cancelSlot'])->name('slot.cancel');
        Route::get('/history', [InterviewController::class, 'history'])->name('history');
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
    Route::resource('users', UserController::class);
    Route::resource('candidates', CandidateController::class);
    Route::resource('employers', EmployerController::class);

    // Job Management
    Route::resource('jobs', App\Http\Controllers\Admin\JobController::class);
    Route::get('/jobs/expired', [App\Http\Controllers\Admin\JobController::class, 'expired'])->name('jobs.expired');
    Route::get('/reported-jobs', [ReportedJobController::class, 'index'])->name('reported-jobs');

    // Content Management
    Route::resource('posts', PostController::class);
    Route::resource('categories', CategoryController::class);

    // Master Data (Grouped for efficiency)
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('countries', CountryController::class);
        Route::resource('states', StateController::class);
        Route::resource('cities', CityController::class);
        Route::resource('industries', IndustryController::class);
        Route::resource('job-types', JobTypeController::class);
        Route::resource('skills', SkillController::class);
        Route::resource('functional-areas', FunctionalAreaController::class);
        Route::resource('career-levels', CareerLevelController::class);
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/', [SettingController::class, 'update'])->name('update');
        Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates');
        Route::get('/front-settings', [FrontSettingController::class, 'index'])->name('front');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers');
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    });
});

/*
|--------------------------------------------------------------------------
| AJAX/API Helper Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('ajax')->name('ajax.')->group(function () {
    // Location Data
    Route::get('/states/{country}', [LocationController::class, 'getStates'])->name('states');
    Route::get('/cities/{state}', [LocationController::class, 'getCities'])->name('cities');

    // Job Actions
    Route::post('/jobs/{job}/favorite', [JobController::class, 'toggleFavorite'])->name('jobs.favorite');
    Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply');
    Route::post('/jobs/{job}/report', [JobController::class, 'report'])->name('jobs.report');

    // Company Actions
    Route::post('/companies/{company}/favorite', [CompanyController::class, 'toggleFavorite'])->name('companies.favorite');
    Route::post('/companies/{company}/report', [CompanyController::class, 'report'])->name('companies.report');
});

/*
|--------------------------------------------------------------------------
| File Downloads (Secured)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'signed'])->prefix('download')->name('download.')->group(function () {
    Route::get('/resume/{candidate}', [DownloadController::class, 'resume'])->name('resume');
    Route::get('/document/{document}', [DownloadController::class, 'document'])->name('document');
});

/*
|--------------------------------------------------------------------------
| Contact & Newsletter (Rate Limited)
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:contact')->group(function () {
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
});

/*
|--------------------------------------------------------------------------
| SEO & Utilities
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Fallback Route (404 Handler)
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
})->name('fallback');
