<?php

ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);

echo "Starting Laravel models test...\n";
echo "Initial memory: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";

require_once __DIR__ . '/vendor/autoload.php';

try {
    // Bootstrap Laravel with minimal overhead
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "Memory after bootstrap: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    // Test basic model loading
    $user = new \App\Models\User();
    echo "User model created successfully\n";
    echo "Memory after User model: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    $job = new \App\Models\Job();
    echo "Job model created successfully\n";
    echo "Memory after Job model: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    $company = new \App\Models\Company();
    echo "Company model created successfully\n";
    echo "Memory after Company model: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    
    // Test model relationships (without database calls)
    echo "Testing relationships definitions...\n";
    $jobRelations = [
        'company', 'currency', 'salaryPeriod', 'jobType', 
        'careerLevel', 'functionalArea', 'jobShift', 'degreeLevel'
    ];
    
    foreach ($jobRelations as $relation) {
        if (method_exists($job, $relation)) {
            echo "✓ Job::{$relation}() exists\n";
        } else {
            echo "✗ Job::{$relation}() missing\n";
        }
    }
    
    $userRelations = ['candidate', 'company', 'country', 'state', 'city'];
    foreach ($userRelations as $relation) {
        if (method_exists($user, $relation)) {
            echo "✓ User::{$relation}() exists\n";
        } else {
            echo "✗ User::{$relation}() missing\n";
        }
    }
    
    echo "All tests completed successfully!\n";
    echo "Final memory: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
    echo "Peak memory: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Memory at error: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
} 