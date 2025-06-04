<?php
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
    'noticeboards.index', 'dashboard'
];

echo "=== ROUTE ANALYSIS REPORT ===\n";
echo "Total routes to check: " . count($routes_to_check) . "\n\n";

$working_routes = [];
$missing_routes = [];

foreach ($routes_to_check as $route) {
    try {
        $url = route($route);
        echo "✅ $route -> $url\n";
        $working_routes[] = $route;
    } catch (Exception $e) {
        echo "❌ $route -> MISSING\n";
        $missing_routes[] = $route;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Working routes: " . count($working_routes) . "\n";
echo "Missing routes: " . count($missing_routes) . "\n";
echo "\nMissing routes list:\n";
foreach ($missing_routes as $route) {
    echo "- $route\n";
} 