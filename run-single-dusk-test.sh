#!/bin/bash

# Set memory limits
export PHP_INI_MEMORY_LIMIT=4G

# Kill any existing chromedriver processes
pkill -f chromedriver || true

# Start ChromeDriver in background
/usr/local/bin/chromedriver --port=9515 --whitelisted-ips= > /dev/null 2>&1 &
CHROMEDRIVER_PID=$!

# Wait for ChromeDriver to start
sleep 3

# Run the test with memory limit
echo "Running Basic Functionality Test..."
php -d memory_limit=4G artisan dusk tests/Browser/BasicFunctionalityTest.php --without-tty

TEST_RESULT=$?

# Kill ChromeDriver
kill $CHROMEDRIVER_PID 2>/dev/null || true

echo "Test completed with exit code: $TEST_RESULT"
exit $TEST_RESULT 