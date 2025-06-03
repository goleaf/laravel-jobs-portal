<?php
// Debug script for Laravel application

ini_set('memory_limit', '2G');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "🔧 Laravel Application Debug Script\n";
echo "===================================\n\n";

// Check basic PHP functionality
echo "✅ PHP Version: " . PHP_VERSION . "\n";
echo "💾 Memory Limit: " . ini_get('memory_limit') . "\n";
echo "⏱️  Max Execution Time: " . ini_get('max_execution_time') . "\n";

// Check if Laravel can bootstrap at all
echo "\n🔄 Testing Laravel Bootstrap...\n";

try {
    // Start with minimal bootstrap
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✅ Autoloader loaded successfully\n";
    
    // Check if .env exists and is readable
    if (file_exists(__DIR__ . '/.env')) {
        echo "✅ .env file exists\n";
        $envContent = file_get_contents(__DIR__ . '/.env');
        echo "✅ .env file readable (" . strlen($envContent) . " bytes)\n";
    } else {
        echo "❌ .env file missing\n";
    }
    
    // Test minimal Laravel bootstrap
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Laravel app.php loaded\n";
    
    // Try to create kernel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    echo "✅ Console kernel created\n";
    
} catch (Exception $e) {
    echo "❌ Error during bootstrap: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

// Check database connection
echo "\n🗄️  Testing Database Connection...\n";
try {
    if (isset($app)) {
        $db = $app['db'];
        $db->connection()->getPdo();
        echo "✅ Database connection successful\n";
    } else {
        echo "⚠️  Cannot test database - app not bootstrapped\n";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Check essential directories
echo "\n📁 Checking Directory Permissions...\n";
$dirs = ['storage', 'storage/logs', 'storage/framework', 'storage/app', 'bootstrap/cache'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ {$dir} - writable\n";
        } else {
            echo "❌ {$dir} - not writable\n";
        }
    } else {
        echo "❌ {$dir} - doesn't exist\n";
    }
}

// Check composer autoload optimization
echo "\n🎵 Checking Composer Status...\n";
if (file_exists(__DIR__ . '/vendor/composer/autoload_classmap.php')) {
    echo "✅ Composer classmap exists\n";
} else {
    echo "⚠️  Composer classmap missing - run 'composer dump-autoload'\n";
}

// Memory usage
echo "\n💾 Memory Usage Report:\n";
echo "Current: " . number_format(memory_get_usage() / 1024 / 1024, 2) . " MB\n";
echo "Peak: " . number_format(memory_get_peak_usage() / 1024 / 1024, 2) . " MB\n";

echo "\n✅ Debug script completed!\n";
?> 