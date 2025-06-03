#!/bin/bash

# Comprehensive Laravel Job Portal Test Runner
# This script runs all Dusk tests with proper setup and cleanup

echo "🚀 Laravel Job Portal - Comprehensive Test Runner"
echo "================================================="

# Set memory limit
export MEMORY_LIMIT="4G"
php -d memory_limit=$MEMORY_LIMIT -v

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if ChromeDriver is running
check_chromedriver() {
    if netstat -tlnp | grep -q 9515; then
        print_success "ChromeDriver is running on port 9515"
        return 0
    else
        print_warning "ChromeDriver is not running"
        return 1
    fi
}

# Function to start ChromeDriver
start_chromedriver() {
    print_status "Starting ChromeDriver..."
    pkill -f chromedriver 2>/dev/null
    sleep 2
    
    nohup ./vendor/laravel/dusk/bin/chromedriver-linux --port=9515 > chromedriver.log 2>&1 &
    sleep 3
    
    if check_chromedriver; then
        print_success "ChromeDriver started successfully"
    else
        print_error "Failed to start ChromeDriver"
        exit 1
    fi
}

# Function to stop ChromeDriver
stop_chromedriver() {
    print_status "Stopping ChromeDriver..."
    pkill -f chromedriver 2>/dev/null
    print_success "ChromeDriver stopped"
}

# Function to run specific test suite
run_test_suite() {
    local test_file=$1
    local test_name=$2
    
    print_status "Running $test_name..."
    echo "----------------------------------------"
    
    php -d memory_limit=$MEMORY_LIMIT artisan dusk "$test_file" --stop-on-failure
    local exit_code=$?
    
    if [ $exit_code -eq 0 ]; then
        print_success "$test_name passed! ✅"
    else
        print_error "$test_name failed! ❌"
    fi
    
    echo ""
    return $exit_code
}

# Main execution
main() {
    print_status "Starting comprehensive test execution..."
    
    # Check if ChromeDriver is running, start if not
    if ! check_chromedriver; then
        start_chromedriver
    fi
    
    # Clear Laravel cache
    print_status "Clearing Laravel cache..."
    php artisan config:clear
    php artisan cache:clear
    
    # Array of test files and their descriptions
    declare -a tests=(
        "tests/Browser/PublicPagesTest.php:Public Pages Tests"
        "tests/Browser/BasicFunctionalityTest.php:Basic Functionality Tests"
        "tests/Browser/AuthenticatedRoutesTest.php:Authenticated Routes Tests"
        "tests/Browser/AdminRoutesTest.php:Admin Routes Tests"
        "tests/Browser/ApiRoutesTest.php:API Routes Tests"
        "tests/Browser/AuthTest.php:Authentication Tests"
        "tests/Browser/JobSearchTest.php:Job Search Tests"
        "tests/Browser/LinkTest.php:Link Tests"
    )
    
    # Run each test suite
    local total_tests=${#tests[@]}
    local passed_tests=0
    local failed_tests=0
    
    for test in "${tests[@]}"; do
        IFS=":" read -r test_file test_name <<< "$test"
        
        if [ -f "$test_file" ]; then
            if run_test_suite "$test_file" "$test_name"; then
                ((passed_tests++))
            else
                ((failed_tests++))
            fi
        else
            print_warning "Test file not found: $test_file"
        fi
    done
    
    # Print summary
    echo "========================================"
    echo "📊 TEST EXECUTION SUMMARY"
    echo "========================================"
    print_status "Total test suites: $total_tests"
    print_success "Passed: $passed_tests"
    if [ $failed_tests -gt 0 ]; then
        print_error "Failed: $failed_tests"
    else
        print_success "Failed: $failed_tests"
    fi
    
    if [ $failed_tests -eq 0 ]; then
        print_success "🎉 All tests passed!"
        echo ""
        echo "✅ Public pages are accessible"
        echo "✅ Authentication system works"
        echo "✅ Protected routes are secure"
        echo "✅ Admin access is controlled"
        echo "✅ API endpoints respond correctly"
        echo "✅ Job search functionality works"
        echo "✅ Navigation links are functional"
    else
        print_error "❌ Some tests failed. Check the output above for details."
        echo ""
        echo "🔧 Common fixes:"
        echo "   - Ensure ChromeDriver version matches Chrome version"
        echo "   - Check Laravel application is running properly"
        echo "   - Verify database migrations are up to date"
        echo "   - Check .env configuration"
    fi
    
    # Cleanup
    # stop_chromedriver
    
    if [ $failed_tests -eq 0 ]; then
        exit 0
    else
        exit 1
    fi
}

# Trap to ensure ChromeDriver is stopped on exit
trap 'stop_chromedriver' EXIT

# Run main function
main "$@" 