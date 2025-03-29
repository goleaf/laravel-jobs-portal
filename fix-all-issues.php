<?php

/**
 * Laravel Memory Fix Script
 * 
 * This script attempts to fix memory issues in Laravel applications by:
 * 1. Setting unlimited memory
 * 2. Installing required dependencies without scripts
 * 3. Creating empty cache files
 * 4. Temporarily disabling problematic service providers
 * 5. Running optimized autoloader
 */

// Set unlimited memory
ini_set('memory_limit', '-1');
echo "Memory limit set to unlimited\n";

// Create bootstrap/cache if it doesn't exist
if (!is_dir(__DIR__ . '/bootstrap/cache')) {
    mkdir(__DIR__ . '/bootstrap/cache', 0755, true);
    echo "Created bootstrap/cache directory\n";
}

// 1. Create empty package manifest files
file_put_contents(
    __DIR__ . '/bootstrap/cache/packages.php',
    '<?php return [];'
);
file_put_contents(
    __DIR__ . '/bootstrap/cache/services.php',
    '<?php return [];'
);
echo "Created empty manifest files\n";

// 2. Fix app config to disable problematic providers
$configPath = __DIR__ . '/config/app.php';
if (file_exists($configPath)) {
    // Backup original
    copy($configPath, $configPath . '.bak');
    echo "Created backup of app config\n";
    
    $appConfig = file_get_contents($configPath);
    
    // Add dont_discover to prevent package discovery
    if (strpos($appConfig, "'dont_discover'") === false) {
        $appConfig = str_replace(
            "'providers' => [",
            "'dont_discover' => ['*'],\n\n    'providers' => [",
            $appConfig
        );
        echo "Added dont_discover to app config\n";
    }
    
    // Temporarily disable problematic providers
    $problemProviders = [
        "App\\Providers\\AuthServiceProvider::class",
        "Livewire\\LivewireServiceProvider::class",
        "Laravel\\Socialite\\SocialiteServiceProvider::class"
    ];
    
    foreach ($problemProviders as $provider) {
        $pattern = "/$provider,/";
        $disabled = "// Temporarily disabled: $provider,";
        $appConfig = preg_replace($pattern, $disabled, $appConfig);
        echo "Disabled provider: $provider\n";
    }
    
    file_put_contents($configPath, $appConfig);
    echo "Updated app config file\n";
}

// 3. Install required dependencies without scripts
echo "\nInstalling required dependencies without running scripts...\n";
passthru('COMPOSER_MEMORY_LIMIT=-1 composer require laravel/framework:^10.0 laravel/sanctum laravel/socialite livewire/livewire --no-scripts');

// 4. Generate optimized autoloader
echo "\nGenerating optimized autoloader without scripts...\n";
passthru('COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-scripts');

// 5. Cache configuration with disabled problematic providers
echo "\nCaching configuration...\n";
passthru('php -d memory_limit=-1 artisan config:cache');

// 6. Test if artisan works now
echo "\nTesting artisan command...\n";
passthru('php -d memory_limit=-1 artisan --version', $artisan_code);

// 7. Restore config if artisan works
if ($artisan_code === 0) {
    echo "\nARTISAN IS WORKING! The fix was successful.\n";
    
    // Attempt to run the composer update without scripts
    echo "\nRunning composer update without scripts...\n";
    passthru('COMPOSER_MEMORY_LIMIT=-1 composer update --no-scripts', $composer_code);
    
    if ($composer_code === 0) {
        echo "\nSUCCESS: Composer update completed without errors.\n";
    } else {
        echo "\nWarning: Composer update failed with code $composer_code\n";
    }
    
    // Check if user wants to restore original config
    echo "\nDo you want to restore the original app config? (y/n) ";
    $answer = trim(fgets(STDIN));
    
    if (strtolower($answer) === 'y') {
        if (file_exists($configPath . '.bak')) {
            rename($configPath . '.bak', $configPath);
            echo "Original app config restored.\n";
            
            // Clear config cache
            passthru('php -d memory_limit=-1 artisan config:clear');
        }
    } else {
        echo "Keeping modified app config with disabled providers.\n";
        echo "You can manually restore the original config by copying config/app.php.bak to config/app.php\n";
    }
} else {
    echo "\nFix didn't work. Consider the following options:\n";
    echo "1. Edit your AuthServiceProvider.php and SocialiteServiceProvider.php to handle missing dependencies\n";
    echo "2. Create a custom deploy script that avoids running package discovery\n";
    echo "3. Modify your composer.json to skip scripts during updates\n";
    echo "4. Contact your hosting provider to increase PHP memory limit\n";
    
    // Restore original config
    if (file_exists($configPath . '.bak')) {
        rename($configPath . '.bak', $configPath);
        echo "Original app config restored.\n";
    }
} 