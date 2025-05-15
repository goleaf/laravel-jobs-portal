<?php

// Set unlimited memory
ini_set('memory_limit', '-1');

// Path to app config file
$configPath = __DIR__ . '/config/app.php';

// Read the current config
$appConfig = file_get_contents($configPath);

// Check if file exists
if (!$appConfig) {
    echo "Error: Could not read config file at $configPath\n";
    exit(1);
}

// Backup the original file
file_put_contents($configPath . '.bak', $appConfig);
echo "Created backup of app.php config file.\n";

// Find the AuthServiceProvider in the providers array
$pattern = "/App\\\\Providers\\\\AuthServiceProvider::class,/";
$disabled = "// Temporarily disabled: App\\Providers\\AuthServiceProvider::class,";
$appConfig = preg_replace($pattern, $disabled, $appConfig);

// Write modified config back
file_put_contents($configPath, $appConfig);
echo "Temporarily disabled AuthServiceProvider in app.php config.\n";

// Clear config cache
echo "Clearing config cache...\n";
passthru('php -d memory_limit=-1 artisan config:clear');

// Run artisan list to see if it works now
echo "Running artisan to test if it works...\n";
passthru('php -d memory_limit=-1 artisan --version', $return_code);

// Check if it worked
if ($return_code === 0) {
    echo "SUCCESS: The fix worked!\n";
    
    // Now try to run composer update
    echo "Running composer update with unlimited memory...\n";
    passthru('COMPOSER_MEMORY_LIMIT=-1 composer update', $update_code);
    
    if ($update_code === 0) {
        echo "Composer update completed successfully!\n";
    } else {
        echo "Composer update failed with code: $update_code\n";
    }
    
    // Check if the user wants to keep the fix or restore original
    echo "\nDo you want to restore the original config? (y/n) ";
    $answer = trim(fgets(STDIN));
    
    if (strtolower($answer) === 'y') {
        // Restore original config
        rename($configPath . '.bak', $configPath);
        echo "Original app.php config file restored.\n";
        
        // Clear config cache
        passthru('php -d memory_limit=-1 artisan config:clear');
    } else {
        echo "Keeping modified config with AuthServiceProvider disabled.\n";
        echo "You can restore the original config by replacing config/app.php with config/app.php.bak when needed.\n";
    }
} else {
    echo "Fix didn't work. Restoring original config...\n";
    rename($configPath . '.bak', $configPath);
    echo "Original app.php config file restored.\n";
} 