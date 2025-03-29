#!/bin/bash

# Fix Laravel memory issues by reinstalling critical packages

echo "Fixing Laravel memory issues..."

# Clear caches first
rm -rf bootstrap/cache/*.php
rm -rf vendor/composer/installed.php

# First pass with no scripts
echo "Step 1: Updating core dependencies without running scripts..."
COMPOSER_MEMORY_LIMIT=-1 composer update --no-scripts --no-autoloader

# Install only essential packages
echo "Step 2: Installing only essential packages..."
COMPOSER_MEMORY_LIMIT=-1 composer require --no-scripts --update-with-dependencies "laravel/framework:^10.0" "laravel/sanctum" "guzzlehttp/guzzle"

# Generate autoloader without discovery
echo "Step 3: Generating optimized autoloader without discovery scripts..."
COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-scripts

# Create cache directory if it doesn't exist
mkdir -p bootstrap/cache

# Create empty package manifest to bypass discovery
echo "<?php return [];" > bootstrap/cache/packages.php
echo "<?php return [];" > bootstrap/cache/services.php

echo "Step 4: Caching configuration..."
php -d memory_limit=-1 artisan config:cache

echo "Step 5: Caching routes..."
php -d memory_limit=-1 artisan route:cache

echo "Fix completed. Your Laravel application should now work with better memory usage.
You can continue using the application normally." 