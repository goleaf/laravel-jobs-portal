<?php

/**
 * OPTIMIZED ROUTES FILE
 * Following Laravel routing best practices from Universal documentation
 * - Proper route grouping with middleware
 * - Consistent naming conventions
 * - Route caching optimization
 * - Removed duplicates and conflicts.
 */

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\CareerLevelController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanySizeController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DegreeLevelController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\EmployerController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FrontSettingController;
use App\Http\Controllers\Admin\FunctionalAreaController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\JobCategoryController;
use App\Http\Controllers\Admin\JobShiftController;
use App\Http\Controllers\Admin\JobTagController;
use App\Http\Controllers\Admin\JobTypeController;
use App\Http\Controllers\Admin\MaritalStatusController;
use App\Http\Controllers\Admin\NotificationSettingController;
use App\Http\Controllers\Admin\OwnershipTypeController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\PostCommentController;
use App\Http\Controllers\Admin\ReportedJobController;
use App\Http\Controllers\Admin\SalaryCurrencyController;
use App\Http\Controllers\Admin\SalaryPeriodController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Auth\CandidateRegisterController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\EmployerRegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RealTimeController;
use App\Http\Controllers\SitemapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Pattern Constraints (Universal Best Practice)
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
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Pages
Route::group(['as' => 'front.'], function () {
    Route::get('/about-us', [PageController::class, 'about'])->name('about');
    Route::get('/contact', [App\Http\Controllers\Front\ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [App\Http\Controllers\Front\ContactController::class, 'store'])->name('contact.store');
    Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
    Route::get('/terms-conditions', [PageController::class, 'terms'])->name('terms');
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
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{post:slug}', [PostController::class, 'show'])->name('show');
});

// Language Switching
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->whereIn('locale', ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'])
    ->name('language.switch')
;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Laravel/UI)
|--------------------------------------------------------------------------
*/

// Guest Routes (Login/Register)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

    // Candidate Registration
    Route::get('/candidate-register', [CandidateRegisterController::class, 'show'])->name('candidate.register');
    Route::post('/candidate-register', [CandidateRegisterController::class, 'register'])->name('candidate.register.submit');

    // Employer Registration
    Route::get('/employer-register', [EmployerRegisterController::class, 'show'])->name('employer.register');
    Route::post('/employer-register', [EmployerRegisterController::class, 'register'])->name('employer.register.submit');

    // Password Reset
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Password Confirmation
    Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm'])->name('password.confirm.submit');
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
        Route::get('/user', function (Request $request) {
            return $request->user();
        })->name('api.user');

        // Job Applications API
        Route::prefix('applications')->name('api.applications.')->group(function () {
            Route::get('/', [ApplicationController::class, 'index'])->name('index');
            Route::post('/', [ApplicationController::class, 'store'])->name('store');
            Route::get('/{application}', [ApplicationController::class, 'show'])->name('show');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Real-time Dashboard
    Route::prefix('realtime')->name('realtime.')->group(function () {
        Route::get('/dashboard', [RealTimeController::class, 'getDashboardData'])->name('dashboard');
        Route::get('/stats', [RealTimeController::class, 'getRealTimeStats'])->name('stats');
        Route::get('/activity', [RealTimeController::class, 'getActivityFeed'])->name('activity');
        Route::get('/websocket-auth', [RealTimeController::class, 'getWebSocketAuth'])->name('websocket.auth');
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
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/general', [ProfileController::class, 'updateGeneral'])->name('update.general');
        Route::put('/online', [ProfileController::class, 'updateOnline'])->name('update.online');
    });

    // Job Applications
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Candidate\ApplicationController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Candidate\ApplicationController::class, 'store'])->name('store');
        Route::get('/{application}', [App\Http\Controllers\Candidate\ApplicationController::class, 'show'])->name('show');
    });

    // Experience & Education
    Route::prefix('experience')->name('experience.')->group(function () {
        Route::get('/create', [ExperienceController::class, 'create'])->name('create');
        Route::post('/', [ExperienceController::class, 'store'])->name('store');
    });

    Route::prefix('education')->name('education.')->group(function () {
        Route::get('/create', [EducationController::class, 'create'])->name('create');
        Route::post('/', [EducationController::class, 'store'])->name('store');
    });

    // Job Alerts & Favorites
    Route::get('/job-alerts', [JobAlertController::class, 'index'])->name('job.alerts');
    Route::post('/job-alerts', [JobAlertController::class, 'store'])->name('job.alerts.store');
    Route::get('/favorite-jobs', [FavoriteJobController::class, 'index'])->name('favorite.jobs');

    // Resume Management
    Route::get('/download-resume/{resume}', [ResumeController::class, 'download'])->name('resume.download');
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
        Route::get('/schedule/{application}', [InterviewController::class, 'schedule'])->name('schedule');
        Route::post('/slots', [InterviewController::class, 'storeSlot'])->name('slot.store');
        Route::post('/batch-slots', [InterviewController::class, 'storeBatchSlots'])->name('batch.slot.store');
        Route::delete('/slots/{slot}', [InterviewController::class, 'cancelSlot'])->name('slot.cancel');
        Route::get('/history', [InterviewController::class, 'history'])->name('history');
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
    Route::resource('users', UserController::class);
    Route::resource('candidates', CandidateController::class);
    Route::resource('employers', EmployerController::class);
    Route::resource('admins', AdminController::class);

    // Job Management
    Route::resource('jobs', App\Http\Controllers\Admin\JobController::class);
    Route::get('/jobs/expired', [App\Http\Controllers\Admin\JobController::class, 'expired'])->name('jobs.expired');
    Route::get('/reported-jobs', [ReportedJobController::class, 'index'])->name('reported.jobs');

    // Content Management
    Route::resource('posts', App\Http\Controllers\Admin\PostController::class);
    Route::resource('post-categories', PostCategoryController::class);
    Route::get('/post-comments', [PostCommentController::class, 'index'])->name('post.comments');

    // Master Data Management
    Route::prefix('master-data')->name('master.')->group(function () {
        Route::resource('countries', CountryController::class);
        Route::resource('states', StateController::class);
        Route::resource('cities', CityController::class);
        Route::resource('industries', IndustryController::class);
        Route::resource('job-categories', JobCategoryController::class);
        Route::resource('job-types', JobTypeController::class);
        Route::resource('job-shifts', JobShiftController::class);
        Route::resource('job-tags', JobTagController::class);
        Route::resource('skills', SkillController::class);
        Route::resource('functional-areas', FunctionalAreaController::class);
        Route::resource('career-levels', CareerLevelController::class);
        Route::resource('degree-levels', DegreeLevelController::class);
        Route::resource('company-sizes', CompanySizeController::class);
        Route::resource('ownership-types', OwnershipTypeController::class);
        Route::resource('salary-currencies', SalaryCurrencyController::class);
        Route::resource('salary-periods', SalaryPeriodController::class);
        Route::resource('marital-statuses', MaritalStatusController::class);
    });

    // CMS Management
    Route::prefix('cms')->name('cms.')->group(function () {
        Route::resource('sliders', SliderController::class);
        Route::resource('testimonials', TestimonialController::class);
        Route::resource('faqs', FaqController::class);
        Route::get('/front-settings', [FrontSettingController::class, 'index'])->name('front.settings');
        Route::put('/front-settings', [FrontSettingController::class, 'update'])->name('front.settings.update');
    });

    // Email Templates
    Route::prefix('email-templates')->name('email.templates.')->group(function () {
        Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
        Route::get('/{template}/edit', [EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [EmailTemplateController::class, 'update'])->name('update');
    });

    // Settings & Configuration
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/', [SettingController::class, 'update'])->name('update');
        Route::get('/notification', [NotificationSettingController::class, 'index'])->name('notification');
    });

    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers');
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
        Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries');
    });

    // Translation Management
    Route::get('/translations', [TranslationController::class, 'index'])->name('translations');
});

/*
|--------------------------------------------------------------------------
| AJAX/API Helper Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Location Data
    Route::get('/api/states/{country}', [LocationController::class, 'getStates'])->name('api.states');
    Route::get('/api/cities/{state}', [LocationController::class, 'getCities'])->name('api.cities');

    // Job Actions
    Route::post('/api/jobs/{job}/favorite', [JobController::class, 'toggleFavorite'])->name('api.jobs.favorite');
    Route::post('/api/jobs/{job}/apply', [JobController::class, 'apply'])->name('api.jobs.apply');
    Route::post('/api/jobs/{job}/report', [JobController::class, 'report'])->name('api.jobs.report');

    // Company Actions
    Route::post('/api/companies/{company}/favorite', [CompanyController::class, 'toggleFavorite'])->name('api.companies.favorite');
    Route::post('/api/companies/{company}/report', [CompanyController::class, 'report'])->name('api.companies.report');

    // File Downloads
    Route::get('/download/resume/{candidate}', [DownloadController::class, 'resume'])->name('download.resume');
    Route::get('/download/document/{document}', [DownloadController::class, 'document'])->name('download.document');
});

/*
|--------------------------------------------------------------------------
| Newsletter & Contact Forms (Rate Limited)
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:contact')->group(function () {
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
});

/*
|--------------------------------------------------------------------------
| SEO & Sitemap Routes
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

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
| Custom Rate Limiters (Universal Best Practice)
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
