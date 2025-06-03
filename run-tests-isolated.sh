#!/bin/bash

# Isolated PHPUnit Test Runner
# Runs problematic tests in separate processes with strict timeouts

echo "🔬 Running Isolated PHPUnit Tests (Process Isolation)"
echo "====================================================="

# Function to run isolated test with timeout
run_isolated_test() {
    local test_file="$1"
    local test_name=$(basename "$test_file" .php)
    local timeout_seconds=60
    
    echo "🔄 Testing $test_name (timeout: ${timeout_seconds}s)"
    
    # Run test with timeout and process isolation
    timeout $timeout_seconds php -d memory_limit=1G ./vendor/bin/phpunit \
        --process-isolation \
        --stop-on-failure \
        --no-coverage \
        "$test_file" > /tmp/test_output.log 2>&1
    
    local exit_code=$?
    
    if [ $exit_code -eq 0 ]; then
        echo "  ✅ $test_name passed"
        return 0
    elif [ $exit_code -eq 124 ]; then
        echo "  ⏱️  $test_name timed out (${timeout_seconds}s)"
        return 1
    else
        echo "  ❌ $test_name failed (exit code: $exit_code)"
        # Show last few lines of error for debugging
        echo "  📋 Last error lines:"
        tail -3 /tmp/test_output.log | sed 's/^/     /'
        return 1
    fi
}

# Test counters
passed=0
failed=0

# Run problematic tests individually with isolation
problematic_tests=(
    "tests/Unit/ConfigurationTest.php"
    "tests/Unit/RouteTest.php" 
    "tests/Unit/Models/UserModelDatabaseTest.php"
)

for test in "${problematic_tests[@]}"; do
    if [ -f "$test" ]; then
        if run_isolated_test "$test"; then
            ((passed++))
        else
            ((failed++))
        fi
    else
        echo "  ⚠️  Test file not found: $test"
        ((failed++))
    fi
    echo ""
done

# Try to run large model tests with extreme isolation
echo "🔄 Testing Large Model Tests (Individual Process Each)"
echo "---"

large_tests=(
    "tests/Unit/Models/UserModelTest.php"
    "tests/Unit/Models/JobModelTest.php"
)

for test in "${large_tests[@]}"; do
    if [ -f "$test" ]; then
        test_name=$(basename "$test" .php)
        echo "🔄 Attempting $test_name with maximum isolation..."
        
        # Even more aggressive isolation
        timeout 120 php -d memory_limit=2G -d max_execution_time=120 ./vendor/bin/phpunit \
            --process-isolation \
            --stop-on-failure \
            --no-coverage \
            --colors=never \
            "$test" > /tmp/large_test_output.log 2>&1
            
        local exit_code=$?
        
        if [ $exit_code -eq 0 ]; then
            echo "  ✅ $test_name passed with isolation"
            ((passed++))
        else
            echo "  ❌ $test_name failed even with maximum isolation"
            echo "  📋 Error summary:"
            grep -i "error\|fatal\|memory" /tmp/large_test_output.log | head -2 | sed 's/^/     /'
            ((failed++))
        fi
    else
        echo "  ⚠️  Test file not found: $test"
        ((failed++))
    fi
    echo ""
done

# Cleanup
rm -f /tmp/test_output.log /tmp/large_test_output.log

# Summary
echo "📊 Isolated Test Summary"
echo "========================"
echo "Passed: $passed test suites"
echo "Failed: $failed test suites"
echo "Total:  $((passed + failed)) test suites"

if [ $failed -eq 0 ]; then
    echo ""
    echo "🎉 All isolated tests passed!"
    exit 0
else
    echo ""
    echo "⚠️  Some tests still have issues"
    echo "💡 Consider further memory optimization or skipping problematic tests"
    exit 1
fi 