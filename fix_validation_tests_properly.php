<?php

/**
 * Properly Fix Validation Test Files
 */

$testFiles = glob('tests/Feature/Requests/*Test.php');

echo "🔧 Properly fixing validation test files...\n\n";

foreach ($testFiles as $testFile) {
    $content = file_get_contents($testFile);
    
    // Extract the request class name from the test file name
    $testFileName = basename($testFile, 'Test.php');
    $requestClassName = $testFileName;
    
    // Replace all instances of wrong class names with the correct one
    $content = str_replace('CreateCompanyRequest', $requestClassName, $content);
    $content = str_replace('UpdateCompanyRequest', $requestClassName, $content);
    $content = str_replace('StoreCandidateRequest', $requestClassName, $content);
    $content = str_replace('UpdateCandidateRequest', $requestClassName, $content);
    $content = str_replace('StoreCompanyRequest', $requestClassName, $content);
    $content = str_replace('CreateUserRequest', $requestClassName, $content);
    $content = str_replace('JobFilterRequest', $requestClassName, $content);
    $content = str_replace('StoreJobRequest', $requestClassName, $content);
    $content = str_replace('StoreTransactionRequest', $requestClassName, $content);
    $content = str_replace('UpdateTransactionRequest', $requestClassName, $content);
    $content = str_replace('StoreUserRequest', $requestClassName, $content);
    $content = str_replace('UserLoginRequest', $requestClassName, $content);
    $content = str_replace('StoreCategoriesRequest', $requestClassName, $content);
    $content = str_replace('UpdateCategoriesRequest', $requestClassName, $content);
    
    // Write back the fixed content
    file_put_contents($testFile, $content);
    
    echo "   ✅ Fixed: $testFile -> $requestClassName\n";
}

echo "\n🎉 All validation test files have been properly fixed!\n"; 