<?php
// Web debug page for Laravel application

ini_set('memory_limit', '2G');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Debug Page</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .section { margin: 20px 0; padding: 10px; border: 1px solid #ccc; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>

<h1>🔧 Laravel Application Debug</h1>

<?php
echo "<div class='section'>";
echo "<h2>System Information</h2>";
echo "<p class='success'>PHP Version: " . PHP_VERSION . "</p>";
echo "<p class='success'>Memory Limit: " . ini_get('memory_limit') . "</p>";
echo "<p class='success'>Max Execution Time: " . ini_get('max_execution_time') . "</p>";
echo "<p class='success'>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>Laravel Bootstrap Test</h2>";

try {
    // Test autoloader
    if (file_exists('../vendor/autoload.php')) {
        require_once '../vendor/autoload.php';
        echo "<p class='success'>✅ Autoloader loaded</p>";
    } else {
        echo "<p class='error'>❌ Autoloader not found</p>";
        exit;
    }
    
    // Test .env
    if (file_exists('../.env')) {
        echo "<p class='success'>✅ .env file exists</p>";
    } else {
        echo "<p class='error'>❌ .env file missing</p>";
    }
    
    // Test Laravel bootstrap
    if (file_exists('../bootstrap/app.php')) {
        $app = require_once '../bootstrap/app.php';
        echo "<p class='success'>✅ Laravel app bootstrapped</p>";
        
        // Try to handle a request
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        echo "<p class='success'>✅ HTTP Kernel created</p>";
        
        // Test basic Laravel services
        try {
            $config = $app['config'];
            echo "<p class='success'>✅ Config service loaded</p>";
            echo "<p>App Name: " . $config->get('app.name', 'Not set') . "</p>";
            echo "<p>App Environment: " . $config->get('app.env', 'Not set') . "</p>";
            echo "<p>App Debug: " . ($config->get('app.debug') ? 'Enabled' : 'Disabled') . "</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Config service failed: " . $e->getMessage() . "</p>";
        }
        
        // Test database
        try {
            $db = $app['db'];
            $connection = $db->connection();
            $connection->getPdo();
            echo "<p class='success'>✅ Database connected</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
        }
        
        // Test view
        try {
            $view = $app['view'];
            echo "<p class='success'>✅ View service loaded</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ View service failed: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Laravel bootstrap file not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Critical error during bootstrap:</p>";
    echo "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p class='error'>❌ Fatal error during bootstrap:</p>";
    echo "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>Environment Variables</h2>";
echo "<p>APP_ENV: " . (getenv('APP_ENV') ?: 'Not set') . "</p>";
echo "<p>APP_DEBUG: " . (getenv('APP_DEBUG') ?: 'Not set') . "</p>";
echo "<p>APP_URL: " . (getenv('APP_URL') ?: 'Not set') . "</p>";
echo "<p>DB_CONNECTION: " . (getenv('DB_CONNECTION') ?: 'Not set') . "</p>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>File System Check</h2>";
$paths = [
    '../storage' => 'Storage directory',
    '../storage/logs' => 'Logs directory',
    '../storage/framework' => 'Framework directory',
    '../bootstrap/cache' => 'Bootstrap cache',
    '../vendor' => 'Vendor directory',
    '../config' => 'Config directory'
];

foreach ($paths as $path => $description) {
    if (is_dir($path)) {
        if (is_writable($path)) {
            echo "<p class='success'>✅ {$description} - exists and writable</p>";
        } else {
            echo "<p class='warning'>⚠️ {$description} - exists but not writable</p>";
        }
    } else {
        echo "<p class='error'>❌ {$description} - doesn't exist</p>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>Memory Usage</h2>";
echo "<p>Current: " . number_format(memory_get_usage() / 1024 / 1024, 2) . " MB</p>";
echo "<p>Peak: " . number_format(memory_get_peak_usage() / 1024 / 1024, 2) . " MB</p>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>Next Steps</h2>";
if (isset($app)) {
    echo "<p class='success'>✅ Laravel is working! The issue might be with specific routes or views.</p>";
    echo "<p>Try accessing: <a href='/'>Homepage</a> | <a href='/api/test'>API Test</a></p>";
} else {
    echo "<p class='error'>❌ Laravel failed to bootstrap. Check the errors above.</p>";
}
echo "</div>";

?>

</body>
</html> 