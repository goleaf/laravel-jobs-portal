<?php

/**
 * HIGH MEMORY LARAVEL ARTISAN WRAPPER
 * Prevents memory exhaustion issues permanently
 */

// Set maximum memory limits
ini_set('memory_limit', '8G');
ini_set('max_execution_time', 600);
ini_set('max_input_time', 600);

// Optimize for performance
ini_set('opcache.enable', 1);
ini_set('opcache.memory_consumption', 512);

// Get the artisan command from arguments
$args = array_slice($argv, 1);
$command = implode(' ', array_map('escapeshellarg', $args));

echo "🚀 Running high-memory Laravel command: php artisan $command\n";
echo "💾 Memory limit: 8GB\n";
echo "⏱️ Time limit: 600 seconds\n";

// Execute the command with high memory
$cmd = "php -c " . __DIR__ . "/php.ini artisan $command";
$output = [];
$returnCode = 0;

exec($cmd, $output, $returnCode);

// Display output
foreach ($output as $line) {
    echo $line . "\n";
}

echo "\n";
if ($returnCode === 0) {
    echo "✅ Command completed successfully!\n";
} else {
    echo "❌ Command failed with return code: $returnCode\n";
}

exit($returnCode); 