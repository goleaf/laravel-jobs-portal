<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request
$request = Request::create('/', 'GET');
$response = $kernel->handle($request);

// Get all routes
$routes = app('router')->getRoutes();

echo "=== ROUTE ANALYSIS REPORT ===\n\n";

$issues = [];
$tested = 0;
$working = 0;
$broken = 0;

// Test critical routes
$criticalRoutes = [
    'admin.jobs.index',
    'admin.jobs.create', 
    'admin.jobs.store',
    'admin.jobs.show',
    'admin.jobs.edit',
    'admin.jobs.update',
    'admin.jobs.destroy',
    'company.index',
    'company.create',
    'company.show',
    'company.edit',
    'companies.index',
    'jobs.index',
    'states-list',
    'cities-list',
    'theme.mode',
    'download.image',
    'front.home',
    'login',
    'register',
    'admin.dashboard'
];

echo "Testing Critical Routes:\n";
echo "========================\n";

foreach ($criticalRoutes as $routeName) {
    $tested++;
    try {
        $route = app('router')->getRoutes()->getByName($routeName);
        if ($route) {
            echo "✓ {$routeName} - EXISTS\n";
            $working++;
        } else {
            echo "✗ {$routeName} - MISSING\n";
            $issues[] = "Missing route: {$routeName}";
            $broken++;
        }
    } catch (Exception $e) {
        echo "✗ {$routeName} - ERROR: " . $e->getMessage() . "\n";
        $issues[] = "Route error: {$routeName} - " . $e->getMessage();
        $broken++;
    }
}

echo "\n";

// Check for route naming inconsistencies
echo "Checking Route Naming Patterns:\n";
echo "===============================\n";

$adminJobRoutes = [];
$adminJobsRoutes = [];

foreach ($routes as $route) {
    $name = $route->getName();
    if ($name) {
        if (strpos($name, 'admin.job.') === 0) {
            $adminJobRoutes[] = $name;
        }
        if (strpos($name, 'admin.jobs.') === 0) {
            $adminJobsRoutes[] = $name;
        }
    }
}

if (!empty($adminJobRoutes)) {
    echo "Found admin.job.* routes (should be admin.jobs.*):\n";
    foreach ($adminJobRoutes as $route) {
        echo "  - {$route}\n";
        $issues[] = "Inconsistent route naming: {$route} (should use admin.jobs.*)";
    }
}

if (!empty($adminJobsRoutes)) {
    echo "Found admin.jobs.* routes (correct):\n";
    foreach ($adminJobsRoutes as $route) {
        echo "  ✓ {$route}\n";
    }
}

echo "\n";

// Check for missing controller methods
echo "Checking Controller Methods:\n";
echo "============================\n";

$controllerChecks = [
    'App\Http\Controllers\CompanyController@show',
    'App\Http\Controllers\CompanyController@edit', 
    'App\Http\Controllers\Web\JobController@index',
    'App\Http\Controllers\Web\JobController@create',
    'App\Http\Controllers\Web\JobController@store',
    'App\Http\Controllers\Web\JobController@show',
    'App\Http\Controllers\Web\JobController@edit',
    'App\Http\Controllers\Web\JobController@update',
    'App\Http\Controllers\Web\JobController@destroy'
];

foreach ($controllerChecks as $controllerMethod) {
    list($controller, $method) = explode('@', $controllerMethod);
    
    if (class_exists($controller)) {
        if (method_exists($controller, $method)) {
            echo "✓ {$controllerMethod} - EXISTS\n";
        } else {
            echo "✗ {$controllerMethod} - METHOD MISSING\n";
            $issues[] = "Missing controller method: {$controllerMethod}";
        }
    } else {
        echo "✗ {$controllerMethod} - CONTROLLER MISSING\n";
        $issues[] = "Missing controller: {$controller}";
    }
}

echo "\n";

// Summary
echo "=== SUMMARY ===\n";
echo "Routes Tested: {$tested}\n";
echo "Working: {$working}\n";
echo "Broken: {$broken}\n";
echo "Success Rate: " . round(($working / $tested) * 100, 2) . "%\n\n";

if (!empty($issues)) {
    echo "=== ISSUES FOUND ===\n";
    foreach ($issues as $i => $issue) {
        echo ($i + 1) . ". {$issue}\n";
    }
    echo "\n";
}

// Check for undefined variables in blade files
echo "=== BLADE TEMPLATE ANALYSIS ===\n";

$bladeFiles = glob(__DIR__ . '/resources/views/**/*.blade.php');
$bladeIssues = [];

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace(__DIR__ . '/resources/views/', '', $file);
    
    // Check for common undefined variable patterns
    if (preg_match('/\$company(?!\w)/', $content) && !preg_match('/compact\([\'"]company[\'"]/', $content)) {
        if (strpos($content, '@isset($company)') === false && strpos($content, 'isset($company)') === false) {
            $bladeIssues[] = "{$relativePath} - Potential undefined \$company variable";
        }
    }
    
    if (preg_match('/\$job(?!\w)/', $content) && !preg_match('/compact\([\'"]job[\'"]/', $content)) {
        if (strpos($content, '@isset($job)') === false && strpos($content, 'isset($job)') === false) {
            $bladeIssues[] = "{$relativePath} - Potential undefined \$job variable";
        }
    }
    
    // Check for route references that might be broken
    if (preg_match_all('/route\([\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        foreach ($matches[1] as $routeName) {
            if (in_array($routeName, ['admin.job.create', 'admin.job.edit', 'admin.job.update', 'admin.job.store'])) {
                $bladeIssues[] = "{$relativePath} - Uses incorrect route name: {$routeName} (should be admin.jobs.*)";
            }
        }
    }
}

if (!empty($bladeIssues)) {
    echo "Blade Template Issues Found:\n";
    foreach ($bladeIssues as $i => $issue) {
        echo ($i + 1) . ". {$issue}\n";
    }
} else {
    echo "No major blade template issues found.\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";

// Cleanup
$kernel->terminate($request, $response); 