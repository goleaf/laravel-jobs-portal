#!/bin/bash

# Redis Queue Worker Script
# Manages Laravel queue workers with Redis backend

WORKERS=3
TIMEOUT=60
SLEEP=3
TRIES=3

echo "🚀 Starting Redis Queue Workers..."
echo "Workers: $WORKERS"
echo "Timeout: $TIMEOUT seconds"
echo "Sleep: $SLEEP seconds"
echo "Max Tries: $TRIES"

# Start queue workers
for i in $(seq 1 $WORKERS); do
    nohup php artisan queue:work redis \
        --sleep=$SLEEP \
        --timeout=$TIMEOUT \
        --tries=$TRIES \
        --daemon \
        --name="worker-$i" \
        > storage/logs/queue-worker-$i.log 2>&1 &
    
    echo "✅ Started worker-$i (PID: $!)"
done

echo "🎉 All Redis queue workers started successfully!"
echo "Monitor with: php artisan queue:monitor"
echo "Stop with: php artisan queue:restart"