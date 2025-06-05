<?php

/**
 * Fix Validation Test Files - Replace broken "new ()" with proper class names
 */

$testFiles = glob('tests/Feature/Requests/*Test.php');

echo "🔧 Fixing validation test files...\n\n";

foreach ($testFiles as $testFile) {
    $content = file_get_contents($testFile);
    
    // Extract the class name from the test file name
    $testFileName = basename($testFile, 'Test.php');
    $requestClassName = $testFileName;
    
    // Replace broken "new ()" with proper class instantiation
    $content = str_replace('new ()', "new {$requestClassName}()", $content);
    
    // Write back the fixed content
    file_put_contents($testFile, $content);
    
    echo "   ✅ Fixed: $testFile\n";
}

echo "\n🎉 All validation test files have been fixed!\n"; 