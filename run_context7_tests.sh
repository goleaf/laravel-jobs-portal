#!/bin/bash

# 🧪 Context7 Batch Test Runner
# Runs all tests with proper error handling and reporting

echo "🧪 CONTEXT7 BATCH TEST RUNNER"
echo "=" && printf '=%.0s' {1..40} && echo ""
echo ""

echo "🔄 Running all feature tests..."
php artisan test tests/Feature/ --verbose --stop-on-failure

echo ""
echo "🔄 Running all unit tests..."
php artisan test tests/Unit/ --verbose --stop-on-failure

echo ""
echo "🔄 Running API tests..."
php artisan test tests/Feature/Api/ --verbose --stop-on-failure

echo ""
echo "🔄 Generating test coverage report..."
php artisan test --coverage --min=80

echo ""
echo "📊 Test Summary:"
echo "Feature Tests: " $(find tests/Feature -name "*Test.php" | wc -l)
echo "Unit Tests: " $(find tests/Unit -name "*Test.php" | wc -l)
echo "API Tests: " $(find tests/Feature/Api -name "*Test.php" | wc -l)
echo "Total Tests: " $(find tests -name "*Test.php" | wc -l)

echo ""
echo "✅ Context7 test suite complete!"
