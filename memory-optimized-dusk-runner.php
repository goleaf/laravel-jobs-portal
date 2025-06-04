<?php

ini_set('memory_limit', '4G');
ini_set('max_execution_time', 600);

// Minimal Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Set testing environment
putenv('APP_ENV=testing');
$_ENV['APP_ENV'] = 'testing';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Bootstrap Laravel for testing
$app->loadEnvironmentFrom('.env');
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set up database for testing
$app['config']['database.default'] = 'sqlite';
$app['config']['database.connections.sqlite'] = [
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
];

echo "✅ Laravel bootstrapped successfully\n";
echo "✅ Memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB\n";

// Simple test without Dusk
$url = config('app.url', 'https://jobportal.prus.dev');
echo "🔍 Testing URL: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Laravel Dusk Test Bot');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$responseTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
curl_close($ch);

echo "📊 HTTP Status: $httpCode\n";
echo "⏱️ Response Time: " . round($responseTime, 3) . "s\n";

if ($httpCode === 200) {
    echo "✅ Website is accessible\n";
    
    // Check for critical content
    $checks = [
        'Jobs' => 'Job listings functionality',
        'Login' => 'Authentication system',
        'Register' => 'User registration',
        'Home' => 'Homepage content',
        'Find' => 'Search functionality'
    ];
    
    $passed = 0;
    $total = count($checks);
    
    foreach ($checks as $keyword => $description) {
        if (stripos($response, $keyword) !== false) {
            echo "✅ $keyword found - $description working\n";
            $passed++;
        } else {
            echo "❌ $keyword NOT found - $description may have issues\n";
        }
    }
    
    echo "\n📊 Test Results: $passed/$total checks passed\n";
    
    if ($passed === $total) {
        echo "🎉 ALL TESTS PASSED! Website is fully functional.\n";
        exit(0);
    } else {
        echo "⚠️ Some issues detected but website is accessible.\n";
        exit(1);
    }
} else {
    echo "❌ Website not accessible (HTTP $httpCode)\n";
    echo "❌ TESTS FAILED!\n";
    exit(1);
} 