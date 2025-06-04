<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Laravel Job Portal - Application Health Check ===\n\n";

// 1. Routes Check
echo "1. CHECKING ROUTES:\n";
try {
    $routeCollection = app('router')->getRoutes();
    $routeCount = count($routeCollection);
    echo "✅ Total routes registered: $routeCount\n";
    
    // Test critical routes
    $criticalRoutes = [
        '/',
        '/login',
        '/register',
        '/jobs',
        '/companies',
        '/contact',
        '/admin/login',
    ];
    
    foreach ($criticalRoutes as $route) {
        try {
            $response = app('router')->dispatch(Illuminate\Http\Request::create($route, 'GET'));
            $status = $response->getStatusCode();
            if ($status == 200) {
                echo "✅ Route $route: Working (HTTP $status)\n";
            } else {
                echo "⚠️  Route $route: HTTP $status\n";
            }
        } catch (Exception $e) {
            echo "❌ Route $route: Error - " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Route system error: " . $e->getMessage() . "\n";
}

echo "\n2. CHECKING MODELS:\n";
try {
    $models = [
        'App\Models\User',
        'App\Models\Job',
        'App\Models\Company',
        'App\Models\Candidate',
        'App\Models\JobApplication',
        'App\Models\Country',
        'App\Models\State',
        'App\Models\City',
    ];
    
    foreach ($models as $model) {
        try {
            if (class_exists($model)) {
                $count = $model::count();
                echo "✅ $model: Class exists, $count records\n";
            } else {
                echo "❌ $model: Class not found\n";
            }
        } catch (Exception $e) {
            echo "⚠️  $model: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Model system error: " . $e->getMessage() . "\n";
}

echo "\n3. CHECKING CONTROLLERS:\n";
try {
    $controllers = [
        'App\Http\Controllers\DashboardController',
        'App\Http\Controllers\LocationController',
        'App\Http\Controllers\CompanyController',
        'App\Http\Controllers\Admin\MasterDataController',
        'App\Http\Controllers\Admin\CmsController',
        'App\Http\Controllers\Web\CandidateController',
        'App\Http\Controllers\Web\JobController',
        'App\Http\Controllers\Web\TransactionController',
    ];
    
    foreach ($controllers as $controller) {
        if (class_exists($controller)) {
            echo "✅ $controller: Class exists\n";
        } else {
            echo "❌ $controller: Class not found\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Controller system error: " . $e->getMessage() . "\n";
}

echo "\n4. CHECKING VIEWS:\n";
try {
    $views = [
        'welcome',
        'auth.login',
        'auth.register',
        'jobs.index',
        'companies.index',
        'contact',
        'admin.candidates.index',
        'admin.jobs.index',
        'dashboard.index',
    ];
    
    foreach ($views as $view) {
        try {
            if (view()->exists($view)) {
                echo "✅ View $view: Exists\n";
            } else {
                echo "❌ View $view: Not found\n";
            }
        } catch (Exception $e) {
            echo "⚠️  View $view: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ View system error: " . $e->getMessage() . "\n";
}

echo "\n5. CHECKING TRANSLATIONS:\n";
try {
    // Test JSON translations
    $locales = ['en', 'es', 'fr', 'de', 'ar', 'ru', 'tr', 'pt', 'zh'];
    
    foreach ($locales as $locale) {
        $file = "lang/$locale.json";
        if (file_exists($file)) {
            $content = json_decode(file_get_contents($file), true);
            $keyCount = count($content ?? []);
            echo "✅ $locale.json: $keyCount translation keys\n";
        } else {
            echo "❌ $locale.json: File missing\n";
        }
    }
    
    // Test translation function
    app()->setLocale('en');
    $testTranslation = __('Login');
    echo "✅ Translation function working: '$testTranslation'\n";
} catch (Exception $e) {
    echo "❌ Translation system error: " . $e->getMessage() . "\n";
}

echo "\n6. CHECKING REQUEST VALIDATION:\n";
try {
    $requestFiles = glob('app/Http/Requests/**/*.php');
    $requestCount = count($requestFiles);
    echo "✅ Total request validation files: $requestCount\n";
    
    // Check for critical request files
    $criticalRequests = [
        'app/Http/Requests/User/LoginRequest.php',
        'app/Http/Requests/User/RegisterRequest.php',
        'app/Http/Requests/Job/CreateJobRequest.php',
    ];
    
    foreach ($criticalRequests as $request) {
        if (file_exists($request)) {
            echo "✅ $request: Exists\n";
        } else {
            echo "❌ $request: Missing\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Request validation error: " . $e->getMessage() . "\n";
}

echo "\n7. CHECKING DATABASE CONNECTION:\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection: Working\n";
    
    // Check critical tables
    $tables = ['users', 'jobs', 'companies', 'candidates', 'countries', 'states', 'cities'];
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "✅ Table $table: $count records\n";
        } catch (Exception $e) {
            echo "⚠️  Table $table: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n8. CHECKING CONFIGURATION:\n";
try {
    echo "✅ App Name: " . config('app.name') . "\n";
    echo "✅ App URL: " . config('app.url') . "\n";
    echo "✅ App Locale: " . config('app.locale') . "\n";
    echo "✅ Database Driver: " . config('database.default') . "\n";
    echo "✅ Cache Driver: " . config('cache.default') . "\n";
} catch (Exception $e) {
    echo "❌ Configuration error: " . $e->getMessage() . "\n";
}

echo "\n=== SUMMARY ===\n";
echo "Application Health Check Complete!\n";
echo "Review any ❌ or ⚠️  items above for potential issues.\n";
echo "✅ items are working correctly.\n\n";

echo "Next Priority Tasks:\n";
echo "- Fix any missing views or controllers identified\n";
echo "- Implement comprehensive error handling\n";
echo "- Add logging and monitoring\n";
echo "- Performance optimization\n";
echo "- Testing framework setup\n"; 