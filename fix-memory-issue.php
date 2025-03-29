<?php

// Set unlimited memory
ini_set('memory_limit', '-1');

// Basic package manifest structure
$packageManifest = [
    'providers' => [
        'Laravel\\Tinker\\TinkerServiceProvider',
        'Laravel\\Sanctum\\SanctumServiceProvider',
        'Laravel\\Sail\\SailServiceProvider',
        'Spatie\\LaravelIgnition\\IgnitionServiceProvider',
        // Add any other known providers here
    ],
    'aliases' => [
        // Common aliases
    ],
    'when' => [
        // Conditional loading
    ]
];

// Ensure bootstrap/cache directory exists
if (!is_dir(__DIR__ . '/bootstrap/cache')) {
    mkdir(__DIR__ . '/bootstrap/cache', 0755, true);
}

// Write to packages.php
file_put_contents(
    __DIR__ . '/bootstrap/cache/packages.php',
    '<?php return ' . var_export($packageManifest, true) . ';'
);

echo "Created package manifest file manually.\n";

// Write to services.php (empty array by default)
file_put_contents(
    __DIR__ . '/bootstrap/cache/services.php',
    '<?php return ' . var_export([], true) . ';'
);

echo "Created services manifest file manually.\n";

// Now run composer dump-autoload to ensure everything is properly loaded
echo "Running composer dump-autoload...\n";
passthru('composer dump-autoload --optimize', $return_code);

echo "Completed with status code: $return_code\n"; 