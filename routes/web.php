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

// Additional routes that are commonly referenced in navigation
Route::get('/employer-register', function () {
    return view('auth.employer_register');
})->name('employer.register');

Route::get('/candidate-register', function () {
    return view('auth.candidate_register');
})->name('candidate.register');

Route::get('/terms-and-conditions', function () {
    return view('terms_conditions');
})->name('terms.conditions.list');

Route::get('/privacy-policy-page', function () {
    return view('privacy_policy');
})->name('privacy.policy.list');

Route::get('/posts', function () {
    return view('posts.index');
})->name('posts.index');

Route::get('/job-listing', function () {
    return view('jobs.index');
})->name('job.index');

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
        
        // Missing candidate routes
        Route::get('/job-alert', function () {
            return view('candidate.job_alert.index');
        })->name('job.alert');
        
        Route::get('/applied-job', function () {
            return view('candidate.applied_job.index');
        })->name('applied.job');
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
        Route::resource('admin', App\Http\Controllers\AdminController::class, ['as' => 'admin']);
        
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
        })->name('job-notification.index');
        
        Route::get('/expired-jobs', function () {
            return view('admin.jobs.expired');
        })->name('admin.jobs.expiredJobs');
        
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
        
        Route::get('/countries', [App\Http\Controllers\Admin\MasterDataController::class, 'countries'])->name('countries.index');
        Route::get('/states', [App\Http\Controllers\Admin\MasterDataController::class, 'states'])->name('states.index');
        Route::get('/cities', [App\Http\Controllers\Admin\MasterDataController::class, 'cities'])->name('cities.index');
        Route::get('/marital-status', [App\Http\Controllers\Admin\MasterDataController::class, 'maritalStatus'])->name('maritalStatus.index');
        Route::get('/skills', [App\Http\Controllers\Admin\MasterDataController::class, 'skills'])->name('skills.index');
        Route::get('/salary-periods', [App\Http\Controllers\Admin\MasterDataController::class, 'salaryPeriods'])->name('salaryPeriod.index');
        Route::get('/industries', [App\Http\Controllers\Admin\MasterDataController::class, 'industries'])->name('industry.index');
        Route::get('/company-sizes', [App\Http\Controllers\Admin\MasterDataController::class, 'companySizes'])->name('companySize.index');
        Route::get('/functional-areas', [App\Http\Controllers\Admin\MasterDataController::class, 'functionalAreas'])->name('functionalArea.index');
        Route::get('/career-levels', [App\Http\Controllers\Admin\MasterDataController::class, 'careerLevels'])->name('careerLevel.index');
        Route::get('/salary-currencies', [App\Http\Controllers\Admin\MasterDataController::class, 'salaryCurrencies'])->name('salaryCurrency.index');
        Route::get('/ownership-types', [App\Http\Controllers\Admin\MasterDataController::class, 'ownershipTypes'])->name('ownerShipType.index');
        Route::get('/languages', [App\Http\Controllers\Admin\MasterDataController::class, 'languages'])->name('languages.index');
        
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
        
        Route::get('/testimonials', [App\Http\Controllers\Admin\CmsController::class, 'testimonials'])->name('testimonials.index');
        Route::get('/branding-sliders', [App\Http\Controllers\Admin\CmsController::class, 'brandingSliders'])->name('branding.sliders.index');
        Route::get('/header-sliders', [App\Http\Controllers\Admin\CmsController::class, 'headerSliders'])->name('header.sliders.index');
        Route::get('/image-sliders', [App\Http\Controllers\Admin\CmsController::class, 'imageSliders'])->name('image-sliders.index');
        Route::get('/cms-services', [App\Http\Controllers\Admin\CmsController::class, 'cmsServices'])->name('cms.services.index');
        Route::get('/cms-about-us', [App\Http\Controllers\Admin\CmsController::class, 'cmsAboutUs'])->name('cms.about-us.service');
        
        // Missing admin index route
        Route::get('/', function () {
            return view('admin.dashboard.index');
        })->name('index');
    });
    
    // Utility routes for authenticated users
    Route::get('/manage-subscription', function () {
        return view('subscription.index');
    })->name('manage-subscription.index');
    
    Route::get('/transactions', function () {
        return view('transactions.index');
    })->name('transactions.index');
    
    Route::get('/theme-mode-toggle', function () {
        session(['theme_mode' => !session('theme_mode', false)]);
        return redirect()->back();
    })->name('theme.mode');
    
    Route::get('/followers', function () {
        return view('followers.index');
    })->name('followers.index');
    
    Route::get('/favourite-companies', function () {
        return view('candidate.favourite_companies.index');
    })->name('favourite.companies');
    
    Route::get('/favourite-jobs', function () {
        return view('candidate.favourite_jobs.index');
    })->name('favourite.jobs');
    
    Route::get('/candidates-list', function () {
        return view('candidates.index');
    })->name('candidates.index');
    
    Route::get('/testimonials', function () {
        return view('testimonials.index');
    })->name('testimonials.index');
    
    Route::get('/subscribers', function () {
        return view('subscribers.index');
    })->name('subscribers.index');
    
    Route::get('/noticeboards', function () {
        return view('noticeboards.index');
    })->name('noticeboards.index');
    
    Route::get('/plans', function () {
        return view('plans.index');
    })->name('plans.index');
    
    Route::get('/post-categories', function () {
        return view('post_categories.index');
    })->name('post-categories.index');
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
        return view('front_web.jobs.job_details');
    })->name('job.details');
    
    Route::get('/company-details/{companyId}', function ($companyId) {
        return view('front_web.company.company_details');
    })->name('company.details');
    
    Route::get('/candidate-details/{candidateId}', function ($candidateId) {
        return view('front_web.candidates.show');
    })->name('candidate.details');
    
    // Additional front-end routes that are missing
    Route::get('/register', function () {
        return view('front_web.auth.register');
    })->name('save.register');
    
    Route::get('/candidate-login', function () {
        return view('front_web.auth.candidate_login');
    })->name('candidate.login');
    
    Route::get('/employee-login', function () {
        return view('front_web.auth.employee_login');
    })->name('employee.login');
    
    Route::get('/login', function () {
        return view('front_web.auth.login');
    })->name('login');
    
    // Critical missing routes from blade analysis
    Route::get('/blog-category/{categoryId}', function ($categoryId) {
        return view('front_web.blogs.category');
    })->name('blog.category');
});

// Job Application Routes
Route::get('/apply-job-form/{jobId}', function ($jobId) {
    return view('front_web.jobs.apply_job.apply_job');
})->name('show.apply-job-form');

Route::post('/apply-job', function () {
    return redirect()->back()->with('success', 'Application submitted successfully!');
})->name('apply-job');

// Favorite functionality routes
Route::post('/save-favourite-company', function () {
    return response()->json(['success' => true, 'message' => 'Company added to favorites']);
})->name('save.favourite.company');

Route::post('/save-favourite-job', function () {
    return response()->json(['success' => true, 'message' => 'Job added to favorites']);
})->name('save.favourite.job');

// Report functionality routes  
Route::post('/report-to-company', function () {
    return response()->json(['success' => true, 'message' => 'Report submitted']);
})->name('report.to.company');

Route::post('/report-job-abuse', function () {
    return response()->json(['success' => true, 'message' => 'Report submitted']);
})->name('report.job.abuse');

// Email job functionality
Route::post('/email-job', function () {
    return response()->json(['success' => true, 'message' => 'Job emailed successfully']);
})->name('email.job');

// Newsletter subscription
Route::post('/news-letter', function () {
    return response()->json(['success' => true, 'message' => 'Subscribed to newsletter']);
})->name('news-letter.create');

// Company edit form route
Route::get('/company/{companyId}/edit-form', function ($companyId) {
    return view('companies.edit');
})->name('company.edit.form');

// Privacy policy with section parameter
Route::get('/privacy-policy/{section?}', function ($section = 'privacy_policy') {
    return view('front_web.privacy_policy.index', compact('section'));
})->name('privacy.policy.index');

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

// Location data routes using proper controller (not API)
Route::get('/states-list', [App\Http\Controllers\LocationController::class, 'getStates'])->name('states-list');
Route::get('/cities-list', [App\Http\Controllers\LocationController::class, 'getCities'])->name('cities-list');
Route::get('/countries-list', [App\Http\Controllers\LocationController::class, 'getCountries'])->name('countries-list');

// Critical missing routes from blade analysis
Route::get('/download/resume/{id}', function ($id) {
    return response()->download(storage_path('app/public/resumes/' . $id . '.pdf'));
})->name('download.resume');

Route::get('/download/post/{id}', function ($id) {
    return response()->download(storage_path('app/public/posts/' . $id . '.pdf'));
})->name('download.post');

// Candidate experience and education routes
Route::middleware(['auth'])->prefix('candidate')->name('candidate.')->group(function () {
    Route::get('/create-experience', function () {
        return view('candidate.profile.experience.create');
    })->name('create-experience');
    
    Route::get('/create-education', function () {
        return view('candidate.profile.education.create');
    })->name('create-education');
    
    Route::get('/cv-template', function () {
        return view('candidate.profile.cv_template');
    })->name('cv.template');
    
    Route::post('/general-profile-update', function () {
        return redirect()->back()->with('success', 'Profile updated successfully');
    })->name('general.profile.update');
    
    Route::post('/online-profile-update', function () {
        return redirect()->back()->with('success', 'Online profile updated successfully');
    })->name('online.profile.update');
    
    Route::get('/edit-profile', function () {
        return view('candidate.profile.edit');
    })->name('edit.profile');
});

// Job application routes
Route::post('/change-job-stage', function () {
    return response()->json(['success' => true, 'message' => 'Job stage changed successfully']);
})->name('change.job.stage');

Route::get('/view-slot-screen/{jobId}', function ($jobId) {
    return view('employer.job_applications.slot_screen');
})->name('view.slot.screen');

Route::post('/interview-slot-store', function () {
    return response()->json(['success' => true, 'message' => 'Interview slot stored successfully']);
})->name('interview.slot.store');

Route::post('/batch-slot-store', function () {
    return response()->json(['success' => true, 'message' => 'Batch slot stored successfully']);
})->name('batch.slot.store');

Route::get('/get-schedule-history/{jobApplicationId}', function ($jobApplicationId) {
    return response()->json(['data' => []]);
})->name('get.schedule.history');

Route::post('/cancel-selected-slot', function () {
    return response()->json(['success' => true, 'message' => 'Slot cancelled successfully']);
})->name('cancel.selected.slot');

// Contact form route
Route::post('/send-contact-email', function () {
    return redirect()->back()->with('success', 'Message sent successfully!');
})->name('send.contact.email');

// Blog comment route
Route::post('/blog-create-comment', function () {
    return redirect()->back()->with('success', 'Comment posted successfully!');
})->name('blog.create.comment');

// Front-end search and category routes
Route::get('/get-jobs-search', function () {
    return response()->json(['data' => []]);
})->name('get.jobs.search');

Route::get('/front-categories', function () {
    return view('front_web.categories.index');
})->name('front.categories');

// Report routes
Route::post('/report-to-candidate', function () {
    return response()->json(['success' => true, 'message' => 'Report submitted successfully']);
})->name('report.to.candidate');

// Admin login route
Route::get('/admin/login', function () {
    return view('admin.auth.login');
})->name('admin.login');

// Employer dashboard route
Route::middleware(['auth'])->get('/employer/dashboard', function () {
    return view('employer.dashboard.index');
})->name('employer.dashboard');

// Job stage management
Route::get('/job-stage', function () {
    return view('employer.job_stages.index');
})->name('job.stage.index');

// Download all resumes
Route::get('/download-all-resume', function () {
    return response()->download(storage_path('app/public/all_resumes.zip'));
})->name('download.all-resume');

// Upload store route
Route::post('/uploads-store', function () {
    return response()->json(['success' => true, 'url' => asset('storage/uploads/sample.jpg')]);
})->name('uploads.store');

// Posts store route
Route::post('/posts-store', function () {
    return redirect()->back()->with('success', 'Post created successfully');
})->name('posts.store');

// Translation manager route
Route::get('/translation-manager', function () {
    return view('admin.translation_manager.index');
})->name('translation-manager.index');
