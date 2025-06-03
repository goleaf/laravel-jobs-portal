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
});

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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

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
        Route::get('/dashboard', function () {
            // Mock data for dashboard
            $data = [
                'dashboardData' => [
                    'totalCandidates' => 150,
                    'totalEmployers' => 75,
                    'totalActiveJobs' => 200,
                    'featuredJobs' => 25,
                    'featuredEmployers' => 15,
                    'featuredJobsIncomes' => 5000,
                    'featuredCompanysIncomes' => 3000,
                    'subscriptionIncomes' => 8000,
                ],
                'registerCandidatesData' => collect([]),
                'recentEmployersData' => collect([]),
                'recentJobsData' => collect([]),
            ];
            return view('dashboard.index', compact('data'));
        })->name('dashboard');

        // Candidates management
        Route::resource('candidates', App\Http\Controllers\Web\CandidateController::class);
        
        // Jobs management
        Route::resource('jobs', App\Http\Controllers\Web\JobController::class);
        
        // Transactions management
        Route::resource('transactions', App\Http\Controllers\Web\TransactionController::class);
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
