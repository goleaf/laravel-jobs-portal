<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test route registration
$router = $app['router'];
$routes = $router->getRoutes();

echo "Checking company routes...\n";

$companyRoutes = [];
foreach ($routes as $route) {
    if (str_contains($route->getName() ?? '', 'company')) {
        $companyRoutes[] = [
            'name' => $route->getName(),
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods()),
            'action' => $route->getActionName()
        ];
    }
}

echo "Found " . count($companyRoutes) . " company routes:\n";
foreach ($companyRoutes as $route) {
    echo "- {$route['name']}: {$route['methods']} {$route['uri']} -> {$route['action']}\n";
}

// Test if CompanyController exists
if (class_exists('\App\Http\Controllers\CompanyController')) {
    echo "\n✅ CompanyController class exists\n";
    
    if (method_exists('\App\Http\Controllers\CompanyController', 'show')) {
        echo "✅ CompanyController::show method exists\n";
    } else {
        echo "❌ CompanyController::show method missing\n";
    }
    
    if (method_exists('\App\Http\Controllers\CompanyController', 'edit')) {
        echo "✅ CompanyController::edit method exists\n";
    } else {
        echo "❌ CompanyController::edit method missing\n";
    }
} else {
    echo "\n❌ CompanyController class not found\n";
}

// Check if routes are cached
if (file_exists(base_path('bootstrap/cache/routes-v7.php'))) {
    echo "\n⚠️  Routes are cached - may need to clear cache\n";
} else {
    echo "\n✅ Routes are not cached\n";
}

echo "\nTest complete!\n"; 