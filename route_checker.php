<?php

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Bootstrap Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);

// Create a request instance
$request = Request::create('/', 'GET');
$response = $kernel->handle($request);

// Initialize the application
$app->boot();

echo "=== COMPREHENSIVE ROUTE ANALYSIS ===\n\n";

// Get all registered routes
$routes = Route::getRoutes();
$routeCollection = $routes->getRoutes();

$results = [
    'working' => [],
    'missing_views' => [],
    'missing_controllers' => [],
    'errors' => [],
    'total' => 0,
];

echo 'Total routes found: '.count($routeCollection)."\n\n";

foreach ($routeCollection as $route) {
    ++$results['total'];
    $routeName = $route->getName();
    $routeUri = $route->uri();
    $routeMethods = implode('|', $route->methods());

    echo "Testing Route: {$routeName} ({$routeMethods}) -> {$routeUri}\n";

    try {
        // Skip routes that require parameters for now
        if (false !== strpos($routeUri, '{')) {
            echo "  ⚠️  Skipped (requires parameters)\n\n";

            continue;
        }

        // Skip POST/PUT/DELETE routes for basic testing
        if (in_array('POST', $route->methods())
            || in_array('PUT', $route->methods())
            || in_array('DELETE', $route->methods())) {
            echo "  ⚠️  Skipped (non-GET method)\n\n";

            continue;
        }

        // Create test request
        $testRequest = Request::create('/'.ltrim($routeUri, '/'), 'GET');

        // Try to match the route
        try {
            $matchedRoute = $routes->match($testRequest);

            // Check if it's a closure or controller
            $action = $route->getAction();

            if (isset($action['uses']) && is_string($action['uses'])) {
                // Controller action
                list($controller, $method) = explode('@', $action['uses']);

                if (!class_exists($controller)) {
                    $results['missing_controllers'][] = [
                        'route' => $routeName,
                        'uri' => $routeUri,
                        'controller' => $controller,
                        'method' => $method,
                    ];
                    echo "  ❌ Missing Controller: {$controller}\n\n";

                    continue;
                }

                if (!method_exists($controller, $method)) {
                    $results['missing_controllers'][] = [
                        'route' => $routeName,
                        'uri' => $routeUri,
                        'controller' => $controller,
                        'method' => $method,
                        'issue' => 'Missing method',
                    ];
                    echo "  ❌ Missing Method: {$controller}@{$method}\n\n";

                    continue;
                }

                echo "  ✅ Controller exists: {$controller}@{$method}\n";
            } else {
                // Closure route
                echo "  ✅ Closure route\n";
            }

            $results['working'][] = [
                'route' => $routeName,
                'uri' => $routeUri,
                'type' => isset($action['uses']) ? 'controller' : 'closure',
            ];
        } catch (Exception $e) {
            $results['errors'][] = [
                'route' => $routeName,
                'uri' => $routeUri,
                'error' => $e->getMessage(),
            ];
            echo '  ❌ Error: '.$e->getMessage()."\n\n";

            continue;
        }
    } catch (Exception $e) {
        $results['errors'][] = [
            'route' => $routeName,
            'uri' => $routeUri,
            'error' => $e->getMessage(),
        ];
        echo '  ❌ Exception: '.$e->getMessage()."\n\n";
    }

    echo "\n";
}

// Generate summary report
echo "\n=== SUMMARY REPORT ===\n";
echo 'Total Routes: '.$results['total']."\n";
echo 'Working Routes: '.count($results['working'])."\n";
echo 'Missing Controllers: '.count($results['missing_controllers'])."\n";
echo 'Errors: '.count($results['errors'])."\n";

if (!empty($results['missing_controllers'])) {
    echo "\n=== MISSING CONTROLLERS ===\n";
    foreach ($results['missing_controllers'] as $missing) {
        echo "Route: {$missing['route']} -> {$missing['controller']}\n";
        if (isset($missing['method'])) {
            echo "  Method: {$missing['method']}\n";
        }
        if (isset($missing['issue'])) {
            echo "  Issue: {$missing['issue']}\n";
        }
        echo "\n";
    }
}

if (!empty($results['errors'])) {
    echo "\n=== ERRORS ===\n";
    foreach ($results['errors'] as $error) {
        echo "Route: {$error['route']} ({$error['uri']})\n";
        echo "Error: {$error['error']}\n\n";
    }
}

// Check for commonly referenced routes in blade files
echo "\n=== BLADE FILE ROUTE ANALYSIS ===\n";

$commonRoutes = [
    'front.home',
    'jobs.index',
    'companies.index',
    'about-us',
    'contact',
    'login',
    'register',
    'dashboard',
    'admin.dashboard',
    'admin.jobs.index',
    'admin.candidates.index',
    'admin.transactions.index',
    'company.show',
    'company.edit',
    'posts.show',
    'posts.edit',
    'front.job.details',
    'front.candidate.details',
    'front.company.details',
];

foreach ($commonRoutes as $routeName) {
    if (Route::has($routeName)) {
        echo "✅ {$routeName} - EXISTS\n";
    } else {
        echo "❌ {$routeName} - MISSING\n";
    }
}

// Save results to JSON file
file_put_contents('route_analysis_report.json', json_encode($results, JSON_PRETTY_PRINT));
echo "\n📄 Detailed report saved to: route_analysis_report.json\n";

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Create missing controllers and methods\n";
echo "2. Implement proper request validation classes\n";
echo "3. Add missing view files\n";
echo "4. Test routes with parameters manually\n";
echo "5. Implement proper error handling\n";
echo "6. Add middleware for authentication and authorization\n";

$kernel->terminate($request, $response);
