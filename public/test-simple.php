<?php
// Very simple Laravel test
ini_set('memory_limit', '1G');
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Memory at start: " . memory_get_usage() . "\n";

try {
    require_once '../vendor/autoload.php';
    echo "Autoloader loaded. Memory: " . memory_get_usage() . "\n";
    
    $app = require_once '../bootstrap/app.php';
    echo "Laravel app created. Memory: " . memory_get_usage() . "\n";
    
    // Try to get config without making HTTP kernel
    $config = $app['config'];
    echo "Config loaded. App name: " . $config->get('app.name') . "\n";
    echo "Memory: " . memory_get_usage() . "\n";
    
    echo "SUCCESS: Laravel loads without HTTP kernel!";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
} catch (Error $e) {
    echo "FATAL: " . $e->getMessage();
}
?> 