<?php

/**
 * OPTIMIZED ROUTES FILE
 * Following Laravel routing best practices from Context7 documentation
 * - Proper route grouping with middleware
 * - Consistent naming conventions
 * - Route caching optimization
 * - Removed duplicates and conflicts
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Route Pattern Constraints (Context7 Best Practice)
|--------------------------------------------------------------------------
| Define global parameter constraints for better performance and security
*/
Route::pattern('id', '[0-9]+');
Route::pattern('token', '[a-zA-Z0-9]+');
Route::pattern('locale', '[a-z]{2}');

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Landing Page Routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Static Pages
Route::group(['as' => 'front.'], function () {
    Route::get('/about-us', [App\Http\Controllers\Front\PageController::class, 'about'])->name('about');
    Route::get('/contact', [App\Http\Controllers\Front\ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [App\Http\Controllers\Front\ContactController::class, 'store'])->name('contact.store');
    Route::get('/privacy-policy', [App\Http\Controllers\Front\PageController::class, 'privacy'])->name('privacy');
    Route::get('/terms-conditions', [App\Http\Controllers\Front\PageController::class, 'terms'])->name('terms');
});

// Job Browsing (Public)
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [App\Http\Controllers\Front\JobController::class, 'index'])->name('index');
    Route::get('/{job:slug}', [App\Http\Controllers\Front\JobController::class, 'show'])->name('show');
    Route::get('/search', [App\Http\Controllers\Front\JobController::class, 'search'])->name('search');
    Route::get('/categories', [App\Http\Controllers\Front\JobController::class, 'categories'])->name('categories');
});

// Company Browsing (Public)
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [App\Http\Controllers\Front\CompanyController::class, 'index'])->name('index');
    Route::get('/{company:slug}', [App\Http\Controllers\Front\CompanyController::class, 'show'])->name('show');
});

// Blog/Posts (Public)
Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/', [App\Http\Controllers\Front\PostController::class, 'index'])->name('index');
    Route::get('/{post:slug}', [App\Http\Controllers\Front\PostController::class, 'show'])->name('show');
});

// Language Switching
Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])
    ->whereIn('locale', ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'])
    ->name('language.switch');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Laravel/UI)
|--------------------------------------------------------------------------
*/

// Guest Routes (Login/Register)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');
    
    // Registration
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.submit');
    
    // Candidate Registration
    Route::get('/candidate-register', [App\Http\Controllers\Auth\CandidateRegisterController::class, 'show'])->name('candidate.register');
    Route::post('/candidate-register', [App\Http\Controllers\Auth\CandidateRegisterController::class, 'register'])->name('candidate.register.submit');
    
    // Employer Registration
    Route::get('/employer-register', [App\Http\Controllers\Auth\EmployerRegisterController::class, 'show'])->name('employer.register');
    Route::post('/employer-register', [App\Http\Controllers\Auth\EmployerRegisterController::class, 'register'])->name('employer.register.submit');
    
    // Password Reset
    Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    
    // Password Confirmation
    Route::get('/password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    Route::post('/password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'confirm'])->name('password.confirm.submit');
});

/*
|--------------------------------------------------------------------------
| API Routes (Rate Limited)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['api', 'throttle:api'])->group(function () {
    // Public API
    Route::get('/jobs', [App\Http\Controllers\Api\JobController::class, 'index'])->name('api.jobs.index');
    Route::get('/companies', [App\Http\Controllers\Api\CompanyController::class, 'index'])->name('api.companies.index');
    
    // Authenticated API
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Illuminate\Http\Request $request) {
            return $request->user();
        })->name('api.user');
        
        // Job Applications API
        Route::prefix('applications')->name('api.applications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\ApplicationController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Api\ApplicationController::class, 'store'])->name('store');
            Route::get('/{application}', [App\Http\Controllers\Api\ApplicationController::class, 'show'])->name('show');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Real-time Dashboard
    Route::prefix('realtime')->name('realtime.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\RealTimeController::class, 'getDashboardData'])->name('dashboard');
        Route::get('/stats', [App\Http\Controllers\RealTimeController::class, 'getRealTimeStats'])->name('stats');
        Route::get('/activity', [App\Http\Controllers\RealTimeController::class, 'getActivityFeed'])->name('activity');
        Route::get('/websocket-auth', [App\Http\Controllers\RealTimeController::class, 'getWebSocketAuth'])->name('websocket.auth');
    });
});

/*
|--------------------------------------------------------------------------
| Candidate Routes (Role-based Access)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:candidate'])->prefix('candidate')->name('candidate.')->group(function () {
    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\Candidate\ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\Candidate\ProfileController::class, 'edit'])->name('edit');
        Route::put('/general', [App\Http\Controllers\Candidate\ProfileController::class, 'updateGeneral'])->name('update.general');
        Route::put('/online', [App\Http\Controllers\Candidate\ProfileController::class, 'updateOnline'])->name('update.online');
    });
    
    // Job Applications
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Candidate\ApplicationController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Candidate\ApplicationController::class, 'store'])->name('store');
        Route::get('/{application}', [App\Http\Controllers\Candidate\ApplicationController::class, 'show'])->name('show');
    });
    
    // Experience & Education
    Route::prefix('experience')->name('experience.')->group(function () {
        Route::get('/create', [App\Http\Controllers\Candidate\ExperienceController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Candidate\ExperienceController::class, 'store'])->name('store');
    });
    
    Route::prefix('education')->name('education.')->group(function () {
        Route::get('/create', [App\Http\Controllers\Candidate\EducationController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Candidate\EducationController::class, 'store'])->name('store');
    });
    
    // Job Alerts & Favorites
    Route::get('/job-alerts', [App\Http\Controllers\Candidate\JobAlertController::class, 'index'])->name('job.alerts');
    Route::post('/job-alerts', [App\Http\Controllers\Candidate\JobAlertController::class, 'store'])->name('job.alerts.store');
    Route::get('/favorite-jobs', [App\Http\Controllers\Candidate\FavoriteJobController::class, 'index'])->name('favorite.jobs');
    
    // Resume Management
    Route::get('/download-resume/{resume}', [App\Http\Controllers\Candidate\ResumeController::class, 'download'])->name('resume.download');
});

/*
|--------------------------------------------------------------------------
| Employer Routes (Role-based Access)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    // Company Management
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/', [App\Http\Controllers\Employer\CompanyController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\Employer\CompanyController::class, 'edit'])->name('edit');
        Route::put('/', [App\Http\Controllers\Employer\CompanyController::class, 'update'])->name('update');
    });
    
    // Job Management
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [App\Http\Controllers\Employer\JobController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Employer\JobController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Employer\JobController::class, 'store'])->name('store');
        Route::get('/{job}/edit', [App\Http\Controllers\Employer\JobController::class, 'edit'])->name('edit');
        Route::put('/{job}', [App\Http\Controllers\Employer\JobController::class, 'update'])->name('update');
        Route::delete('/{job}', [App\Http\Controllers\Employer\JobController::class, 'destroy'])->name('destroy');
    });
    
    // Application Management
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Employer\ApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [App\Http\Controllers\Employer\ApplicationController::class, 'show'])->name('show');
        Route::post('/{application}/status', [App\Http\Controllers\Employer\ApplicationController::class, 'updateStatus'])->name('update.status');
    });
    
    // Interview Scheduling
    Route::prefix('interviews')->name('interviews.')->group(function () {
        Route::get('/schedule/{application}', [App\Http\Controllers\Employer\InterviewController::class, 'schedule'])->name('schedule');
        Route::post('/slots', [App\Http\Controllers\Employer\InterviewController::class, 'storeSlot'])->name('slot.store');
        Route::post('/batch-slots', [App\Http\Controllers\Employer\InterviewController::class, 'storeBatchSlots'])->name('batch.slot.store');
        Route::delete('/slots/{slot}', [App\Http\Controllers\Employer\InterviewController::class, 'cancelSlot'])->name('slot.cancel');
        Route::get('/history', [App\Http\Controllers\Employer\InterviewController::class, 'history'])->name('history');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Role-based Access with Rate Limiting)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin', 'throttle:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('candidates', App\Http\Controllers\Admin\CandidateController::class);
    Route::resource('employers', App\Http\Controllers\Admin\EmployerController::class);
    Route::resource('admins', App\Http\Controllers\Admin\AdminController::class);
    
    // Job Management
    Route::resource('jobs', App\Http\Controllers\Admin\JobController::class);
    Route::get('/jobs/expired', [App\Http\Controllers\Admin\JobController::class, 'expired'])->name('jobs.expired');
    Route::get('/reported-jobs', [App\Http\Controllers\Admin\ReportedJobController::class, 'index'])->name('reported.jobs');
    
    // Content Management
    Route::resource('posts', App\Http\Controllers\Admin\PostController::class);
    Route::resource('post-categories', App\Http\Controllers\Admin\PostCategoryController::class);
    Route::get('/post-comments', [App\Http\Controllers\Admin\PostCommentController::class, 'index'])->name('post.comments');
    
    // Master Data Management
    Route::prefix('master-data')->name('master.')->group(function () {
        Route::resource('countries', App\Http\Controllers\Admin\CountryController::class);
        Route::resource('states', App\Http\Controllers\Admin\StateController::class);
        Route::resource('cities', App\Http\Controllers\Admin\CityController::class);
        Route::resource('industries', App\Http\Controllers\Admin\IndustryController::class);
        Route::resource('job-categories', App\Http\Controllers\Admin\JobCategoryController::class);
        Route::resource('job-types', App\Http\Controllers\Admin\JobTypeController::class);
        Route::resource('job-shifts', App\Http\Controllers\Admin\JobShiftController::class);
        Route::resource('job-tags', App\Http\Controllers\Admin\JobTagController::class);
        Route::resource('skills', App\Http\Controllers\Admin\SkillController::class);
        Route::resource('functional-areas', App\Http\Controllers\Admin\FunctionalAreaController::class);
        Route::resource('career-levels', App\Http\Controllers\Admin\CareerLevelController::class);
        Route::resource('degree-levels', App\Http\Controllers\Admin\DegreeLevelController::class);
        Route::resource('company-sizes', App\Http\Controllers\Admin\CompanySizeController::class);
        Route::resource('ownership-types', App\Http\Controllers\Admin\OwnershipTypeController::class);
        Route::resource('salary-currencies', App\Http\Controllers\Admin\SalaryCurrencyController::class);
        Route::resource('salary-periods', App\Http\Controllers\Admin\SalaryPeriodController::class);
        Route::resource('marital-statuses', App\Http\Controllers\Admin\MaritalStatusController::class);
    });
    
    // CMS Management
    Route::prefix('cms')->name('cms.')->group(function () {
        Route::resource('sliders', App\Http\Controllers\Admin\SliderController::class);
        Route::resource('testimonials', App\Http\Controllers\Admin\TestimonialController::class);
        Route::resource('faqs', App\Http\Controllers\Admin\FaqController::class);
        Route::get('/front-settings', [App\Http\Controllers\Admin\FrontSettingController::class, 'index'])->name('front.settings');
        Route::put('/front-settings', [App\Http\Controllers\Admin\FrontSettingController::class, 'update'])->name('front.settings.update');
    });
    
    // Email Templates
    Route::prefix('email-templates')->name('email.templates.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('index');
        Route::get('/{template}/edit', [App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('update');
    });
    
    // Settings & Configuration
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
        Route::put('/', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
        Route::get('/notification', [App\Http\Controllers\Admin\NotificationSettingController::class, 'index'])->name('notification');
    });
    
    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/subscribers', [App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers');
        Route::get('/transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions');
        Route::get('/inquiries', [App\Http\Controllers\Admin\InquiryController::class, 'index'])->name('inquiries');
    });
    
    // Translation Management
    Route::get('/translations', [App\Http\Controllers\Admin\TranslationController::class, 'index'])->name('translations');
});

/*
|--------------------------------------------------------------------------
| AJAX/API Helper Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Location Data
    Route::get('/api/states/{country}', [App\Http\Controllers\LocationController::class, 'getStates'])->name('api.states');
    Route::get('/api/cities/{state}', [App\Http\Controllers\LocationController::class, 'getCities'])->name('api.cities');
    
    // Job Actions
    Route::post('/api/jobs/{job}/favorite', [App\Http\Controllers\JobController::class, 'toggleFavorite'])->name('api.jobs.favorite');
    Route::post('/api/jobs/{job}/apply', [App\Http\Controllers\JobController::class, 'apply'])->name('api.jobs.apply');
    Route::post('/api/jobs/{job}/report', [App\Http\Controllers\JobController::class, 'report'])->name('api.jobs.report');
    
    // Company Actions
    Route::post('/api/companies/{company}/favorite', [App\Http\Controllers\CompanyController::class, 'toggleFavorite'])->name('api.companies.favorite');
    Route::post('/api/companies/{company}/report', [App\Http\Controllers\CompanyController::class, 'report'])->name('api.companies.report');
    
    // File Downloads
    Route::get('/download/resume/{candidate}', [App\Http\Controllers\DownloadController::class, 'resume'])->name('download.resume');
    Route::get('/download/document/{document}', [App\Http\Controllers\DownloadController::class, 'document'])->name('download.document');
});

/*
|--------------------------------------------------------------------------
| Newsletter & Contact Forms (Rate Limited)
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:contact')->group(function () {
    Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::post('/contact/send', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');
});

/*
|--------------------------------------------------------------------------
| SEO & Sitemap Routes
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Fallback Route (Must be last)
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
})->name('fallback');

/*
|--------------------------------------------------------------------------
| Custom Rate Limiters (Context7 Best Practice)
|--------------------------------------------------------------------------
*/

// This would be defined in RouteServiceProvider boot method:
/*
RateLimiter::for('admin', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('contact', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

RateLimiter::for('api', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(100)->by($request->user()->id)
        : Limit::perMinute(20)->by($request->ip());
});
*/ 