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
| Enhanced Level 4 Complex System Transformation
| All routes now serve Vue3 SPA - Blade files removed
*/

// SPA Route - catch all routes and serve Vue3 app (MOVED TO BOTTOM)
Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Laravel is working!',
        'timestamp' => now(),
        'memory_usage' => memory_get_usage(true) / 1024 / 1024 .' MB',
    ]);
})->name('test');

/*
|--------------------------------------------------------------------------
| Locale/Language Routes
|--------------------------------------------------------------------------
|
| Routes for handling language switching and locale management
|
*/

Route::group(['prefix' => 'locale', 'as' => 'locale.'], function () {
    Route::post('switch', [LocaleController::class, 'switch'])->name('switch');
    Route::get('current', [LocaleController::class, 'current'])->name('current');
    Route::get('available', [LocaleController::class, 'available'])->name('available');
    Route::get('translations/{locale?}', [LocaleController::class, 'translations'])->name('translations');
    Route::post('clear-cache', [LocaleController::class, 'clearCache'])->name('clear-cache');
});

// Home route - basic test
Route::get('/', function () {
    return "Welcome to Job Portal - Basic Test";
})->name('home');

Route::get('/about-us', function () {
    return view('about');
})->name('about-us');

Route::get('/aboutus', function () {
    return view('aboutus.index');
})->name('aboutus.index');

Route::get('/contact', function () {
    return view('contact');
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

Route::get('/jobs', function () {
    return view('jobs.index');
})->name('jobs.index');

Route::get('/companies', function () {
    $companies = Company::with(['industry', 'companySize', 'ownerShipType'])->paginate(12);

    return view('companies.index', compact('companies'));
})->name('companies.index');

Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');

// Company management routes
Route::get('/company', [CompanyController::class, 'index'])->name('company.index');

// Additional routes that are commonly referenced in navigation
Route::get('/terms-and-conditions', function () {
    return view('terms_conditions');
})->name('terms.conditions.list');

Route::get('/privacy-policy-page', function () {
    return view('privacy_policy');
})->name('privacy.policy.list');

Route::get('/help', function () {
    return view('help.index');
})->name('help.index');

Route::get('/terms', function() {
    return view('terms.index');
})->name('terms');

Route::get('/privacy', function() {
    return view('privacy.index');
})->name('privacy');

Route::get('/posts', function () {
    return view('posts.index');
})->name('posts.index');

Route::get('/job-listing', function () {
    return view('jobs.index');
})->name('front.job.listing');

// Company management routes
Route::get('/company', [CompanyController::class, 'index'])->name('front.company.index');
Route::get('/company/index', [CompanyController::class, 'index'])->name('company.index');
Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');

// CRITICAL MISSING ROUTES - Adding routes that are referenced in blade files

// Admin Login route (referenced in auth_template/passwords/email.blade.php)
Route::get('admin/login', [AdminController::class, 'login'])->name('admin.login');

// Admin password forgot
Route::get('admin/password/forgot', [AdminController::class, 'forgotPassword'])->name('admin.password.forgot');

// Dashboard route (for both admin and company)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Candidate routes
Route::resource('candidate-profile', CandidateController::class)->except(['create', 'store', 'show', 'destroy']);
Route::post('candidate-profile/update-profile-image', [CandidateController::class, 'updateProfileImage'])->name('candidate.profile.update-image');

// Additional missing routes
Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
Route::get('/employers', [EmployerController::class, 'index'])->name('employers.index');

// Real-time validation
Route::post('/real-time-validation', [RealTimeController::class, 'validateField'])->name('real-time-validation');

// Location routes
Route::get('/get-states', [LocationController::class, 'getStates'])->name('get-states');
Route::get('/get-cities', [LocationController::class, 'getCities'])->name('get-cities');

// Transactions
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::post('transactions/store', [TransactionController::class, 'store'])->name('transactions.store');

// Job applications
Route::post('job-applications', [ApplicationController::class, 'store'])->name('job-applications.store');

// Subscriber routes
Route::post('subscribe', [SubscriberController::class, 'store'])->name('subscribe');
Route::get('unsubscribe/{token}', [SubscriberController::class, 'unsubscribe'])->name('unsubscribe');

// Language and Translation routes
Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
Route::get('translation-manager', [TranslationManagerController::class, 'index'])->name('translation-manager.index');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// DEMO routes for Habr articles
Route::get('/habr/views-demo/users', [HabrViewsDemoController::class, 'users'])->name('habr.views-demo.users');
Route::get('/habr/views-demo/jobs', [HabrViewsDemoController::class, 'jobs'])->name('habr.views-demo.jobs');
Route::get('/habr/views-demo/companies', [HabrViewsDemoController::class, 'companies'])->name('habr.views-demo.companies');

// Admin routes group
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Branding Sliders
    Route::resource('branding-sliders', BrandingSliderController::class);

    // Image Sliders
    Route::resource('image-sliders', ImageSliderController::class);

    // Header Sliders
    Route::resource('header-sliders', HeaderSliderController::class);

    // CMS
    Route::get('cms', [CmsController::class, 'index'])->name('cms.index');
    Route::post('cms', [CmsController::class, 'update'])->name('cms.update');

    // Email Templates
    Route::resource('email-templates', EmailTemplateController::class);

    // Master Data
    Route::resource('functional-areas', FunctionalAreaController::class);
    Route::resource('ownership-types', OwnershipTypeController::class);
    Route::resource('salary-currencies', SalaryCurrencyController::class);
    Route::resource('salary-periods', SalaryPeriodController::class);
    Route::resource('company-sizes', CompanySizeController::class);

    // Reported Jobs
    Route::resource('reported-jobs', ReportedJobController::class);

    // Taxonomies
    Route::resource('taxonomies', TaxonomyController::class);
    Route::resource('terms', TermController::class);

    // Blog Comments
    Route::get('blog-comments', [BlogCommentController::class, 'index'])->name('blog-comments.index');
    Route::delete('blog-comments/{id}', [BlogCommentController::class, 'destroy'])->name('blog-comments.destroy');
});

// All other routes should be handled by Vue Router
Route::get('/{any}', [HomeController::class, 'index'])->where('any', '.*');

Route::get('/employer-register', function () {
    return view('auth.employer_register');
})->name('employer.register');

Route::get('/candidate-register', function () {
    return view('auth.candidate_register');
})->name('candidate.register')->name('register');

Route::get('/employee-login', function () {
    return view('front_web.auth.employee_login');
})->name('employee.login');

Route::get('/login', function () {
    return view('front_web.auth.login');
})->name('login')->name('front.login');

Route::get('/blog-category/{categoryId}', function ($categoryId) {
    // ... existing code ...
});
