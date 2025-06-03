<?php

/**
 * Job Portal Upgrade Analysis
 * Comprehensive check for available updates and improvements
 */

echo "==========================================\n";
echo "    JOB PORTAL UPGRADE ANALYSIS\n";
echo "==========================================\n\n";

// Function to execute command and capture output
function runCommand($command) {
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    return [
        'output' => $output,
        'success' => $returnCode === 0,
        'code' => $returnCode
    ];
}

// Function to check if command exists
function commandExists($command) {
    $result = runCommand("which $command");
    return $result['success'];
}

// Current System Information
echo "1. CURRENT SYSTEM STATUS\n";
echo "========================\n";

// PHP Version
$phpVersion = PHP_VERSION;
echo "✅ PHP Version: $phpVersion\n";

// Laravel Version (from composer.json)
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    $laravelVersion = $composer['require']['laravel/framework'] ?? 'Not found';
    echo "✅ Laravel Framework: $laravelVersion\n";
} else {
    echo "❌ composer.json not found\n";
}

// Check if we're in Laravel project
if (file_exists('artisan')) {
    echo "✅ Laravel Project Detected\n";
} else {
    echo "❌ Laravel artisan command not found\n";
}

echo "\n";

// 2. Composer Dependencies Check
echo "2. COMPOSER DEPENDENCIES\n";
echo "=========================\n";

if (commandExists('composer')) {
    // Check for outdated packages
    echo "Checking for outdated packages...\n";
    $outdated = runCommand('composer outdated --direct');
    
    if ($outdated['success'] && !empty($outdated['output'])) {
        echo "📦 Outdated packages found:\n";
        foreach ($outdated['output'] as $line) {
            if (strpos($line, '/') !== false && strpos($line, '!') === false) {
                echo "  ⚠️  $line\n";
            }
        }
    } else {
        echo "✅ All direct dependencies are up to date\n";
    }
    
    // Security audit
    echo "\nRunning security audit...\n";
    $audit = runCommand('composer audit');
    if ($audit['success']) {
        echo "✅ No known security vulnerabilities found\n";
    } else {
        echo "⚠️  Security issues detected:\n";
        foreach ($audit['output'] as $line) {
            if (!empty(trim($line))) {
                echo "  🔒 $line\n";
            }
        }
    }
} else {
    echo "❌ Composer not available\n";
}

echo "\n";

// 3. Laravel Specific Checks
echo "3. LARAVEL FRAMEWORK STATUS\n";
echo "============================\n";

// Check Laravel version compatibility
$currentLaravel = '10.0'; // From composer.json constraint
$latestLTS = '11.0'; // Laravel 11 LTS
$latest = '11.x';

echo "📋 Current Laravel: ^$currentLaravel\n";
echo "📋 Latest LTS: $latestLTS\n";
echo "📋 Latest Version: $latest\n";

if (version_compare($currentLaravel, '11.0', '<')) {
    echo "⚠️  UPGRADE AVAILABLE: Laravel 11 LTS is available\n";
    echo "   Benefits:\n";
    echo "   - Performance improvements\n";
    echo "   - New features and API improvements\n";
    echo "   - Long-term support until 2027\n";
    echo "   - Better PHP 8.3+ compatibility\n";
} else {
    echo "✅ Laravel version is current\n";
}

echo "\n";

// 4. PHP Version Check
echo "4. PHP VERSION ANALYSIS\n";
echo "========================\n";

$currentPHP = PHP_VERSION;
$recommendedPHP = '8.3';
$minimumPHP = '8.2';

echo "📋 Current PHP: $currentPHP\n";
echo "📋 Recommended: PHP $recommendedPHP\n";
echo "📋 Minimum for Laravel 11: PHP $minimumPHP\n";

if (version_compare($currentPHP, $recommendedPHP, '<')) {
    echo "⚠️  UPGRADE RECOMMENDED: PHP $recommendedPHP available\n";
    echo "   Benefits:\n";
    echo "   - Performance improvements\n";
    echo "   - New language features\n";
    echo "   - Better security\n";
    echo "   - Laravel 11 compatibility\n";
} else {
    echo "✅ PHP version is optimal\n";
}

echo "\n";

// 5. Node.js and Frontend Dependencies
echo "5. FRONTEND DEPENDENCIES\n";
echo "=========================\n";

if (file_exists('package.json')) {
    echo "📦 package.json found\n";
    
    if (commandExists('npm')) {
        $npmOutdated = runCommand('npm outdated');
        if (!empty($npmOutdated['output'])) {
            echo "⚠️  Outdated npm packages:\n";
            foreach ($npmOutdated['output'] as $line) {
                if (!empty(trim($line))) {
                    echo "  📦 $line\n";
                }
            }
        } else {
            echo "✅ All npm packages are up to date\n";
        }
        
        // Check for security vulnerabilities
        $npmAudit = runCommand('npm audit --audit-level=moderate');
        if ($npmAudit['success']) {
            echo "✅ No npm security vulnerabilities found\n";
        } else {
            echo "⚠️  npm security issues detected - run 'npm audit' for details\n";
        }
    } else {
        echo "❌ npm not available\n";
    }
} else {
    echo "📋 No package.json found - consider adding Vite for asset compilation\n";
    echo "⚠️  IMPROVEMENT: Add modern frontend build tools:\n";
    echo "   - Laravel Vite integration\n";
    echo "   - Modern JavaScript compilation\n";
    echo "   - CSS preprocessing\n";
    echo "   - Asset optimization\n";
}

echo "\n";

// 6. Database and Performance
echo "6. DATABASE & PERFORMANCE\n";
echo "==========================\n";

// Check for .env file
if (file_exists('.env')) {
    echo "✅ Environment configuration found\n";
    
    // Parse .env for database info
    $envContent = file_get_contents('.env');
    if (strpos($envContent, 'DB_CONNECTION=mysql') !== false) {
        echo "📋 Database: MySQL detected\n";
    } elseif (strpos($envContent, 'DB_CONNECTION=postgresql') !== false) {
        echo "📋 Database: PostgreSQL detected\n";
    } else {
        echo "📋 Database: Configuration detected\n";
    }
    
    // Check for caching configuration
    if (strpos($envContent, 'CACHE_DRIVER=') !== false) {
        $cacheDriver = trim(explode('=', explode('CACHE_DRIVER=', $envContent)[1])[0]);
        echo "📋 Cache Driver: $cacheDriver\n";
        
        if ($cacheDriver === 'file') {
            echo "⚠️  UPGRADE OPPORTUNITY: Consider Redis/Memcached for better performance\n";
        }
    }
    
    // Check for queue configuration
    if (strpos($envContent, 'QUEUE_CONNECTION=') !== false) {
        $queueDriver = trim(explode('=', explode('QUEUE_CONNECTION=', $envContent)[1])[0]);
        echo "📋 Queue Driver: $queueDriver\n";
        
        if ($queueDriver === 'sync') {
            echo "⚠️  IMPROVEMENT: Consider Redis/database queues for background jobs\n";
        }
    }
} else {
    echo "❌ .env file not found\n";
}

echo "\n";

// 7. Security and Configuration
echo "7. SECURITY & CONFIGURATION\n";
echo "============================\n";

// Check for important Laravel security features
$securityChecks = [
    'config/app.php' => 'App configuration',
    'config/auth.php' => 'Authentication configuration',
    'config/cors.php' => 'CORS configuration',
    'app/Http/Middleware/Authenticate.php' => 'Authentication middleware'
];

foreach ($securityChecks as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description found\n";
    } else {
        echo "⚠️  $description missing\n";
    }
}

// Check for HTTPS redirect
if (file_exists('app/Http/Middleware/TrustProxies.php')) {
    echo "✅ Proxy trust middleware available\n";
} else {
    echo "⚠️  Consider adding TrustProxies middleware for HTTPS\n";
}

echo "\n";

// 8. Recommended Upgrades and Improvements
echo "8. RECOMMENDED UPGRADES\n";
echo "=======================\n";

$recommendations = [
    "HIGH PRIORITY" => [
        "🔄 Upgrade to Laravel 11 LTS for long-term support",
        "🔒 Run 'composer audit' and fix any security issues",
        "📦 Update outdated Composer packages",
        "🚀 Add Redis for caching and sessions"
    ],
    "MEDIUM PRIORITY" => [
        "⚡ Add Laravel Vite for modern asset compilation",
        "📊 Implement database query optimization",
        "🔧 Add Laravel Horizon for queue monitoring",
        "🎯 Implement API rate limiting"
    ],
    "LOW PRIORITY" => [
        "📈 Add Laravel Telescope for debugging",
        "🌐 Implement multi-language support",
        "📱 Add PWA capabilities",
        "🎨 Upgrade to Bootstrap 5.3"
    ]
];

foreach ($recommendations as $priority => $items) {
    echo "\n$priority:\n";
    foreach ($items as $item) {
        echo "  $item\n";
    }
}

echo "\n";

// 9. Quick Actions
echo "9. QUICK UPGRADE COMMANDS\n";
echo "=========================\n";

echo "To update dependencies:\n";
echo "  composer update --with-all-dependencies\n";
echo "  composer audit\n\n";

echo "To upgrade Laravel (manual process):\n";
echo "  1. Update composer.json Laravel version\n";
echo "  2. composer update\n";
echo "  3. Check Laravel upgrade guide\n";
echo "  4. Update configuration files\n\n";

echo "To add modern frontend tools:\n";
echo "  npm init -y\n";
echo "  npm install --save-dev vite laravel-vite-plugin\n";
echo "  php artisan install:api\n\n";

echo "To improve performance:\n";
echo "  php artisan config:cache\n";
echo "  php artisan route:cache\n";
echo "  php artisan view:cache\n";

echo "\n==========================================\n";
echo "         UPGRADE ANALYSIS COMPLETE\n";
echo "==========================================\n"; 