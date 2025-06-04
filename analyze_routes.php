<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes_to_check = [
    'front.home', 'front.search.jobs', 'login', 'front.save.register', 
    'front.candidate.login', 'employer.register', 'company.index', 
    'candidate.register', 'job.index', 'register', 'password.request',
    'admin.jobs.index', 'front.employee.login', 'candidates.index',
    'manage-subscription.index', 'jobs.index', 'terms.conditions.list',
    'privacy.policy.list', 'posts.index', 'front.login', 'followers.index',
    'candidate.job.alert', 'candidate.applied.job', 'admin.index',
    'transactions.index', 'theme.mode', 'front.contact', 'front.about.us',
    'favourite.companies', 'candidate.profile', 'favourite.jobs', 
    'companies.index', 'admin.candidates.index', 'testimonials.index',
    'subscribers.index', 'post-categories.index', 'plans.index',
    'noticeboards.index', 'dashboard', 'candidates.update',
    'admin.candidates.show', 'admin.candidates.edit', 'admin.candidates.create'
];

echo "=== LARAVEL ROUTE ANALYSIS REPORT ===\n";
echo "Total routes to check: " . count($routes_to_check) . "\n\n";

$working_routes = [];
$missing_routes = [];

foreach ($routes_to_check as $route_name) {
    try {
        $url = route($route_name);
        echo "✅ $route_name -> $url\n";
        $working_routes[] = $route_name;
    } catch (Exception $e) {
        echo "❌ $route_name -> MISSING (" . $e->getMessage() . ")\n";
        $missing_routes[] = $route_name;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Working routes: " . count($working_routes) . "\n";
echo "Missing routes: " . count($missing_routes) . "\n";

if (!empty($missing_routes)) {
    echo "\n=== MISSING ROUTES LIST ===\n";
    foreach ($missing_routes as $route) {
        echo "- $route\n";
    }
}

echo "\n=== ROUTE DEFINITIONS ANALYSIS ===\n";
$all_routes = \Illuminate\Support\Facades\Route::getRoutes();
echo "Total defined routes in application: " . $all_routes->count() . "\n";

echo "\n=== ADMIN ROUTES ===\n";
foreach ($all_routes as $route) {
    $name = $route->getName();
    if ($name && str_contains($name, 'admin.')) {
        echo "✓ " . $name . " -> " . $route->uri() . "\n";
    }
} 