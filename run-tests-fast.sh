#!/bin/bash

# Fast PHPUnit Test Runner
# Runs only lightweight tests without database overhead

echo "🚀 Running Fast PHPUnit Tests (Memory Optimized)"
echo "================================================="

# Clear any previous cache
if [ -d ".phpunit.cache" ]; then
    rm -rf .phpunit.cache
fi

# Run fast tests with memory optimization
php -d memory_limit=512M ./vendor/bin/phpunit --configuration phpunit-fast.xml --testdox

echo ""
echo "✅ Fast tests completed!"
echo ""

# Show memory usage summary
echo "📊 Memory Usage Summary:"
echo "- Configuration: 512MB limit"
echo "- Tests run: Lightweight model tests only"
echo "- Database: No database operations"
echo "- Time: < 30 seconds expected" 