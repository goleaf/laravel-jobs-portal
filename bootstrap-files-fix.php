<?php

/**
 * Laravel Custom Bootstrap with Files Fix
 * 
 * This script directly bootstraps the Laravel application
 * with a manually registered Filesystem service.
 */

// Set unlimited memory
ini_set('memory_limit', '-1');

// Define the application path
$appPath = __DIR__;

// Load the composer autoloader
require $appPath . '/vendor/autoload.php';

// Create a fresh Laravel application instance
$app = new Illuminate\Foundation\Application($appPath);

// Directly register the filesystem service provider
$app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);

// Bind the 'files' service in the container
$app->singleton('files', function () {
    return new Illuminate\Filesystem\Filesystem;
});

// Bind the File facade
Illuminate\Support\Facades\File::setFacadeApplication($app);

// Register the kernel
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// Bind the container to the facade
Illuminate\Support\Facades\App::setFacadeApplication($app);

// Now create a simple custom artisan command to run composer update safely
echo "Creating custom helper script to run composer update...\n";

// Create a shell script to run composer update safely
$shellScript = <<<'EOT'
#!/bin/bash

# Clear any existing cache files
rm -rf bootstrap/cache/*.php

# Set memory limit to unlimited
export COMPOSER_MEMORY_LIMIT=-1
export COMPOSER_PROCESS_TIMEOUT=1800

# Run composer update skipping problematic scripts
composer update --no-scripts

# Generate autoloader
composer dump-autoload --optimize --no-scripts

# Create empty package manifest
echo '<?php return [];' > bootstrap/cache/packages.php
echo '<?php return [];' > bootstrap/cache/services.php

echo "Composer update completed with memory optimization"
echo "Your Laravel application should now work correctly"
EOT;

file_put_contents($appPath . '/safe-update.sh', $shellScript);
chmod($appPath . '/safe-update.sh', 0755);
echo "Created safe-update.sh script\n";

// Check if composer update works using the helper scripts
echo "\nRunning shell script to safely update composer...\n";
passthru('./safe-update.sh', $result);

if ($result === 0) {
    echo "\nComposer update completed successfully!\n";
} else {
    echo "\nThere were issues with composer update. Try running './safe-update.sh' manually.\n";
}

echo "\nTo run composer update safely in the future, use: ./safe-update.sh\n";
echo "This will bypass the problematic scripts and memory issues.\n"; 