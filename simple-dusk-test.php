<?php

require_once __DIR__ . '/vendor/autoload.php';

// Set memory limit
ini_set('memory_limit', '4G');

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Set environment for testing
$app['env'] = 'testing';

// Run a simple test
echo "Testing basic website functionality...\n";

// Test if the website is accessible
$url = env('APP_URL', 'https://jobportal.prus.dev');
echo "Testing URL: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Website is accessible (HTTP 200)\n";
    
    // Check for basic content
    if (strpos($response, 'Jobs') !== false) {
        echo "✅ 'Jobs' content found\n";
    } else {
        echo "❌ 'Jobs' content NOT found\n";
    }
    
    if (strpos($response, 'Login') !== false) {
        echo "✅ 'Login' content found\n";
    } else {
        echo "❌ 'Login' content NOT found\n";
    }
    
    echo "✅ Basic functionality test PASSED\n";
} else {
    echo "❌ Website not accessible (HTTP $httpCode)\n";
    echo "❌ Basic functionality test FAILED\n";
}

echo "\nTest completed.\n"; 