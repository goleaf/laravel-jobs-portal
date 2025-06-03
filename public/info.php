<?php
// Simple PHP info page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>PHP Configuration Test</h1>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Memory Limit:</strong> " . ini_get('memory_limit') . "</p>";
echo "<p><strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "</p>";

echo "<h2>File System Check</h2>";
echo "<p>Current directory: " . getcwd() . "</p>";
echo "<p>Parent directory exists: " . (is_dir('../') ? 'Yes' : 'No') . "</p>";
echo "<p>Vendor directory exists: " . (is_dir('../vendor') ? 'Yes' : 'No') . "</p>";
echo "<p>Bootstrap directory exists: " . (is_dir('../bootstrap') ? 'Yes' : 'No') . "</p>";
echo "<p>Laravel index.php exists: " . (file_exists('index.php') ? 'Yes' : 'No') . "</p>";

echo "<h2>Environment Variables</h2>";
echo "<p>APP_ENV: " . (getenv('APP_ENV') ?: 'Not set') . "</p>";
echo "<p>DEBUGBAR_ENABLED: " . (getenv('DEBUGBAR_ENABLED') ?: 'Not set') . "</p>";

phpinfo();
?> 