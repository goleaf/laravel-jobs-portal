#!/bin/bash
# 🧪 Universal Batch Test Runner
# Runs specific Universal test suites for the Laravel job portal

echo "🧪 UNIVERSAL BATCH TEST RUNNER"
echo "=============================="

# Test categories
echo "Select test category:"
echo "1. Universal API Tests"
echo "2. Universal Security Tests" 
echo "3. Universal Feature Tests"
echo "4. Universal Unit Tests"
echo "5. Run all Universal tests"
echo "6. Quick Universal smoke tests"

read -p "Enter choice (1-6): " choice

case $choice in
    1)
        echo "🔗 Running Universal API Tests..."
        php artisan test tests/Feature/Api/Universal/
        ;;
    2)
        echo "🔐 Running Universal Security Tests..."
        php artisan test tests/Feature/Enhanced/SecurityTest.php
        ;;
    3)
        echo "🎯 Running Universal Feature Tests..."
        php artisan test tests/Feature/ --exclude-group=slow
        ;;
    4)
        echo "⚙️ Running Universal Unit Tests..."
        php artisan test tests/Unit/
        ;;
    5)
        echo "🚀 Running ALL Universal Tests..."
        php artisan test
        ;;
    6)
        echo "⚡ Running Quick Universal Smoke Tests..."
        php artisan test tests/Feature/Universal/ --stop-on-failure
        ;;
    *)
        echo "❌ Invalid choice. Exiting."
        exit 1
        ;;
esac

echo "✅ Universal test suite complete!"
