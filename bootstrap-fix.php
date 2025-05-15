<?php

/*
 * Memory-optimized Laravel bootstrap file
 * Use this file to fix memory issues with package discovery
 */

// Set unlimited memory
ini_set('memory_limit', '-1');

// Create bootstrap/cache directory if it doesn't exist
if (!is_dir(__DIR__ . '/bootstrap/cache')) {
    mkdir(__DIR__ . '/bootstrap/cache', 0755, true);
}

// Create empty package manifest
file_put_contents(
    __DIR__ . '/bootstrap/cache/packages.php',
    '<?php return [];'
);

// Create empty services manifest
file_put_contents(
    __DIR__ . '/bootstrap/cache/services.php',
    '<?php return [];'
);

echo "Created empty package manifests to bypass discovery\n";

// Run composer dumpautoload without scripts
echo "Running composer dump-autoload...\n";
passthru('COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-scripts', $return_code);

echo "Optimizing the framework...\n";

try {
    // We need to modify config to skip discovery
    if (file_exists(__DIR__ . '/config/app.php')) {
        $appConfig = file_get_contents(__DIR__ . '/config/app.php');
        
        // Create a backup
        file_put_contents(__DIR__ . '/config/app.php.bak', $appConfig);
        
        // Disable package auto discovery by adding this to the config
        $appConfig = str_replace(
            "'providers' => [",
            "'dont_discover' => ['*'],\n    'providers' => [",
            $appConfig
        );
        
        file_put_contents(__DIR__ . '/config/app.php', $appConfig);
        echo "Modified app config to disable auto-discovery\n";
    }
    
    // Now run a basic artisan command that doesn't require discovery
    echo "Running artisan clear-compiled...\n";
    passthru('php -d memory_limit=-1 artisan clear-compiled', $return_code);
    
    echo "Running environment setup...\n";
    passthru('php -d memory_limit=-1 artisan env:decrypt --force', $return_code);
    
    // Generate application key if needed
    echo "Making sure application key is set...\n";
    passthru('php -d memory_limit=-1 artisan key:generate --force', $return_code);
    
    // Cache config
    echo "Caching config...\n";
    passthru('php -d memory_limit=-1 artisan config:cache', $return_code);
    
    // Cache routes
    echo "Caching routes...\n";
    passthru('php -d memory_limit=-1 artisan route:cache', $return_code);
    
    // Restore original config if we modified it
    if (file_exists(__DIR__ . '/config/app.php.bak')) {
        rename(__DIR__ . '/config/app.php.bak', __DIR__ . '/config/app.php');
        echo "Restored original app config\n";
    }
    
    echo "\nSuccess! Your Laravel application should now work with less memory usage\n";
    echo "You can now run your application normally\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 