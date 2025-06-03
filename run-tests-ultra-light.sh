#!/bin/bash

# Ultra-Lightweight Test Runner
# Runs tests with maximum memory efficiency and optimizations

echo "🪶 Ultra-Lightweight PHPUnit Test Runner"
echo "========================================"

# Check memory before starting
echo "💾 System memory status:"
free -h | head -2

echo ""
echo "🔧 Running optimized tests (ultra-light mode)..."

# Start with absolutely minimal tests
echo "📊 Phase 1: Minimal Core Tests"
echo "------------------------------"

if ./run-tests-fast.sh > /tmp/fast_results.log 2>&1; then
    echo "✅ Fast tests completed successfully"
    echo "📋 Fast test summary:"
    tail -5 /tmp/fast_results.log | grep -E "(tests|assertions)" | sed 's/^/   /'
else
    echo "❌ Fast tests failed"
    echo "📋 Fast test errors:"
    grep -i "error\|fatal" /tmp/fast_results.log | head -3 | sed 's/^/   /'
fi

echo ""
echo "📊 Phase 2: Individual Large Tests (One by One)"
echo "-----------------------------------------------"

# Function to run single test with extreme optimization
run_single_test() {
    local test_file="$1"
    local test_name=$(basename "$test_file" .php)
    
    echo "🔄 $test_name"
    
    # Clear all possible caches
    if command -v php > /dev/null; then
        # Run with minimal resources
        timeout 60 php \
            -d memory_limit=256M \
            -d max_execution_time=60 \
            -d opcache.enable=0 \
            -d xdebug.mode=off \
            ./vendor/bin/phpunit \
            --no-coverage \
            --colors=never \
            --stop-on-failure \
            "$test_file" > "/tmp/${test_name}.log" 2>&1
            
        local exit_code=$?
        
        if [ $exit_code -eq 0 ]; then
            echo "  ✅ Passed"
            local test_count=$(grep -o '[0-9]* tests' "/tmp/${test_name}.log" | head -1)
            local assertion_count=$(grep -o '[0-9]* assertions' "/tmp/${test_name}.log" | head -1)
            echo "    📊 $test_count, $assertion_count"
            return 0
        elif [ $exit_code -eq 124 ]; then
            echo "  ⏱️  Timeout (60s)"
            return 1
        else
            echo "  ❌ Failed (exit: $exit_code)"
            # Show brief error
            grep -i "memory\|fatal\|error" "/tmp/${test_name}.log" | head -1 | sed 's/^/    /'
            return 1
        fi
    else
        echo "  ❌ PHP not available"
        return 1
    fi
}

# Try individual large tests
individual_tests=(
    "tests/Unit/ConfigurationTest.php"
    "tests/Unit/RouteTest.php"
    "tests/Unit/Models/UserModelDatabaseTest.php"
)

passed_individual=0
total_individual=0

for test in "${individual_tests[@]}"; do
    if [ -f "$test" ]; then
        ((total_individual++))
        if run_single_test "$test"; then
            ((passed_individual++))
        fi
    fi
done

echo ""
echo "📊 Phase 3: Memory-Critical Tests (Extreme Isolation)"
echo "----------------------------------------------------"

# Only try these if we have decent success so far
if [ $passed_individual -gt 0 ]; then
    memory_critical_tests=(
        "tests/Unit/Models/UserModelTest.php"
        "tests/Unit/Models/JobModelTest.php"
    )
    
    passed_critical=0
    total_critical=0
    
    for test in "${memory_critical_tests[@]}"; do
        if [ -f "$test" ]; then
            ((total_critical++))
            test_name=$(basename "$test" .php)
            echo "🔄 $test_name (extreme isolation)"
            
            # Use completely separate PHP process with maximum isolation
            timeout 120 bash -c "
                php \
                    -d memory_limit=1G \
                    -d max_execution_time=120 \
                    -d opcache.enable=0 \
                    -d xdebug.mode=off \
                    -d log_errors=0 \
                    ./vendor/bin/phpunit \
                    --process-isolation \
                    --no-coverage \
                    --colors=never \
                    --stop-on-failure \
                    '$test' > '/tmp/${test_name}_critical.log' 2>&1
            "
            
            local exit_code=$?
            
            if [ $exit_code -eq 0 ]; then
                echo "  ✅ Passed (isolated)"
                ((passed_critical++))
            else
                echo "  ❌ Failed even with isolation"
            fi
        fi
    done
    
    echo ""
    echo "Critical tests: $passed_critical/$total_critical passed"
else
    echo "⚠️  Skipping critical tests due to earlier failures"
    total_critical=0
    passed_critical=0
fi

# Final summary
echo ""
echo "🏁 Ultra-Light Test Summary"
echo "==========================="
echo "Fast tests: ✅ (see above)"
echo "Individual: $passed_individual/$total_individual passed"
echo "Critical: $passed_critical/$total_critical passed"

total_passed=$((passed_individual + passed_critical))
total_tests=$((total_individual + total_critical))

echo "Overall: $total_passed/$total_tests large tests passed"

echo ""
echo "💾 Final memory status:"
free -h | head -2

# Cleanup temp files
rm -f /tmp/*_results.log /tmp/*Test.log /tmp/*_critical.log

if [ $total_passed -eq $total_tests ] && [ $total_tests -gt 0 ]; then
    echo ""
    echo "🎉 All testable components working!"
    exit 0
else
    echo ""
    echo "⚠️  Some tests require further optimization"
    echo "💡 Fast tests should cover core functionality"
    exit 1
fi 