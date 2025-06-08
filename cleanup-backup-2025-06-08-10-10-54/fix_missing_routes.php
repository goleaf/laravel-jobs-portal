<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class RouteFixer
{
    private $webRoutesFile = 'routes/web.php';
    private $missingRoutes = [
        // Core user routes
        'login' => "Route::get('/login', [App\\Http\\Controllers\\Auth\\LoginController::class, 'showLoginForm'])->name('login');",
        'register' => "Route::get('/register', [App\\Http\\Controllers\\Auth\\RegisterController::class, 'showRegistrationForm'])->name('register');",
        
        // Job routes
        'jobs.index' => "Route::get('/jobs', [App\\Http\\Controllers\\Web\\JobController::class, 'index'])->name('jobs.index');",
        'jobs.show' => "Route::get('/jobs/{job}', [App\\Http\\Controllers\\Web\\JobController::class, 'show'])->name('jobs.show');",
        'jobs.search' => "Route::get('/jobs/search', [App\\Http\\Controllers\\Web\\JobController::class, 'search'])->name('jobs.search');",
        
        // Company routes  
        'companies.index' => "Route::get('/companies', [App\\Http\\Controllers\\Web\\CompanyController::class, 'index'])->name('companies.index');",
        'companies.show' => "Route::get('/companies/{company}', [App\\Http\\Controllers\\Web\\CompanyController::class, 'show'])->name('companies.show');",
        
        // Candidate routes
        'candidates.index' => "Route::get('/candidates', [App\\Http\\Controllers\\Web\\CandidateController::class, 'index'])->name('candidates.index');",
        'candidates.show' => "Route::get('/candidates/{candidate}', [App\\Http\\Controllers\\Web\\CandidateController::class, 'show'])->name('candidates.show');",
        'candidates.export.excel' => "Route::get('/candidates/export/excel', [App\\Http\\Controllers\\CandidateController::class, 'exportExcel'])->name('candidates.export.excel');",
        
        // Admin routes
        'admin.candidates.index' => "Route::get('/admin/candidates', [App\\Http\\Controllers\\Admin\\CandidateController::class, 'index'])->name('admin.candidates.index');",
        'admin.candidates.show' => "Route::get('/admin/candidates/{candidate}', [App\\Http\\Controllers\\Admin\\CandidateController::class, 'show'])->name('admin.candidates.show');",
        'admin.candidates.edit' => "Route::get('/admin/candidates/{candidate}/edit', [App\\Http\\Controllers\\Admin\\CandidateController::class, 'edit'])->name('admin.candidates.edit');",
        'admin.candidates.create' => "Route::get('/admin/candidates/create', [App\\Http\\Controllers\\Admin\\CandidateController::class, 'create'])->name('admin.candidates.create');",
        
        // Profile routes
        'candidate.profile' => "Route::get('/candidate/profile', [App\\Http\\Controllers\\Candidate\\CandidateController::class, 'profile'])->name('candidate.profile');",
        'candidate.profile.edit' => "Route::get('/candidate/profile/edit', [App\\Http\\Controllers\\Candidate\\CandidateController::class, 'editProfile'])->name('candidate.profile.edit');",
        
        // Dashboard routes
        'candidate.dashboard' => "Route::get('/candidate/dashboard', [App\\Http\\Controllers\\Candidate\\DashboardController::class, 'index'])->name('candidate.dashboard');",
        'employer.dashboard' => "Route::get('/employer/dashboard', [App\\Http\\Controllers\\Employer\\DashboardController::class, 'index'])->name('employer.dashboard');",
        
        // Settings routes
        'settings.about_us' => "Route::get('/admin/settings/about-us', [App\\Http\\Controllers\\SettingController::class, 'aboutUs'])->name('settings.about_us');",
        'settings.env' => "Route::get('/admin/settings/env', [App\\Http\\Controllers\\SettingController::class, 'envSetting'])->name('settings.env');",
        
        // Contact routes
        'contact' => "Route::get('/contact', [App\\Http\\Controllers\\Web\\HomeController::class, 'contact'])->name('contact');",
        'contact.store' => "Route::post('/contact', [App\\Http\\Controllers\\Web\\HomeController::class, 'sendContactEmail'])->name('contact.store');",
        
        // Blog routes
        'blogs.index' => "Route::get('/blogs', [App\\Http\\Controllers\\Web\\PostController::class, 'index'])->name('blogs.index');",
        'blogs.show' => "Route::get('/blogs/{post}', [App\\Http\\Controllers\\Web\\PostController::class, 'show'])->name('blogs.show');",
        
        // Privacy and Terms
        'privacy-policy' => "Route::get('/privacy-policy', [App\\Http\\Controllers\\Web\\PrivacyPolicyController::class, 'index'])->name('privacy-policy');",
        'terms-conditions' => "Route::get('/terms-conditions', [App\\Http\\Controllers\\Web\\PrivacyPolicyController::class, 'termsConditions'])->name('terms-conditions');",
    ];

    public function fixRoutes()
    {
        echo "=== FIXING MISSING ROUTES ===\n\n";
        
        if (!file_exists($this->webRoutesFile)) {
            echo "❌ Routes file not found: {$this->webRoutesFile}\n";
            return;
        }
        
        $content = file_get_contents($this->webRoutesFile);
        $originalContent = $content;
        
        // Find where to insert new routes (before the closing ?>)
        $insertPosition = strrpos($content, '?>');
        if ($insertPosition === false) {
            $insertPosition = strlen($content);
        }
        
        $newRoutes = "\n// === MISSING ROUTES ADDED BY ANALYZER ===\n";
        $addedCount = 0;
        
        foreach ($this->missingRoutes as $routeName => $routeDefinition) {
            // Check if route already exists
            if (strpos($content, "->name('$routeName')") === false) {
                $newRoutes .= $routeDefinition . "\n";
                $addedCount++;
                echo "✅ Added route: $routeName\n";
            } else {
                echo "⏭️  Route already exists: $routeName\n";
            }
        }
        
        if ($addedCount > 0) {
            // Insert new routes before closing tag
            $newContent = substr($content, 0, $insertPosition) . $newRoutes . "\n" . substr($content, $insertPosition);
            
            // Write back to file
            file_put_contents($this->webRoutesFile, $newContent);
            
            echo "\n✅ Successfully added $addedCount new routes to {$this->webRoutesFile}\n";
        } else {
            echo "\n⏭️  No new routes needed to be added\n";
        }
        
        echo "\n=== ROUTE FIXING COMPLETED ===\n";
    }

    public function createMissingViews()
    {
        echo "\n=== CREATING MISSING VIEWS ===\n\n";
        
        $criticalViews = [
            'flash::message' => 'vendor/laracasts/flash/src/views/message.blade.php',
        ];
        
        foreach ($criticalViews as $viewName => $suggestedPath) {
            echo "🔍 Missing view: $viewName\n";
            echo "   Suggested path: $suggestedPath\n";
            echo "   This appears to be a vendor package view. Please install: composer require laracasts/flash\n\n";
        }
        
        echo "=== VIEW CREATION COMPLETED ===\n";
    }

    public function generateMiddlewareGroups()
    {
        echo "\n=== GENERATING MIDDLEWARE GROUPS ===\n\n";
        
        $middlewareGroups = "
// === MIDDLEWARE GROUPS ===
Route::middleware(['web'])->group(function () {
    // Public routes
    Route::get('/', [App\\Http\\Controllers\\Web\\HomeController::class, 'index'])->name('home');
    Route::get('/about', [App\\Http\\Controllers\\Web\\AboutUsController::class, 'index'])->name('about');
});

Route::middleware(['web', 'auth'])->group(function () {
    // Authenticated user routes
    Route::get('/dashboard', [App\\Http\\Controllers\\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['web', 'auth', 'role:candidate'])->prefix('candidate')->name('candidate.')->group(function () {
    // Candidate-specific routes
    Route::get('/dashboard', [App\\Http\\Controllers\\Candidate\\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\\Http\\Controllers\\Candidate\\CandidateController::class, 'profile'])->name('profile');
});

Route::middleware(['web', 'auth', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    // Employer-specific routes
    Route::get('/dashboard', [App\\Http\\Controllers\\Employer\\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['web', 'auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin-specific routes
    Route::resource('candidates', App\\Http\\Controllers\\Admin\\CandidateController::class);
    Route::resource('jobs', App\\Http\\Controllers\\Admin\\JobController::class);
    Route::resource('companies', App\\Http\\Controllers\\Admin\\CompanyController::class);
});
";
        
        echo "Suggested middleware groups structure:\n";
        echo $middlewareGroups;
        
        echo "\n=== MIDDLEWARE GROUPS COMPLETED ===\n";
    }
}

// Run the route fixer
$fixer = new RouteFixer();
$fixer->fixRoutes();
$fixer->createMissingViews();
$fixer->generateMiddlewareGroups(); 