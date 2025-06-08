<?php

$testFiles = [
    'tests/Unit/ExampleTest.php',
    'tests/Unit/SimpleTest.php', 
    'tests/Unit/ConfigurationTest.php',
    'tests/Unit/LaravelBasicTest.php',
    'tests/Unit/HelperTest.php',
    'tests/Unit/RouteTest.php',
    'tests/Unit/VueComponentsTest.php',
];

$passedTests = [];
$failedTests = [];
$memoryErrorTests = [];

echo "Running individual unit tests...\n\n";

foreach ($testFiles as $testFile) {
    echo "Running: $testFile\n";
    
    $command = "vendor/bin/phpunit $testFile 2>&1";
    $output = [];
    $returnCode = 0;
    
    exec($command, $output, $returnCode);
    
    $outputStr = implode("\n", $output);
    
    if ($returnCode === 0) {
        $passedTests[] = $testFile;
        echo "✅ PASSED\n";
    } elseif (strpos($outputStr, 'memory size') !== false) {
        $memoryErrorTests[] = $testFile;
        echo "❌ MEMORY ERROR\n";
    } else {
        $failedTests[] = $testFile;
        echo "❌ FAILED\n";
        // Show first few lines of error
        $lines = explode("\n", $outputStr);
        for ($i = 0; $i < min(3, count($lines)); $i++) {
            if (trim($lines[$i])) {
                echo "   " . $lines[$i] . "\n";
            }
        }
    }
    
    echo "\n";
}

echo "\n=== SUMMARY ===\n";
echo "Passed: " . count($passedTests) . "\n";
echo "Failed: " . count($failedTests) . "\n"; 
echo "Memory Errors: " . count($memoryErrorTests) . "\n";

if (!empty($failedTests)) {
    echo "\nFailed tests:\n";
    foreach ($failedTests as $test) {
        echo "- $test\n";
    }
}

if (!empty($memoryErrorTests)) {
    echo "\nMemory error tests:\n";
    foreach ($memoryErrorTests as $test) {
        echo "- $test\n";
    }
}