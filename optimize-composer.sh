#!/bin/bash

# Clear any cached files first
rm -rf bootstrap/cache/*.php

# Optimize memory usage in PHP
export COMPOSER_MEMORY_LIMIT=-1

# Increase process timeout
export COMPOSER_PROCESS_TIMEOUT=1800

# Run composer with optimize flags
php -d memory_limit=-1 $(which composer) update --optimize-autoloader --no-dev 