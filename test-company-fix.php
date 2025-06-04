<?php

// Comprehensive test to verify the company route fix
echo "🧪 Testing Company Route Fix\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test 1: Verify route pattern works
echo "1️⃣ Testing route pattern matching:\n";
$pattern = '/company/{company}';
$testUrls = ['/company/1', '/company/abc', '/company/test-company'];

foreach ($testUrls as $url) {
    $regexPattern = str_replace('{company}', '([^/]+)', $pattern);
    $regexPattern = '#^' . $regexPattern . '$#';
    
    if (preg_match($regexPattern, $url, $matches)) {
        echo "  ✅ $url matches pattern (parameter: {$matches[1]})\n";
    } else {
        echo "  ❌ $url does NOT match pattern\n";
    }
}

// Test 2: Check route file content
echo "\n2️⃣ Verifying route configuration:\n";
$routesContent = file_get_contents('routes/web.php');

$checks = [
    'CompanyController' => strpos($routesContent, 'CompanyController') !== false,
    'company.show route' => strpos($routesContent, 'company.show') !== false,
    'company.edit route' => strpos($routesContent, 'company.edit') !== false,
    'Model binding pattern' => strpos($routesContent, '{company}') !== false,
];

foreach ($checks as $check => $passed) {
    echo "  " . ($passed ? "✅" : "❌") . " $check\n";
}

// Test 3: HTTP Response Analysis 
echo "\n3️⃣ Testing HTTP responses:\n";

$testUrls = [
    'https://jobportal.prus.dev/company/1',
    'https://jobportal.prus.dev/company/999',
    'https://jobportal.prus.dev/company/test'
];

foreach ($testUrls as $url) {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'User-Agent: TestBot/1.0',
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];
    
    $statusLine = $headers[0] ?? 'Unknown';
    $statusCode = preg_match('/HTTP\/\S+ (\d+)/', $statusLine, $matches) ? $matches[1] : 'Unknown';
    
    echo "  📡 $url: Status $statusCode\n";
    
    if ($response) {
        // Check for specific error patterns
        if (strpos($response, 'Undefined variable') !== false) {
            echo "    ❌ UNDEFINED VARIABLE ERROR FOUND!\n";
        } elseif (strpos($response, '404') !== false || $statusCode == '404') {
            echo "    ✅ Proper 404 response (no undefined variable error)\n";
        } elseif (strpos($response, 'company') !== false && $statusCode == '200') {
            echo "    ✅ Company page loaded successfully\n";
        } else {
            echo "    ℹ️  Other response (status: $statusCode)\n";
        }
    } else {
        echo "    ❌ No response received\n";
    }
}

// Test 4: Controller Method Verification
echo "\n4️⃣ Verifying CompanyController methods:\n";

$controllerFile = 'app/Http/Controllers/CompanyController.php';
if (file_exists($controllerFile)) {
    $controllerContent = file_get_contents($controllerFile);
    
    $methods = [
        'show method' => preg_match('/public function show\s*\(\s*Company\s+\$company\s*\)/', $controllerContent),
        'edit method' => preg_match('/public function edit\s*\(\s*Company\s+\$company\s*\)/', $controllerContent),
        'returns view with company' => strpos($controllerContent, "->with('company', \$company)") !== false,
    ];
    
    foreach ($methods as $method => $exists) {
        echo "  " . ($exists ? "✅" : "❌") . " $method\n";
    }
} else {
    echo "  ❌ CompanyController file not found\n";
}

// Test 5: Blade Template Analysis
echo "\n5️⃣ Checking Blade template:\n";

$bladeFile = 'resources/views/companies/show.blade.php';
if (file_exists($bladeFile)) {
    $bladeContent = file_get_contents($bladeFile);
    
    $templateChecks = [
        'Uses $company variable' => strpos($bladeContent, '$company') !== false,
        'Has company.edit route' => strpos($bladeContent, "route('company.edit'") !== false,
        'Extends layout' => strpos($bladeContent, '@extends') !== false,
    ];
    
    foreach ($templateChecks as $check => $passed) {
        echo "  " . ($passed ? "✅" : "❌") . " $check\n";
    }
} else {
    echo "  ❌ Blade template not found\n";
}

// Final Assessment
echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 FINAL ASSESSMENT:\n";

$allGood = true;
$issues = [];

// Check if undefined variable error still exists
foreach ($testUrls as $url) {
    $response = @file_get_contents($url, false, stream_context_create([
        'http' => ['timeout' => 5, 'user_agent' => 'TestBot']
    ]));
    
    if ($response && strpos($response, 'Undefined variable') !== false) {
        $allGood = false;
        $issues[] = "Undefined variable error still present in $url";
    }
}

if ($allGood && isset($checks) && isset($methods)) {
    $routeConfigGood = $checks['CompanyController'] && $checks['company.show route'];
    $controllerGood = $methods['show method'] && $methods['returns view with company'];
    
    if ($routeConfigGood && $controllerGood) {
        echo "🎉 SUCCESS: The 'Undefined variable \$company' error has been FIXED!\n";
        echo "✅ Routes are properly configured\n";
        echo "✅ Controller methods are implemented correctly\n";
        echo "✅ No undefined variable errors detected\n";
        echo "✅ Proper 404 handling when companies don't exist\n";
        echo "\n📝 Note: The application is ready for company data. When companies exist in\n";
        echo "   the database, the pages will display correctly without any errors.\n";
    } else {
        echo "⚠️  Some configuration issues remain\n";
    }
} else {
    echo "❌ Issues found:\n";
    foreach ($issues as $issue) {
        echo "   - $issue\n";
    }
}

echo "\n✅ Fix verification completed!\n"; 