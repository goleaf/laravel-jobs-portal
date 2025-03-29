<?php

// Set unlimited memory
ini_set('memory_limit', '-1');

// Disable error reporting to prevent fatal errors from stopping execution
error_reporting(0);

// Function to get memory usage in a human-readable format
function getMemoryUsage() {
    $mem = memory_get_usage();
    return round($mem / 1024 / 1024, 2) . ' MB';
}

echo "Starting memory usage: " . getMemoryUsage() . "\n";

// Load composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

echo "Memory after autoloader: " . getMemoryUsage() . "\n";

// Skip package discovery by creating empty manifest files
if (!is_dir(__DIR__ . '/bootstrap/cache')) {
    mkdir(__DIR__ . '/bootstrap/cache', 0755, true);
}

file_put_contents(
    __DIR__ . '/bootstrap/cache/packages.php',
    '<?php return [];'
);

file_put_contents(
    __DIR__ . '/bootstrap/cache/services.php',
    '<?php return [];'
);

echo "Created empty manifest files\n";

// Now try to load Laravel app without running discovery
$app = new Illuminate\Foundation\Application(__DIR__);
echo "Memory after app creation: " . getMemoryUsage() . "\n";

// Try to find service providers that might be causing issues
$providers = [
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    // Add more Laravel core providers here
];

foreach ($providers as $provider) {
    echo "Loading provider: " . $provider . "... ";
    $before = memory_get_usage();
    try {
        $instance = new $provider($app);
        echo "OK - ";
    } catch (\Exception $e) {
        echo "FAILED (" . $e->getMessage() . ") - ";
    }
    $after = memory_get_usage();
    $diff = round(($after - $before) / 1024 / 1024, 2);
    echo "Memory impact: " . $diff . " MB\n";
    
    if ($diff > 100) {
        echo "WARNING: This provider is using a lot of memory!\n";
    }
}

echo "\nFinal memory usage: " . getMemoryUsage() . "\n";
echo "Done. Check for providers with high memory impact above.\n"; 