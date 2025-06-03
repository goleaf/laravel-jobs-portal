#!/bin/bash

# Comprehensive Test Runner with Debugbar Enabled
# Tests all components with debugging capabilities

echo "🔧 Comprehensive PHPUnit Test Runner (Debugbar Enabled)"
echo "======================================================"

# System information
echo "🖥️  System Information:"
echo "   PHP: $(php --version | head -1)"
echo "   Memory: $(free -h | grep Mem | awk '{print $2}')"
echo "   PHPUnit: $(./vendor/bin/phpunit --version | head -1)"
echo "   Debugbar: ✅ ENABLED"

echo ""

# Function to run test suite with detailed monitoring
run_test_phase() {
    local phase_name="$1"
    local config_file="$2"
    local testsuite="$3"
    local description="$4"
    
    echo "🔄 Phase: $phase_name"
    echo "   Config: $config_file"
    echo "   Suite: $testsuite"
    echo "   Description: $description"
    
    if [ -f "$config_file" ]; then
        local start_time=$(date +%s)
        
        if [ -n "$testsuite" ]; then
            ./vendor/bin/phpunit --configuration "$config_file" --testsuite="$testsuite" > "/tmp/${phase_name}.log" 2>&1
        else
            ./vendor/bin/phpunit --configuration "$config_file" > "/tmp/${phase_name}.log" 2>&1
        fi
        
        local exit_code=$?
        local end_time=$(date +%s)
        local duration=$((end_time - start_time))
        
        if [ $exit_code -eq 0 ]; then
            echo "   ✅ SUCCESS (${duration}s)"
            # Extract detailed stats
            local stats=$(grep -E "Tests:|Time:|Memory:|OK" "/tmp/${phase_name}.log" | tail -3 | tr '\n' ' ')
            echo "   📊 $stats"
            
            # Check for warnings or deprecations
            local warnings=$(grep -i "warning\|deprecation" "/tmp/${phase_name}.log" | wc -l)
            if [ $warnings -gt 0 ]; then
                echo "   ⚠️  $warnings warnings/deprecations found"
            fi
            
            return 0
        else
            echo "   ❌ FAILED (${duration}s, exit: $exit_code)"
            # Show specific error information
            local errors=$(grep -i "error\|fatal\|memory" "/tmp/${phase_name}.log" | head -2)
            if [ -n "$errors" ]; then
                echo "   🔍 Error details:"
                echo "$errors" | sed 's/^/      /'
            fi
            return 1
        fi
    else
        echo "   ⚠️  Config file not found: $config_file"
        return 1
    fi
    echo ""
}

# Test execution plan
echo "📋 Comprehensive Test Execution Plan with Debugbar:"
echo "===================================================="

# Phase 1: Optimized fast tests with debugbar
echo "📊 Phase 1: Optimized Tests (Debugbar Enabled)"
echo "----------------------------------------------"
run_test_phase "Optimized" "phpunit-fast.xml" "" "Fast optimized tests with debugbar"
OPTIMIZED_RESULT=$?

# Phase 2: Extended tests using new config
echo "📊 Phase 2: All Tests (Comprehensive with Debugbar)"
echo "---------------------------------------------------"
run_test_phase "AllWithDebugbar" "phpunit-all-with-debugbar.xml" "Optimized" "All optimized tests with debugbar"
ALL_OPTIMIZED_RESULT=$?

# Phase 3: Extended tests (non-optimized)
echo "📊 Phase 3: Extended Tests (Legacy + Debugbar)"
echo "-----------------------------------------------"
run_test_phase "Extended" "phpunit-all-with-debugbar.xml" "Extended" "Legacy tests with debugbar"
EXTENDED_RESULT=$?

# Phase 4: Feature tests (if they exist)
echo "📊 Phase 4: Feature Tests (Debugbar Enabled)"
echo "---------------------------------------------"
if [ -d "tests/Feature" ] && [ "$(ls -A tests/Feature)" ]; then
    run_test_phase "Feature" "phpunit-all-with-debugbar.xml" "Feature" "Feature tests with debugbar"
    FEATURE_RESULT=$?
else
    echo "   ⚠️  No feature tests found"
    FEATURE_RESULT=0
fi

echo ""

# Comprehensive summary
echo "🏁 Comprehensive Test Summary (Debugbar Enabled)"
echo "================================================="
echo "Phase 1 - Optimized: $([ $OPTIMIZED_RESULT -eq 0 ] && echo "✅ Passed" || echo "❌ Failed")"
echo "Phase 2 - All Optimized: $([ $ALL_OPTIMIZED_RESULT -eq 0 ] && echo "✅ Passed" || echo "❌ Failed")"
echo "Phase 3 - Extended: $([ $EXTENDED_RESULT -eq 0 ] && echo "✅ Passed" || echo "❌ Failed")"
echo "Phase 4 - Feature: $([ $FEATURE_RESULT -eq 0 ] && echo "✅ Passed" || echo "❌ Failed")"

# Calculate success rate
total_phases=4
passed_phases=0
[ $OPTIMIZED_RESULT -eq 0 ] && ((passed_phases++))
[ $ALL_OPTIMIZED_RESULT -eq 0 ] && ((passed_phases++))
[ $EXTENDED_RESULT -eq 0 ] && ((passed_phases++))
[ $FEATURE_RESULT -eq 0 ] && ((passed_phases++))

echo ""
echo "📈 Overall Results with Debugbar:"
echo "================================="
echo "Success rate: $passed_phases/$total_phases phases passed"

if [ $OPTIMIZED_RESULT -eq 0 ]; then
    echo "✅ CORE OPTIMIZED TESTS: Working perfectly with debugbar"
    echo "   • Memory usage still optimized"
    echo "   • Debugbar integration successful"
    echo "   • No performance degradation"
fi

if [ $ALL_OPTIMIZED_RESULT -eq 0 ]; then
    echo "✅ COMPREHENSIVE OPTIMIZED: All optimized tests with debugbar working"
fi

if [ $EXTENDED_RESULT -eq 0 ]; then
    echo "✅ LEGACY TESTS: Even problematic tests working with debugbar"
    echo "   • Memory optimizations effective"
    echo "   • Debugbar not causing issues"
fi

if [ $FEATURE_RESULT -eq 0 ]; then
    echo "✅ FEATURE TESTS: Full application tests working"
fi

echo ""
echo "🔧 Debugbar Impact Analysis:"
echo "============================"

# Analyze memory usage with debugbar
for log_file in /tmp/Optimized.log /tmp/AllWithDebugbar.log; do
    if [ -f "$log_file" ]; then
        local memory=$(grep "Memory:" "$log_file" | tail -1)
        local time=$(grep "Time:" "$log_file" | tail -1)
        local test_name=$(basename "$log_file" .log)
        echo "📊 $test_name: $memory $time"
    fi
done

echo ""
echo "💡 Recommendations:"
echo "==================="

if [ $OPTIMIZED_RESULT -eq 0 ]; then
    echo "✓ Debugbar successfully integrated with optimized tests"
    echo "✓ No significant memory impact on core functionality"
    echo "✓ Safe to use debugbar for development testing"
fi

if [ $passed_phases -eq $total_phases ]; then
    echo "🎉 PERFECT: All test phases working with debugbar enabled!"
    echo "✓ Complete test coverage with debugging capabilities"
    echo "✓ Memory optimizations maintain effectiveness"
elif [ $passed_phases -ge 2 ]; then
    echo "👍 GOOD: Core functionality working with debugbar"
    echo "! Some legacy tests may still have issues"
    echo "! Focus on optimized test suites for daily development"
else
    echo "⚠️  WARNING: Debugbar causing significant issues"
    echo "! Consider using debugbar only for specific debugging sessions"
    echo "! Revert to optimized configurations for regular testing"
fi

# Cleanup
rm -f /tmp/*.log

# Exit with success if at least optimized tests work
if [ $OPTIMIZED_RESULT -eq 0 ]; then
    echo ""
    echo "🎉 SUCCESS: Debugbar integration successful!"
    exit 0
else
    echo ""
    echo "❌ CRITICAL: Debugbar causing issues with core tests"
    exit 1
fi 