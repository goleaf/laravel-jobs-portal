<?php

ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);

echo "Starting memory debug...\n";
echo "Initial memory: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";

// Try to load Laravel bootstrap
try {
    echo "Loading vendor/autoload.php...\n";
    require_once __DIR__ . '/vendor/autoload.php';
    echo "Memory after autoload: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    echo "Loading Laravel app...\n";
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "Memory after app: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    echo "Loading kernel...\n";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    echo "Memory after kernel: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    echo "Laravel loaded successfully!\n";
    echo "Final memory: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    echo "Peak memory: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Memory at error: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
} 