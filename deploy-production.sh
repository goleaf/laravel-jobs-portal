#!/bin/bash

echo "🚀 Deploying Laravel Application with Optimizations"
echo "=================================================="

echo ""
echo "📦 Step 1: Installing Dependencies"
echo "----------------------------------"
composer install --no-dev --optimize-autoloader

echo ""
echo "🔧 Step 2: Caching Configurations"
echo "---------------------------------"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ""
echo "📦 Step 3: Building Assets"
echo "-------------------------"
npm ci --production
npm run build

echo ""
echo "🗄️ Step 4: Database Optimizations"
echo "----------------------------------"
php artisan migrate --force
php artisan db:seed --class=CacheWarmupSeeder

echo ""
echo "🔥 Step 5: Cache Warmup"
echo "----------------------"
php artisan cache:warmup

echo ""
echo "🧹 Step 6: Cleanup"
echo "-----------------"
php artisan optimize:clear
php artisan optimize

echo ""
echo "✅ Production Deployment Complete!"
echo "================================="
php artisan app:performance-report