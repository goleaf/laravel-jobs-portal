<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

/*
|--------------------------------------------------------------------------
| Universal Optimized Web Routes
|--------------------------------------------------------------------------
| Modern Laravel routing with Universal patterns for performance & security
*/

// Universal Public Routes (Cached for Performance)
Route::middleware(['cache.headers:public;max_age=3600,etag'])->group(function () {
    Route::get('/', [App\Http\Controllers\Front\HomeController::class, 'index'])->name('front.home');
    Route::get('/about-us', function () { return view('front.about'); })->name('about');
    Route::get('/contact', [App\Http\Controllers\Front\ContactController::class, 'index'])->name('contact');
    Route::get('/privacy-policy', function () { return view('front.privacy'); })->name('privacy');
    Route::get('/terms-conditions', function () { return view('front.terms'); })->name('terms');
});

// Universal Job Browsing (Public with Light Caching)
Route::middleware(['cache.headers:public;max_age=600'])->prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [App\Http\Controllers\Front\JobController::class, 'index'])->name('index');
    Route::get('/{job:slug}', [App\Http\Controllers\Front\JobController::class, 'show'])->name('show');
    Route::get('/category/{category:slug}', [App\Http\Controllers\Front\JobController::class, 'category'])->name('category');
    Route::get('/company/{company:slug}', [App\Http\Controllers\Front\JobController::class, 'company'])->name('company');
});

// Universal Authentication Routes (Rate Limited)
Route::middleware(['throttle:auth'])->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

// Universal Candidate Protected Routes
Route::middleware(['auth', 'verified', 'role:candidate'])->prefix('candidate')->name('candidate.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Candidate\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('profile', App\Http\Controllers\Candidate\ProfileController::class)->except(['index', 'destroy']);
    Route::resource('applications', App\Http\Controllers\Candidate\ApplicationController::class)->only(['index', 'show', 'destroy']);
    Route::resource('resumes', App\Http\Controllers\Candidate\ResumeController::class);
    
    // Job Application with Security
    Route::post('/jobs/{job}/apply', [App\Http\Controllers\Candidate\ApplicationController::class, 'store'])
         ->middleware('signed')
         ->name('jobs.apply');
});

// Universal Employer Protected Routes  
Route::middleware(['auth', 'verified', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Employer\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('jobs', App\Http\Controllers\Employer\JobController::class);
    Route::resource('company', App\Http\Controllers\Employer\CompanyController::class)->except(['index', 'create', 'store']);
    Route::resource('applications', App\Http\Controllers\Employer\ApplicationController::class)->only(['index', 'show', 'update']);
});

// Universal Admin Routes (Maximum Security)
Route::middleware(['auth', 'verified', 'role:admin', 'throttle:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('jobs', App\Http\Controllers\Admin\JobController::class);
    Route::resource('companies', App\Http\Controllers\Admin\CompanyController::class);
    Route::resource('candidates', App\Http\Controllers\Admin\CandidateController::class);
    Route::resource('employers', App\Http\Controllers\Admin\EmployerController::class);
    Route::resource('job-categories', App\Http\Controllers\Admin\JobCategoryController::class);
    Route::resource('skills', App\Http\Controllers\Admin\SkillController::class);
});

// Universal API Routes (Separate file for better organization)
// These would go in routes/api.php
