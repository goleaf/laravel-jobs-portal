<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Simple test route that doesn't require views
Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Laravel is working!',
        'timestamp' => now(),
        'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB'
    ]);
})->name('test');

Route::get('/', function () {
    return view('welcome');
})->name('front.home');

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->middleware('guest')->name('login.submit');

Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

Route::post('/register', function (Illuminate\Http\Request $request) {
    $request->validate([
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['nullable', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'phone' => ['nullable', 'string', 'max:20'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = App\Models\User::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user);

    return redirect('/dashboard');
})->middleware('guest');

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Password Reset Routes
Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->middleware('guest')->name('password.request');

Route::post('/password/email', function (Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);
    // Implement password reset logic here
    return back()->with('status', 'Password reset link sent to your email!');
})->middleware('guest')->name('password.email');

Route::get('/password/reset/{token}', function ($token) {
    return view('auth.passwords.reset', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/password/reset', function (Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);
    // Implement password reset logic here
    return redirect('/login')->with('status', 'Password has been reset!');
})->middleware('guest')->name('password.update');

Route::get('/about-us', function () {
    return view('about');
})->name('about-us');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/jobs', function () {
    return view('jobs.index');
})->name('jobs.index');

Route::get('/companies', function () {
    return view('companies.index');
})->name('companies.index');

// Company management routes
Route::get('/company', [App\Http\Controllers\CompanyController::class, 'index'])->name('company.index');
Route::get('/company/create', [App\Http\Controllers\CompanyController::class, 'create'])->name('company.create');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Candidate routes
    Route::prefix('candidate')->name('candidate.')->group(function () {
        Route::get('/profile', function () {
            return view('candidate.profile.index');
        })->name('profile');

        Route::get('/profile/edit', function () {
            return view('candidate.profile.general');
        })->name('profile.edit');

        Route::get('/applied-jobs', function () {
            return view('candidate.applied_job.index');
        })->name('applied-jobs');

        Route::get('/favorite-jobs', function () {
            return view('candidate.favourite_jobs.index');
        })->name('favorite-jobs');

        Route::get('/job-alerts', function () {
            return view('candidate.job_alert.index');
        })->name('job-alerts');
    });

    // Employer routes
    Route::prefix('employer')->name('employer.')->group(function () {
        Route::get('/company', function () {
            return view('employer.companies.edit');
        })->name('company');

        Route::get('/company/edit', function () {
            return view('employer.companies.edit');
        })->name('company.edit');

        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', function () {
                return view('employer.jobs.index');
            })->name('index');

            Route::get('/create', function () {
                return view('employer.jobs.create');
            })->name('create');

            Route::get('/applications', function () {
                return view('employer.job_applications.index');
            })->name('applications');
        });
    });

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        // Admin users management
        Route::resource('admin', App\Http\Controllers\AdminController::class);
        
        // Candidates management
        Route::resource('candidates', App\Http\Controllers\Web\CandidateController::class);
        
        // Jobs management
        Route::resource('jobs', App\Http\Controllers\Web\JobController::class);
        
        // Transactions management
        Route::resource('transactions', App\Http\Controllers\Web\TransactionController::class);
        
        // Subscribers management
        Route::resource('subscribers', App\Http\Controllers\SubscriberController::class)->only(['index', 'destroy']);
        
        // Additional admin routes that are referenced in Blade files
        Route::get('/reported-employers', function () {
            return view('admin.employers.reported');
        })->name('reported.companies');
        
        Route::get('/reported-candidates', function () {
            return view('admin.candidates.reported');
        })->name('reported.candidates');
        
        Route::get('/degree-levels', function () {
            return view('admin.degree_levels.index');
        })->name('requiredDegreeLevel.index');
        
        Route::get('/resumes', function () {
            return view('admin.resumes.index');
        })->name('resumes.index');
        
        Route::get('/selected-candidates', function () {
            return view('admin.selected_candidates.index');
        })->name('selected.candidate');
        
        Route::get('/job-categories', function () {
            return view('admin.job_categories.index');
        })->name('job-categories.index');
        
        Route::get('/job-types', function () {
            return view('admin.job_types.index');
        })->name('jobType.index');
        
        Route::get('/job-tags', function () {
            return view('admin.job_tags.index');
        })->name('jobTag.index');
        
        Route::get('/job-shifts', function () {
            return view('admin.job_shifts.index');
        })->name('jobShift.index');
        
        Route::get('/reported-jobs', function () {
            return view('admin.jobs.reported');
        })->name('reported.jobs');
        
        Route::get('/job-notification', function () {
            return view('admin.job_notification.index');
        })->name('job.notification.index');
        
        Route::get('/expired-jobs', function () {
            return view('admin.jobs.expired');
        })->name('expired.jobs.index');
        
        Route::get('/post-categories', function () {
            return view('admin.post_categories.index');
        })->name('post-categories.index');
        
        Route::get('/posts', function () {
            return view('admin.posts.index');
        })->name('posts.index');
        
        Route::get('/posts/create', function () {
            return view('admin.posts.create');
        })->name('posts.create');
        
        Route::get('/posts/{post}', function ($post) {
            return view('admin.posts.show');
        })->name('posts.show');
        
        Route::get('/posts/{post}/edit', function ($post) {
            return view('admin.posts.edit');
        })->name('posts.edit');
        
        Route::get('/post-comments', function () {
            return view('admin.post_comments.index');
        })->name('post.comments');
        
        Route::get('/plans', function () {
            return view('admin.plans.index');
        })->name('plans.index');
        
        Route::get('/countries', function () {
            return view('admin.countries.index');
        })->name('countries.index');
        
        Route::get('/states', function () {
            return view('admin.states.index');
        })->name('states.index');
        
        Route::get('/cities', function () {
            return view('admin.cities.index');
        })->name('cities.index');
        
        Route::get('/marital-status', function () {
            return view('admin.marital_status.index');
        })->name('maritalStatus.index');
        
        Route::get('/skills', function () {
            return view('admin.skills.index');
        })->name('skills.index');
        
        Route::get('/salary-periods', function () {
            return view('admin.salary_periods.index');
        })->name('salaryPeriod.index');
        
        Route::get('/industries', function () {
            return view('admin.industries.index');
        })->name('industry.index');
        
        Route::get('/company-sizes', function () {
            return view('admin.company_sizes.index');
        })->name('companySize.index');
        
        Route::get('/functional-areas', function () {
            return view('admin.functional_areas.index');
        })->name('functionalArea.index');
        
        Route::get('/career-levels', function () {
            return view('admin.career_levels.index');
        })->name('careerLevel.index');
        
        Route::get('/salary-currencies', function () {
            return view('admin.salary_currencies.index');
        })->name('salaryCurrency.index');
        
        Route::get('/ownership-types', function () {
            return view('admin.ownership_types.index');
        })->name('ownerShipType.index');
        
        Route::get('/languages', function () {
            return view('admin.languages.index');
        })->name('languages.index');
        
        Route::get('/noticeboards', function () {
            return view('admin.noticeboards.index');
        })->name('noticeboards.index');
        
        Route::get('/faqs', function () {
            return view('admin.faqs.index');
        })->name('faqs.index');
        
        Route::get('/inquires', function () {
            return view('admin.inquires.index');
        })->name('inquires.index');
        
        Route::get('/inquires/{inquire}', function ($inquire) {
            return view('admin.inquires.show');
        })->name('inquires.show');
        
        Route::get('/notification-settings', function () {
            return view('admin.notification_settings.index');
        })->name('notification.settings.index');
        
        Route::get('/privacy-policy', function () {
            return view('admin.privacy_policy.index');
        })->name('privacy.policy.index');
        
        Route::get('/front-settings', function () {
            return view('admin.front_settings.index');
        })->name('front.settings.index');
        
        Route::get('/email-template', function () {
            return view('admin.email_templates.index');
        })->name('email.template.index');
        
        Route::get('/settings', function () {
            return view('admin.settings.index');
        })->name('settings.index');
        
        Route::get('/testimonials', function () {
            return view('admin.testimonials.index');
        })->name('testimonials.index');
        
        Route::get('/branding-sliders', function () {
            return view('admin.branding_sliders.index');
        })->name('branding.sliders.index');
        
        Route::get('/header-sliders', function () {
            return view('admin.header_sliders.index');
        })->name('header.sliders.index');
        
        Route::get('/image-sliders', function () {
            return view('admin.image_sliders.index');
        })->name('image-sliders.index');
        
        Route::get('/cms-services', function () {
            return view('admin.cms_services.index');
        })->name('cms.services.index');
        
        Route::get('/cms-about-us', function () {
            return view('admin.cms_about_us.index');
        })->name('cms.about-us.service');
    });
});

// Job and company detail pages
Route::get('/jobs/{id}', function ($id) {
    return view('jobs.show');
})->name('jobs.show');

Route::get('/company/{company}', [App\Http\Controllers\CompanyController::class, 'show'])->name('company.show');
Route::get('/company/{company}/edit', [App\Http\Controllers\CompanyController::class, 'edit'])->name('company.edit');

// Terms and privacy pages
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-conditions', function () {
    return view('terms-conditions');
})->name('terms-conditions');

// Front-end routes (for front_web templates)
Route::prefix('front')->name('front.')->group(function () {
    Route::get('/search-jobs', function () {
        // Provide placeholder data for the view
        $data = [
            'jobCategories' => collect([]),
            'jobSkills' => collect([]),
            'genders' => collect([]),
            'functionalAreas' => collect([]),
            'careerLevels' => collect([]),
            'jobTypes' => collect([]),
            'advertise_image' => (object)['value' => asset('front_web/images/job-img.png')],
            'input' => []
        ];
        return view('front_web.jobs.index', $data);
    })->name('search.jobs');
    
    Route::get('/company-lists', function () {
        return view('front_web.companies.index');
    })->name('company.lists');
    
    Route::get('/candidate-lists', function () {
        return view('front_web.candidates.index');
    })->name('candidate.lists');
    
    Route::get('/about-us', function () {
        return view('front_web.about_us.index');
    })->name('about.us');
    
    Route::get('/contact-us', function () {
        return view('front_web.contact.index');
    })->name('contact');
    
    Route::get('/posts', function () {
        return view('front_web.blogs.index');
    })->name('post.lists');
    
    Route::get('/posts/{post}', function ($post) {
        return view('front_web.blogs.show');
    })->name('posts.details');
    
    Route::get('/job-details/{jobId}', function ($jobId) {
        return view('front_web.jobs.show');
    })->name('job.details');
    
    Route::get('/company-details/{companyId}', function ($companyId) {
        return view('front_web.companies.show');
    })->name('company.details');
    
    Route::get('/candidate-details/{candidateId}', function ($candidateId) {
        return view('front_web.candidates.show');
    })->name('candidate.details');
});

// Additional utility routes
Route::get('/language/{locale}', function ($locale) {
    session(['locale' => $locale]);
    return redirect()->back();
})->name('language.change');

Route::get('/download/image/{id}', function ($id) {
    return response()->download(storage_path('app/public/testimonials/' . $id . '.jpg'));
})->name('download.image');

// Email verification routes (referenced in auth templates)
Route::post('/email/verification-notification', function () {
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// Password confirmation (referenced in auth templates)
Route::post('/password/confirm', function () {
    return redirect()->intended();
})->middleware('auth')->name('password.confirm');

// Pricing and payment routes (referenced in pricing views)
Route::get('/payment-method/{planId}', function ($planId) {
    return view('pricing.payment_methods');
})->name('payment-method-screen');

Route::get('/paypal-payment/{planId}', function ($planId) {
    return redirect()->away('https://paypal.com');
})->name('paypal-payment');

Route::get('/manually-payment/{planId}', function ($planId) {
    return view('pricing.manual_payment');
})->name('manually-payment');

Route::get('/paystack-payment/{planId}', function ($planId) {
    return redirect()->away('https://paystack.com');
})->name('paystack.payment');

// Component documentation routes (referenced in navigation)
Route::get('/components/icon-documentation', function () {
    return view('components.icon-documentation');
})->name('components.icon-documentation');

Route::get('/icons/documentation', function () {
    return view('icons.documentation');
})->name('icons.documentation');
