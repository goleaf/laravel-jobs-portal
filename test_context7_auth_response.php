<?php

/**
 * Quick test to debug Context7 authentication response format
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/api/auth/login', 'POST', [
    'email' => 'admin@example.com',  // Non-existent user to test error format
    'password' => 'password123',
    'device_name' => 'test-device'
]);

$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Headers:\n";
foreach ($response->headers->all() as $key => $values) {
    foreach ($values as $value) {
        echo "  $key: $value\n";
    }
}
echo "\nContent:\n";
echo $response->getContent() . "\n";

$kernel->terminate($request, $response); 