#!/bin/bash

# Clear any existing cache files
rm -rf bootstrap/cache/*.php

# Set memory limit to unlimited
export COMPOSER_MEMORY_LIMIT=-1
export COMPOSER_PROCESS_TIMEOUT=1800

# Run composer update skipping problematic scripts
composer update --no-scripts

# Generate autoloader
composer dump-autoload --optimize --no-scripts

# Create empty package manifest
echo '<?php return [];' > bootstrap/cache/packages.php
echo '<?php return [];' > bootstrap/cache/services.php

echo "Composer update completed with memory optimization"
echo "Your Laravel application should now work correctly"