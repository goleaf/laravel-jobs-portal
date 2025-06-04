#!/bin/bash

echo "🧪 Running Comprehensive Test Suite..."
echo "====================================="

# Run Unit Tests
echo "📊 Running Unit Tests..."
vendor/bin/phpunit --configuration phpunit-optimized.xml --testsuite=Unit --stop-on-failure

if [ $? -eq 0 ]; then
    echo "✅ Unit Tests Passed"
else
    echo "❌ Unit Tests Failed"
    exit 1
fi

# Run Feature Tests
echo "🎯 Running Feature Tests..."
vendor/bin/phpunit --configuration phpunit-optimized.xml --testsuite=Feature --stop-on-failure

if [ $? -eq 0 ]; then
    echo "✅ Feature Tests Passed"
else
    echo "❌ Feature Tests Failed"
    exit 1
fi

echo "🎉 All Tests Passed Successfully!"
echo "Test Coverage Report: coverage/index.html"
