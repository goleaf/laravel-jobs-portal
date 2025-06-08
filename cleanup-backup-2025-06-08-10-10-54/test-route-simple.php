<?php

// Simple test to check route registration
echo "Testing route registration...\n";

// Read the routes file directly
$routesContent = file_get_contents('routes/web.php');

// Check if our routes are in the file
if (strpos($routesContent, 'CompanyController') !== false) {
    echo "✅ CompanyController routes found in web.php\n";
} else {
    echo "❌ CompanyController routes NOT found in web.php\n";
}

// Check specific route patterns
if (strpos($routesContent, '/company/{company}') !== false) {
    echo "✅ Company show route pattern found\n";
} else {
    echo "❌ Company show route pattern NOT found\n";
}

if (strpos($routesContent, 'company.show') !== false) {
    echo "✅ Company show route name found\n";
} else {
    echo "❌ Company show route name NOT found\n";
}

// Test if the URL pattern should match
echo "\nTesting URL pattern matching:\n";
$pattern = '/company/{company}';
$url = '/company/1';

// Simple pattern matching test
$regexPattern = str_replace('{company}', '([^/]+)', $pattern);
$regexPattern = '#^' . $regexPattern . '$#';

if (preg_match($regexPattern, $url)) {
    echo "✅ URL '/company/1' should match pattern '/company/{company}'\n";
} else {
    echo "❌ URL '/company/1' does NOT match pattern '/company/{company}'\n";
}

echo "\nRoute file content (last 20 lines):\n";
$lines = explode("\n", $routesContent);
$lastLines = array_slice($lines, -20);
foreach ($lastLines as $i => $line) {
    $lineNum = count($lines) - 20 + $i + 1;
    echo sprintf("%3d: %s\n", $lineNum, $line);
} 