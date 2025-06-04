#!/bin/bash

echo "🧪 Running Comprehensive Test Suite"
echo "===================================="

echo ""
echo "📋 Phase 1: Unit Tests"
echo "----------------------"
./vendor/bin/phpunit tests/Unit --testdox

echo ""
echo "🌐 Phase 2: Feature Tests"
echo "-------------------------"
./vendor/bin/phpunit tests/Feature --testdox

echo ""
echo "🎨 Phase 3: Blade Template Validation"
echo "-------------------------------------"
php validate_blade_templates.php

echo ""
echo "🛣️ Phase 4: Route Analysis"
echo "---------------------------"
php artisan route:list --compact

echo ""
echo "✅ Test Suite Complete!"