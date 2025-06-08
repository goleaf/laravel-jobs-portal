<?php

/**
 * Fix Missing Files Class for Laravel
 * 
 * This script fixes the "Class files does not exist" error by ensuring 
 * the Filesystem ServiceProvider is properly registered
 */

// Set unlimited memory
ini_set('memory_limit', '-1');
echo "Memory limit set to unlimited\n";

// Path to app config
$configPath = __DIR__ . '/config/app.php';
if (!file_exists($configPath)) {
    echo "Error: Could not find app.php config at $configPath\n";
    exit(1);
}

// Backup original
copy($configPath, $configPath . '.bak');
echo "Created backup of app.php config\n";

// Read app config
$appConfig = file_get_contents($configPath);

// Check if Filesystem provider is present and uncommented
$filesystemProvider = 'Illuminate\Filesystem\FilesystemServiceProvider::class';
if (strpos($appConfig, $filesystemProvider) === false) {
    // Filesystem provider is missing, add it
    $providersPos = strpos($appConfig, "'providers' => [");
    if ($providersPos !== false) {
        $insertPos = $providersPos + strlen("'providers' => [");
        $newConfig = substr($appConfig, 0, $insertPos) . "\n        " . $filesystemProvider . ",\n" . substr($appConfig, $insertPos);
        file_put_contents($configPath, $newConfig);
        echo "Added FilesystemServiceProvider to app config\n";
    }
} else {
    // Check if it's commented out
    if (preg_match('/\/\/\s*' . preg_quote($filesystemProvider, '/') . '/', $appConfig) || 
        preg_match('/#\s*' . preg_quote($filesystemProvider, '/') . '/', $appConfig)) {
        // It's commented out, uncomment it
        $appConfig = preg_replace('/\/\/\s*(' . preg_quote($filesystemProvider, '/') . ')/', '$1', $appConfig);
        $appConfig = preg_replace('/#\s*(' . preg_quote($filesystemProvider, '/') . ')/', '$1', $appConfig);
        file_put_contents($configPath, $appConfig);
        echo "Uncommented FilesystemServiceProvider in app config\n";
    } else {
        echo "FilesystemServiceProvider already present and active\n";
    }
}

// Fix aliases section to ensure "File" facade is registered
$fileAlias = "'File' => Illuminate\Support\Facades\File::class";
if (strpos($appConfig, $fileAlias) === false) {
    // File alias is missing, add it
    $aliasesPos = strpos($appConfig, "'aliases' => [");
    if ($aliasesPos !== false) {
        $insertPos = $aliasesPos + strlen("'aliases' => [");
        $newConfig = substr($appConfig, 0, $insertPos) . "\n        " . $fileAlias . ",\n" . substr($appConfig, $insertPos);
        file_put_contents($configPath, $newConfig);
        echo "Added File facade alias to app config\n";
    }
} else {
    // Check if it's commented out
    if (preg_match('/\/\/\s*' . preg_quote($fileAlias, '/') . '/', $appConfig) || 
        preg_match('/#\s*' . preg_quote($fileAlias, '/') . '/', $appConfig)) {
        // It's commented out, uncomment it
        $appConfig = preg_replace('/\/\/\s*(' . preg_quote($fileAlias, '/') . ')/', '$1', $appConfig);
        $appConfig = preg_replace('/#\s*(' . preg_quote($fileAlias, '/') . ')/', '$1', $appConfig);
        file_put_contents($configPath, $appConfig);
        echo "Uncommented File facade alias in app config\n";
    } else {
        echo "File facade alias already present and active\n";
    }
}

// Clear config cache
echo "\nClearing config cache...\n";
passthru('php -d memory_limit=-1 artisan config:clear');

// Rebuild service provider cache
echo "Fixing cache files...\n";
if (!is_dir(__DIR__ . '/bootstrap/cache')) {
    mkdir(__DIR__ . '/bootstrap/cache', 0755, true);
}

// Create a basic services.php with the filesystem service
$filesystemService = [
    'providers' => [
        Illuminate\Filesystem\FilesystemServiceProvider::class,
    ],
    'eager' => [
        Illuminate\Filesystem\FilesystemServiceProvider::class,
    ],
    'deferred' => [],
];

file_put_contents(
    __DIR__ . '/bootstrap/cache/services.php',
    '<?php return ' . var_export($filesystemService, true) . ';'
);
echo "Created services.php with FilesystemServiceProvider\n";

// Test if artisan works now
echo "\nTesting if artisan works now...\n";
passthru('php -d memory_limit=-1 artisan --version', $artisan_code);

if ($artisan_code === 0) {
    echo "\nSUCCESS! The 'files' class error has been fixed. Laravel should now work correctly.\n";
} else {
    echo "\nError: Artisan command still has issues. Try these additional steps:\n";
    echo "1. Run: php artisan cache:clear\n";
    echo "2. Run: php artisan view:clear\n";
    echo "3. Run: php artisan route:clear\n";
    echo "4. Check if any other service providers are missing in config/app.php\n";
    
    // Restore original if user wants
    echo "\nDo you want to restore the original app.php config? (y/n) ";
    $answer = trim(fgets(STDIN));
    
    if (strtolower($answer) === 'y') {
        if (file_exists($configPath . '.bak')) {
            rename($configPath . '.bak', $configPath);
            echo "Original app.php config restored.\n";
        }
    }
} 