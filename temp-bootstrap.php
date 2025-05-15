<?php

// Set unlimited memory
ini_set('memory_limit', '-1');

// Load autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Create empty manifest files to bypass discovery
if (!is_dir(__DIR__ . '/bootstrap/cache')) {
    mkdir(__DIR__ . '/bootstrap/cache', 0755, true);
}

file_put_contents(
    __DIR__ . '/bootstrap/cache/packages.php',
    '<?php return [];'
);

file_put_contents(
    __DIR__ . '/bootstrap/cache/services.php',
    '<?php return [];'
);

// Create a basic Laravel application
$app = new Illuminate\Foundation\Application(__DIR__);

// Register the Socialite provider directly
$app->register(Laravel\Socialite\SocialiteServiceProvider::class);

// Register the authentication service provider
$app->register(Illuminate\Auth\AuthServiceProvider::class);

// Bind the Socialite factory
$app->singleton('Laravel\Socialite\Contracts\Factory', function ($app) {
    return new Laravel\Socialite\SocialiteManager($app);
});

echo "Fixed Socialite dependencies.\n";

// Run composer dump-autoload
echo "Running composer dump-autoload...\n";
passthru('COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-scripts', $return_code);

echo "Running artisan to test if it works...\n";
passthru('php -d memory_limit=-1 artisan --version', $return_code);

echo "Done. Status code: $return_code\n"; 