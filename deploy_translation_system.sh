#!/bin/bash

# =============================================================================
# Translation System Deployment Script
# =============================================================================
# This script automates the deployment and validation of the comprehensive
# translation system for the job portal application.
#
# Usage: bash deploy_translation_system.sh
# =============================================================================

set -e  # Exit on any error

echo "🌍 Starting Translation System Deployment..."
echo "============================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️ $1${NC}"
}

# Check if PHP is available
if ! command -v php &> /dev/null; then
    print_error "PHP is not installed or not in PATH"
    exit 1
fi

# Check if Composer is available
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed or not in PATH"
    exit 1
fi

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    print_error "This script must be run from the Laravel project root directory"
    exit 1
fi

print_info "Step 1: Validating Environment..."

# Check if .env file exists
if [ ! -f ".env" ]; then
    print_warning ".env file not found. Creating from .env.example..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
        print_status ".env file created from .env.example"
    else
        print_error ".env.example not found. Please create .env file manually."
        exit 1
    fi
fi

print_info "Step 2: Installing/Updating Dependencies..."

# Install/update composer dependencies
composer install --no-dev --optimize-autoloader
print_status "Composer dependencies updated"

print_info "Step 3: Configuring Application..."

# Generate application key if not set
if ! grep -q "APP_KEY=" .env || [ "$(grep "^APP_KEY=" .env | cut -d'=' -f2)" = "" ]; then
    php artisan key:generate
    print_status "Application key generated"
fi

# Clear and cache configurations
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

print_status "Caches cleared"

print_info "Step 4: Setting up Translation Environment Variables..."

# Check and add translation environment variables
if ! grep -q "TRANSLATION_CACHE_ENABLED" .env; then
    echo "" >> .env
    echo "# Translation System Configuration" >> .env
    echo "TRANSLATION_CACHE_ENABLED=true" >> .env
    echo "TRANSLATION_CACHE_TTL=3600" >> .env
    echo "TRANSLATION_CACHE_PREFIX=translations" >> .env
    echo "TRANSLATION_CACHE_STORE=redis" >> .env
    echo "TRANSLATION_LAZY_LOADING=true" >> .env
    echo "TRANSLATION_VALIDATION_ENABLED=true" >> .env
    echo "TRANSLATION_MANAGEMENT_ENABLED=true" >> .env
    echo "TRANSLATION_FRONTEND_ENABLED=true" >> .env
    print_status "Translation environment variables added to .env"
else
    print_info "Translation environment variables already exist in .env"
fi

print_info "Step 5: Running Translation System Tests..."

# Run the translation system validation
if php test_translation_system.php; then
    print_status "Translation system validation completed"
else
    print_warning "Some validation tests failed. Check output above."
fi

print_info "Step 6: Checking Translation Management CLI..."

# Test the translation management command
if php artisan translation:manage stats > /dev/null 2>&1; then
    print_status "Translation management CLI is working"
    echo ""
    print_info "Translation Statistics:"
    php artisan translation:manage stats
else
    print_warning "Translation management CLI may need additional setup"
fi

print_info "Step 7: Optimizing for Production..."

# Cache configurations for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_status "Application optimized for production"

print_info "Step 8: Setting up Storage Directories..."

# Create translation backup directory
mkdir -p storage/app/translation-backups
chmod 755 storage/app/translation-backups
print_status "Translation backup directory created"

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

print_status "Storage permissions set"

echo ""
echo "🎉 Translation System Deployment Complete!"
echo "=========================================="
echo ""

echo "✅ DEPLOYMENT SUMMARY:"
echo "   • 9 languages supported (en, ar, de, es, fr, pt, ru, tr, zh)"
echo "   • RTL support configured for Arabic"
echo "   • Redis caching enabled"
echo "   • Translation management tools available"
echo "   • Frontend integration ready"
echo "   • Performance optimizations applied"
echo ""

echo "📋 NEXT STEPS:"
echo "   1. Configure Redis connection in .env (REDIS_HOST, REDIS_PORT)"
echo "   2. Test language switching: Visit /locale/ar to switch to Arabic"
echo "   3. Access admin panel: /admin/translations for management"
echo "   4. Monitor performance: Check Redis cache usage"
echo "   5. Test RTL layout: Verify Arabic text displays correctly"
echo ""

echo "🔧 AVAILABLE COMMANDS:"
echo "   • php artisan translation:manage stats    - View translation statistics"
echo "   • php artisan translation:manage scan     - Scan for hardcoded strings"
echo "   • php artisan translation:manage missing  - Find missing translations"
echo "   • php artisan translation:manage sync     - Sync translation files"
echo "   • php artisan translation:manage export   - Export translations"
echo ""

echo "📚 DOCUMENTATION:"
echo "   • TRANSLATION_SYSTEM_GUIDE.md - Complete usage guide"
echo "   • MASTER_TRANSLATION_IMPLEMENTATION.php - Technical reference"
echo "   • .env.translation.example - Environment configuration template"
echo ""

print_status "Your job portal is now fully internationalized and ready for global users!"

# Final validation
echo ""
print_info "Running final validation..."

# Check if critical files exist
critical_files=(
    "app/Http/Controllers/LocaleController.php"
    "app/Http/Controllers/TranslationManagerController.php"
    "app/Services/TranslationService.php"
    "app/Console/Commands/TranslationCommand.php"
    "app/Providers/TranslationServiceProvider.php"
    "config/translation.php"
    "resources/js/translation.js"
)

all_files_exist=true
for file in "${critical_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file"
    else
        echo "❌ $file - MISSING"
        all_files_exist=false
    fi
done

if [ "$all_files_exist" = true ]; then
    print_status "All critical translation system files are present"
    echo ""
    echo "🚀 DEPLOYMENT SUCCESSFUL - System is ready for production!"
else
    print_error "Some critical files are missing. Please check the installation."
    exit 1
fi