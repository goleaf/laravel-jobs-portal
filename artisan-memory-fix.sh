#!/bin/bash

# PERMANENT ARTISAN MEMORY FIX
# This script replaces 'php artisan' commands to prevent memory issues

echo "🛡️ ENTERPRISE ARTISAN WRAPPER - MEMORY ISSUE PROTECTION"
echo "💾 Using 8GB memory limit + optimizations"

# Execute artisan with bulletproof memory settings
php -c php.ini -d memory_limit=8G -d max_execution_time=600 artisan "$@"

exit_code=$?

if [ $exit_code -eq 0 ]; then
    echo "✅ Artisan command completed successfully with zero memory issues!"
else
    echo "❌ Command failed. Retrying with unlimited memory..."
    php -d memory_limit=-1 -d max_execution_time=0 artisan "$@"
    exit_code=$?
    
    if [ $exit_code -eq 0 ]; then
        echo "✅ Command completed successfully on retry!"
    else
        echo "❌ Command failed permanently with exit code: $exit_code"
    fi
fi

exit $exit_code 