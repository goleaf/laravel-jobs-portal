<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Bind the app to the facade root
Illuminate\Support\Facades\Facade::setFacadeApplication($app);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a fake request to properly initialize the application
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

echo "🔍 Route Analysis Report\n";
echo "========================\n\n";

$routes = Route::getRoutes();
$totalRoutes = count($routes);
$workingRoutes = [];
$brokenRoutes = [];
$missingViews = [];

echo "📊 Total Routes Found: {$totalRoutes}\n\n";

foreach ($routes as $route) {
    $name = $route->getName();
    $uri = $route->uri();
    $methods = implode('|', $route->methods());
    $action = $route->getActionName();
    
    if (str_contains($uri, '{') || in_array('POST', $route->methods()) || in_array('PUT', $route->methods()) || in_array('DELETE', $route->methods())) {
        // Skip routes with parameters or non-GET methods for this test
        continue;
    }
    
    echo "🧪 Testing: {$methods} {$uri}";
    if ($name) {
        echo " [{$name}]";
    }
    echo "\n";
    
    try {
        // Test if the route action exists
        if (str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action);
            if (!class_exists($controller)) {
                $brokenRoutes[] = [
                    'route' => $name,
                    'uri' => $uri,
                    'error' => "Controller {$controller} not found"
                ];
                echo "   ❌ Controller missing: {$controller}\n";
                continue;
            }
            
            if (!method_exists($controller, $method)) {
                $brokenRoutes[] = [
                    'route' => $name,
                    'uri' => $uri,
                    'error' => "Method {$method} not found in {$controller}"
                ];
                echo "   ❌ Method missing: {$method}\n";
                continue;
            }
        }
        
        // Check for view existence if it's a closure that returns a view
        $closure = $route->getAction('uses');
        if ($closure instanceof Closure) {
            // Try to extract view name from closure source
            $reflection = new ReflectionFunction($closure);
            $source = file($reflection->getFileName());
            $body = '';
            for ($i = $reflection->getStartLine() - 1; $i < $reflection->getEndLine(); $i++) {
                $body .= $source[$i];
            }
            
            if (preg_match("/view\s*\(\s*['\"]([^'\"]+)['\"]/", $body, $matches)) {
                $viewName = $matches[1];
                if (!View::exists($viewName)) {
                    $missingViews[] = [
                        'route' => $name,
                        'uri' => $uri,
                        'view' => $viewName
                    ];
                    echo "   ⚠️  View missing: {$viewName}\n";
                } else {
                    echo "   ✅ View exists: {$viewName}\n";
                }
            }
        }
        
        $workingRoutes[] = [
            'route' => $name,
            'uri' => $uri,
            'action' => $action
        ];
        echo "   ✅ Route structure OK\n";
        
    } catch (Exception $e) {
        $brokenRoutes[] = [
            'route' => $name,
            'uri' => $uri,
            'error' => $e->getMessage()
        ];
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Summary Report
echo "\n📋 SUMMARY REPORT\n";
echo "==================\n";
echo "✅ Working Routes: " . count($workingRoutes) . "\n";
echo "❌ Broken Routes: " . count($brokenRoutes) . "\n";
echo "⚠️  Missing Views: " . count($missingViews) . "\n\n";

if (!empty($brokenRoutes)) {
    echo "🚨 BROKEN ROUTES:\n";
    echo "------------------\n";
    foreach ($brokenRoutes as $broken) {
        echo "❌ {$broken['route']} ({$broken['uri']})\n";
        echo "   Error: {$broken['error']}\n\n";
    }
}

if (!empty($missingViews)) {
    echo "📄 MISSING VIEWS:\n";
    echo "------------------\n";
    foreach ($missingViews as $missing) {
        echo "⚠️  {$missing['route']} ({$missing['uri']})\n";
        echo "   View: {$missing['view']}\n\n";
    }
}

echo "🎯 RECOMMENDATIONS:\n";
echo "---------------------\n";
echo "1. Create missing controllers and methods\n";
echo "2. Create missing view files\n";
echo "3. Fix route name conflicts\n";
echo "4. Test routes with parameters separately\n\n";

echo "✅ Route analysis complete!\n"; 