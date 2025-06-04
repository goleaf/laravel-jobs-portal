<?php

ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);

echo "Starting minimal Laravel test...\n";
echo "Initial memory: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";

require_once __DIR__ . '/vendor/autoload.php';

try {
    // Create minimal Laravel app without console kernel
    $app = new Illuminate\Foundation\Application(
        $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
    );
    
    echo "Memory after app creation: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    // Set environment to testing
    $app->detectEnvironment(function () {
        return 'testing';
    });
    
    echo "Memory after environment: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    // Load configuration
    $app->make('Illuminate\Foundation\Bootstrap\LoadConfiguration')->bootstrap($app);
    echo "Memory after config: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    // Register providers
    $app->make('Illuminate\Foundation\Bootstrap\RegisterProviders')->bootstrap($app);
    echo "Memory after providers: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    // Test basic model creation
    $user = new \App\Models\User();
    echo "✓ User model created\n";
    
    $job = new \App\Models\Job();
    echo "✓ Job model created\n";
    
    $company = new \App\Models\Company();
    echo "✓ Company model created\n";
    
    echo "Final memory: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    echo "Peak memory: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB\n";
    echo "Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Memory at error: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
} 