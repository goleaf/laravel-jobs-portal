<?php

use Illuminate\Support\Facades\Route;

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
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Candidate routes
    Route::prefix('candidate')->name('candidate.')->group(function () {
        Route::get('/profile', function () {
            return view('candidate.profile');
        })->name('profile');

        Route::get('/profile/edit', function () {
            return view('candidate.profile-edit');
        })->name('profile.edit');

        Route::get('/applied-jobs', function () {
            return view('candidate.applied-jobs');
        })->name('applied-jobs');

        Route::get('/favorite-jobs', function () {
            return view('candidate.favorite-jobs');
        })->name('favorite-jobs');

        Route::get('/job-alerts', function () {
            return view('candidate.job-alerts');
        })->name('job-alerts');
    });

    // Employer routes
    Route::prefix('employer')->name('employer.')->group(function () {
        Route::get('/company', function () {
            return view('employer.company');
        })->name('company');

        Route::get('/company/edit', function () {
            return view('employer.company-edit');
        })->name('company.edit');

        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', function () {
                return view('employer.jobs.index');
            })->name('index');

            Route::get('/create', function () {
                return view('employer.jobs.create');
            })->name('create');

            Route::get('/applications', function () {
                return view('employer.jobs.applications');
            })->name('applications');
        });
    });

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });
});

// Add auth routes for login/logout functionality
Route::post('/login', function () {
    return redirect('/dashboard');
})->name('login.submit');

Route::post('/logout', function () {
    return redirect('/');
})->name('logout');

// Password reset routes
Route::get('/password/reset', function () {
    return view('auth.passwords.reset');
})->name('password.reset');

Route::post('/password/email', function () {
    return back()->with('status', 'Password reset link sent!');
})->name('password.email');

Route::post('/password/reset', function () {
    return redirect('/home');
})->name('password.update');

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
