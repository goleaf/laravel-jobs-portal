#!/bin/bash

# Install Chrome
if ! command -v google-chrome &> /dev/null; then
    echo "Installing Google Chrome..."
    # Depending on the OS, you might need different installation commands
    # This is for CentOS/RHEL
    yum install -y google-chrome-stable
fi

# Install required PHP extensions (if not already installed)
echo "Ensuring required PHP extensions are installed..."
# php -m | grep -E 'zip|gd|mbstring|curl'

# Set PHP memory limit
echo "memory_limit = 4G" > php-memory.ini

# Make sure storage directory is writable
echo "Setting proper permissions..."
chmod -R 777 storage

# Run the Dusk Chrome driver installer with automatic detection
echo "Installing Dusk Chrome driver..."
php -c php-memory.ini artisan dusk:chrome-driver --detect

# Start ChromeDriver in the background
echo "Starting Chrome Driver..."
./vendor/laravel/dusk/bin/chromedriver > /dev/null 2>&1 &
CHROME_PID=$!

# Give ChromeDriver a moment to start
sleep 2

# Run a local server for testing
echo "Starting Laravel server..."
php -c php-memory.ini artisan serve > /dev/null 2>&1 &
SERVER_PID=$!

# Give the server a moment to start
sleep 2

# Run the Dusk tests
echo "Running Dusk tests..."
php -c php-memory.ini artisan dusk --filter=ExampleTest

# Kill background processes when tests are done
kill $CHROME_PID
kill $SERVER_PID

echo "Dusk tests completed!" 