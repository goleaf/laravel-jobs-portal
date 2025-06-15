<?php

/**
 * Comprehensive Upgrade Analyzer for Laravel Job Portal
 * Identifies all aspects needing upgrades and modernization
 */

echo "🚀 COMPREHENSIVE UPGRADE ANALYZER - Laravel Job Portal\n";
echo "================================================================\n\n";

// Analysis categories
$analyses = [
    'composer_dependencies' => 'Composer Dependencies Analysis',
    'npm_dependencies' => 'NPM Dependencies Analysis', 
    'laravel_config' => 'Laravel Configuration Analysis',
    'database_schema' => 'Database Schema Analysis',
    'api_routes' => 'API Routes Analysis',
    'controller_issues' => 'Controller Issues Analysis',
    'request_validation' => 'Request Validation Analysis',
    'test_coverage' => 'Test Coverage Analysis',
    'vue_components' => 'Vue Components Analysis',
    'build_assets' => 'Build Assets Analysis',
    'security_issues' => 'Security Issues Analysis'
];

$upgrade_report = [];

// 1. Composer Dependencies Analysis
echo "📦 COMPOSER DEPENDENCIES ANALYSIS\n";
echo "=====================================\n";
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    
    $outdated_packages = [];
    $security_packages = [];
    
    // Check for key Laravel packages
    $key_packages = [
        'laravel/framework' => '12.x',
        'laravel/sanctum' => '^4.0',
        'spatie/laravel-permission' => '^6.0',
        'spatie/laravel-activitylog' => '^4.0'
    ];
    
    foreach ($key_packages as $package => $recommended) {
        if (isset($composer['require'][$package])) {
            $current = $composer['require'][$package];
            if ($current !== $recommended) {
                $outdated_packages[] = "$package: $current → $recommended";
            }
        } else {
            $outdated_packages[] = "$package: MISSING → $recommended";
        }
    }
    
    $upgrade_report['composer'] = [
        'outdated_packages' => $outdated_packages,
        'security_packages' => $security_packages
    ];
    
    echo "Outdated packages: " . count($outdated_packages) . "\n";
    foreach ($outdated_packages as $package) {
        echo "  - $package\n";
    }
} else {
    echo "❌ composer.json not found\n";
}
echo "\n";

// 2. NPM Dependencies Analysis  
echo "📦 NPM DEPENDENCIES ANALYSIS\n";
echo "============================\n";
if (file_exists('package.json')) {
    $package = json_decode(file_get_contents('package.json'), true);
    
    $outdated_npm = [];
    
    // Check for key frontend packages
    $key_npm_packages = [
        'vue' => '^3.4.0',
        'vite' => '^5.4.0',
        '@vitejs/plugin-vue' => '^5.0.0',
        'tailwindcss' => '^3.4.0',
        'typescript' => '^5.3.0',
        'pinia' => '^2.1.0',
        'vue-router' => '^4.2.0'
    ];
    
    foreach ($key_npm_packages as $package_name => $recommended) {
        $current = $package['dependencies'][$package_name] ?? $package['devDependencies'][$package_name] ?? null;
        if ($current && $current !== $recommended) {
            $outdated_npm[] = "$package_name: $current → $recommended";
        } elseif (!$current) {
            $outdated_npm[] = "$package_name: MISSING → $recommended";
        }
    }
    
    $upgrade_report['npm'] = ['outdated_packages' => $outdated_npm];
    
    echo "Outdated NPM packages: " . count($outdated_npm) . "\n";
    foreach ($outdated_npm as $package_name) {
        echo "  - $package_name\n";
    }
} else {
    echo "❌ package.json not found\n";
}
echo "\n";

// 3. Laravel Configuration Analysis
echo "⚙️ LARAVEL CONFIGURATION ANALYSIS\n";
echo "==================================\n";
$config_issues = [];

// Check Laravel version
exec('php artisan --version 2>/dev/null', $version_output);
if ($version_output) {
    echo "Laravel Version: " . $version_output[0] . "\n";
}

// Check key config files
$config_files = [
    'config/app.php',
    'config/database.php',
    'config/sanctum.php',
    'config/security.php',
    'config/cache.php',
    'config/session.php'
];

foreach ($config_files as $config_file) {
    if (file_exists($config_file)) {
        echo "✅ $config_file exists\n";
    } else {
        $config_issues[] = "Missing: $config_file";
        echo "❌ $config_file missing\n";
    }
}

$upgrade_report['config'] = ['issues' => $config_issues];
echo "\n";

// 4. Database Schema Analysis  
echo "🗄️ DATABASE SCHEMA ANALYSIS\n";
echo "============================\n";
$schema_issues = [];

// Check for recent migrations
$migrations_dir = 'database/migrations';
if (is_dir($migrations_dir)) {
    $migrations = glob("$migrations_dir/*.php");
    echo "Total migrations: " . count($migrations) . "\n";
    
    // Check for recent migrations (last 30 days)
    $recent_migrations = array_filter($migrations, function($file) {
        return filemtime($file) > (time() - 30 * 24 * 3600);
    });
    echo "Recent migrations (30 days): " . count($recent_migrations) . "\n";
    
    // Check for specific important tables
    $required_tables = [
        'users', 'jobs', 'companies', 'applications',
        'personal_access_tokens', 'permissions', 'roles'
    ];
    
    foreach ($required_tables as $table) {
        $migration_exists = false;
        foreach ($migrations as $migration) {
            if (strpos(basename($migration), $table) !== false) {
                $migration_exists = true;
                break;
            }
        }
        if (!$migration_exists) {
            $schema_issues[] = "Missing migration for: $table";
        }
    }
} else {
    $schema_issues[] = "Migrations directory not found";
}

$upgrade_report['schema'] = ['issues' => $schema_issues];
echo "Schema issues: " . count($schema_issues) . "\n";
foreach ($schema_issues as $issue) {
    echo "  - $issue\n";
}
echo "\n";

// 5. Controller Issues Analysis
echo "🎮 CONTROLLER ISSUES ANALYSIS\n";
echo "==============================\n";
$controller_issues = [];

$controllers_dir = 'app/Http/Controllers';
if (is_dir($controllers_dir)) {
    $controllers = glob("$controllers_dir/**/*.php", GLOB_BRACE);
    echo "Total controllers: " . count($controllers) . "\n";
    
    $issues_found = 0;
    foreach ($controllers as $controller) {
        $content = file_get_contents($controller);
        
        // Check for common issues
        if (strpos($content, 'use use ') !== false) {
            $controller_issues[] = basename($controller) . ": Duplicate use statements";
            $issues_found++;
        }
        if (strpos($content, '$request->validated(),]);') !== false) {
            $controller_issues[] = basename($controller) . ": Malformed array syntax";
            $issues_found++;
        }
        if (strpos($content, '{}') !== false && strpos($content, '[]') === false) {
            $controller_issues[] = basename($controller) . ": JavaScript-style arrays";
            $issues_found++;
        }
    }
    
    echo "Controllers with issues: $issues_found\n";
    if (count($controller_issues) > 0) {
        echo "Sample issues:\n";
        foreach (array_slice($controller_issues, 0, 5) as $issue) {
            echo "  - $issue\n";
        }
    }
} else {
    echo "❌ Controllers directory not found\n";
}

$upgrade_report['controllers'] = ['issues' => $controller_issues];
echo "\n";

// 6. Request Validation Analysis
echo "✅ REQUEST VALIDATION ANALYSIS\n";
echo "===============================\n";
$validation_issues = [];

$requests_dir = 'app/Http/Requests';
if (is_dir($requests_dir)) {
    $requests = glob("$requests_dir/**/*.php", GLOB_BRACE);
    echo "Total request files: " . count($requests) . "\n";
    
    $empty_requests = 0;
    foreach ($requests as $request) {
        $content = file_get_contents($request);
        if (strpos($content, 'return [];') !== false || strpos($content, 'return [ ];') !== false) {
            $validation_issues[] = basename($request) . ": Empty validation rules";
            $empty_requests++;
        }
    }
    
    echo "Empty request files: $empty_requests\n";
} else {
    echo "❌ Requests directory not found\n";
}

$upgrade_report['validation'] = ['issues' => $validation_issues];
echo "\n";

// 7. Vue Components Analysis
echo "🖼️ VUE COMPONENTS ANALYSIS\n";
echo "===========================\n";
$vue_issues = [];

$vue_dirs = ['resources/js/components', 'resources/js/pages', 'resources/js/views'];
$total_vue_files = 0;

foreach ($vue_dirs as $vue_dir) {
    if (is_dir($vue_dir)) {
        $vue_files = glob("$vue_dir/**/*.vue", GLOB_BRACE);
        $total_vue_files += count($vue_files);
        echo "$vue_dir: " . count($vue_files) . " files\n";
    }
}

echo "Total Vue files: $total_vue_files\n";

if ($total_vue_files === 0) {
    $vue_issues[] = "No Vue components found - migration needed";
}

$upgrade_report['vue'] = ['issues' => $vue_issues, 'total_files' => $total_vue_files];
echo "\n";

// 8. Build Assets Analysis
echo "🏗️ BUILD ASSETS ANALYSIS\n";
echo "=========================\n";
$build_issues = [];

// Check Vite config
if (file_exists('vite.config.js') || file_exists('vite.config.ts')) {
    echo "✅ Vite config exists\n";
} else {
    $build_issues[] = "Missing Vite configuration";
    echo "❌ Vite config missing\n";
}

// Check for built assets
if (is_dir('public/build')) {
    $manifest = 'public/build/manifest.json';
    if (file_exists($manifest)) {
        $manifest_data = json_decode(file_get_contents($manifest), true);
        echo "Built assets: " . count($manifest_data) . " files\n";
    } else {
        $build_issues[] = "Missing build manifest";
    }
} else {
    $build_issues[] = "No built assets found";
    echo "❌ No built assets found\n";
}

$upgrade_report['build'] = ['issues' => $build_issues];
echo "\n";

// 9. Security Analysis
echo "🔐 SECURITY ANALYSIS\n";
echo "====================\n";
$security_issues = [];

// Check for security middleware
$middleware_dir = 'app/Http/Middleware';
$security_middleware = ['AuthenticateMiddleware.php', 'SecurityHeadersMiddleware.php'];

foreach ($security_middleware as $middleware) {
    if (file_exists("$middleware_dir/$middleware")) {
        echo "✅ $middleware exists\n";
    } else {
        $security_issues[] = "Missing: $middleware";
        echo "❌ $middleware missing\n";
    }
}

// Check for env security
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    if (strpos($env_content, 'APP_DEBUG=true') !== false) {
        $security_issues[] = "APP_DEBUG is true in production";
    }
    if (strpos($env_content, 'APP_KEY=') === false) {
        $security_issues[] = "Missing APP_KEY";
    }
} else {
    $security_issues[] = "Missing .env file";
}

$upgrade_report['security'] = ['issues' => $security_issues];
echo "Security issues: " . count($security_issues) . "\n";
foreach ($security_issues as $issue) {
    echo "  - $issue\n";
}
echo "\n";

// Generate Comprehensive Upgrade Report
echo "📊 COMPREHENSIVE UPGRADE SUMMARY\n";
echo "=================================\n";

$total_issues = 0;
foreach ($upgrade_report as $category => $data) {
    $issues_count = count($data['issues'] ?? $data['outdated_packages'] ?? []);
    $total_issues += $issues_count;
    echo ucfirst($category) . ": $issues_count issues\n";
}

echo "\nTotal Issues Found: $total_issues\n";

// Priority recommendations
echo "\n🎯 UPGRADE PRIORITIES\n";
echo "=====================\n";

$priorities = [
    'CRITICAL' => [],
    'HIGH' => [],
    'MEDIUM' => [],
    'LOW' => []
];

// Categorize issues by priority
if (count($upgrade_report['security']['issues']) > 0) {
    $priorities['CRITICAL'][] = "Fix security vulnerabilities (" . count($upgrade_report['security']['issues']) . ")";
}

if (count($upgrade_report['controllers']['issues']) > 10) {
    $priorities['HIGH'][] = "Fix controller syntax errors (" . count($upgrade_report['controllers']['issues']) . ")";
}

if (count($upgrade_report['composer']['outdated_packages']) > 0) {
    $priorities['HIGH'][] = "Update Composer dependencies (" . count($upgrade_report['composer']['outdated_packages']) . ")";
}

if (count($upgrade_report['npm']['outdated_packages']) > 0) {
    $priorities['MEDIUM'][] = "Update NPM dependencies (" . count($upgrade_report['npm']['outdated_packages']) . ")";
}

if (count($upgrade_report['validation']['issues']) > 5) {
    $priorities['MEDIUM'][] = "Implement request validation (" . count($upgrade_report['validation']['issues']) . ")";
}

if ($upgrade_report['vue']['total_files'] < 10) {
    $priorities['HIGH'][] = "Complete Vue.js migration (" . $upgrade_report['vue']['total_files'] . " files)";
}

foreach ($priorities as $level => $items) {
    if (count($items) > 0) {
        echo "\n$level PRIORITY:\n";
        foreach ($items as $item) {
            echo "  • $item\n";
        }
    }
}

// Save detailed report
$report_file = 'COMPREHENSIVE_UPGRADE_REPORT.json';
file_put_contents($report_file, json_encode($upgrade_report, JSON_PRETTY_PRINT));
echo "\n📋 Detailed report saved to: $report_file\n";

echo "\n✅ Comprehensive upgrade analysis complete!\n";
echo "Next: Run specific upgrade commands based on priorities above.\n";

?> 