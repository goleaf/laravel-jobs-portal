<?php

/**
 * Laravel Final Fix Script
 * 
 * This script fixes "files" class not found errors during asset publishing by:
 * 1. Setting unlimited memory
 * 2. Modifying composer.json to skip problematic post-update-cmd script
 * 3. Running composer update with memory optimizations
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
    
    // Remove vendor:publish from post-update-cmd
    if (isset($composerJson['scripts']['post-update-cmd'])) {
        // Save the original scripts
        $composerJson['scripts']['_original_post_update_cmd'] = $composerJson['scripts']['post-update-cmd'];
        
        // Remove the vendor:publish script
        $newScripts = [];
        foreach ($composerJson['scripts']['post-update-cmd'] as $script) {
            if (strpos($script, 'vendor:publish') === false) {
                $newScripts[] = $script;
            } else {
                echo "Disabled script: $script\n";
            }
        }
        
        $composerJson['scripts']['post-update-cmd'] = $newScripts;
        echo "Modified composer.json to skip vendor:publish\n";
    }
    
    // Also remove package:discover from post-autoload-dump if it exists
    if (isset($composerJson['scripts']['post-autoload-dump'])) {
        // Save the original scripts
        $composerJson['scripts']['_original_post_autoload_dump'] = $composerJson['scripts']['post-autoload-dump'];
        
        // Remove the package:discover script
        $newScripts = [];
        foreach ($composerJson['scripts']['post-autoload-dump'] as $script) {
            if (strpos($script, 'package:discover') === false) {
                $newScripts[] = $script;
            } else {
                echo "Disabled script: $script\n";
            }
        }
        
        $composerJson['scripts']['post-autoload-dump'] = $newScripts;
        echo "Modified composer.json to skip package:discover\n";
    }
    
    file_put_contents($composerJsonPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// 3. Run composer update with memory optimization
echo "\nRunning composer update with memory optimization...\n";
$command = 'COMPOSER_MEMORY_LIMIT=-1 COMPOSER_PROCESS_TIMEOUT=600 composer update';
passthru($command, $update_code);

if ($update_code === 0) {
    echo "\nSUCCESS: Composer update completed successfully!\n";
    
    // Test artisan
    echo "\nTesting if artisan works now...\n";
    passthru('php artisan --version', $artisan_code);
    
    if ($artisan_code === 0) {
        echo "\nArisan is working! Your Laravel app should now function correctly.\n";
    } else {
        echo "\nArtisan command is still having issues. Try running: php -d memory_limit=-1 artisan\n";
    }
    
    echo "\nDo you want to keep these fixes in composer.json? (y/n) ";
    $answer = trim(fgets(STDIN));
    
    if (strtolower($answer) !== 'y') {
        if (file_exists($composerJsonPath . '.bak')) {
            rename($composerJsonPath . '.bak', $composerJsonPath);
            echo "Original composer.json restored.\n";
        }
    } else {
        echo "Keeping modified composer.json with script changes.\n";
        echo "You can restore the original anytime by copying composer.json.bak to composer.json\n";
    }
} else {
    echo "\nError: Composer update failed with code $update_code\n";
    
    // Restore original composer.json
    if (file_exists($composerJsonPath . '.bak')) {
        rename($composerJsonPath . '.bak', $composerJsonPath);
        echo "Original composer.json restored due to error.\n";
    }
    
    echo "\nTry running these commands manually:\n";
    echo "1. COMPOSER_MEMORY_LIMIT=-1 composer update --no-scripts\n";
    echo "2. php -d memory_limit=-1 artisan config:clear\n";
} 