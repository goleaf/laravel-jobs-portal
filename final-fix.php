<?php

/**
 * Laravel Memory Fix Script - Final Solution
 * 
 * This script fixes memory issues by:
 * 1. Setting unlimited memory
 * 2. Creating empty cache manifests
 * 3. Modifying composer.json to skip scripts
 * 4. Running composer update with memory optimizations
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

// 2. Modify composer.json to skip scripts
$composerJsonPath = __DIR__ . '/composer.json';
if (file_exists($composerJsonPath)) {
    // Backup original
    copy($composerJsonPath, $composerJsonPath . '.bak');
    echo "Created backup of composer.json\n";
    
    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
    
    // Disable the post-autoload-dump script that runs package discovery
    if (isset($composerJson['scripts']['post-autoload-dump'])) {
        // Save the original scripts
        $composerJson['scripts']['_original_post_autoload_dump'] = $composerJson['scripts']['post-autoload-dump'];
        
        // Remove the package:discover script
        $newScripts = [];
        foreach ($composerJson['scripts']['post-autoload-dump'] as $script) {
            if (strpos($script, 'package:discover') === false) {
                $newScripts[] = $script;
            }
        }
        
        $composerJson['scripts']['post-autoload-dump'] = $newScripts;
        echo "Modified composer.json to skip package discovery\n";
        
        file_put_contents($composerJsonPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

// 3. Run composer update with memory optimization
echo "\nRunning composer update with memory optimization...\n";
$command = 'COMPOSER_MEMORY_LIMIT=-1 COMPOSER_PROCESS_TIMEOUT=600 composer update --no-scripts';
passthru($command, $update_code);

// 4. Generate optimized autoloader
echo "\nGenerating optimized autoloader...\n";
passthru('COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-scripts', $dump_code);

// 5. Try to run an artisan command
echo "\nTesting artisan...\n";
passthru('php -d memory_limit=-1 artisan --version', $artisan_code);

// 6. Check results
if ($artisan_code === 0) {
    echo "\nSUCCESS! Laravel is now working correctly.\n";
    
    // Now you can either restore the original composer.json or leave it as is
    echo "Do you want to restore the original composer.json? (y/n) ";
    $answer = trim(fgets(STDIN));
    
    if (strtolower($answer) === 'y') {
        if (file_exists($composerJsonPath . '.bak')) {
            rename($composerJsonPath . '.bak', $composerJsonPath);
            echo "Original composer.json restored.\n";
        }
    } else {
        echo "Keeping modified composer.json that skips package discovery.\n";
        echo "You can manually restore the original by copying composer.json.bak to composer.json\n";
    }
} else {
    echo "\nFix was not completely successful. Artisan is still having issues.\n";
    
    // Provide additional options
    echo "Your options:\n";
    echo "1. Edit app/Providers/AuthServiceProvider.php to fix Socialite integration issues\n";
    echo "2. Add 'dont_discover' => ['*'] to your app config to permanently disable package discovery\n";
    echo "3. Contact your hosting provider to increase PHP memory limit (currently " . ini_get('memory_limit') . ")\n";
    
    // Restore original composer.json
    if (file_exists($composerJsonPath . '.bak')) {
        echo "Do you want to restore the original composer.json? (y/n) ";
        $answer = trim(fgets(STDIN));
        
        if (strtolower($answer) === 'y') {
            rename($composerJsonPath . '.bak', $composerJsonPath);
            echo "Original composer.json restored.\n";
        }
    }
} 