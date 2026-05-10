#!/bin/bash

# Laravel Queue Worker Script for Hostinger Shared Hosting
# This script processes queued jobs with lock file protection

# Project directory
PROJECT_DIR="/home/u960938720/tmtourism.com/public/weblearn"
LOCK_FILE="$PROJECT_DIR/storage/queue-worker.lock"

# Change to project directory
cd "$PROJECT_DIR" || exit 1

# Check if lock file exists and process is still running
if [ -f "$LOCK_FILE" ]; then
    PID=$(cat "$LOCK_FILE")
    if ps -p "$PID" > /dev/null 2>&1; then
        # Process is still running, exit silently
        exit 0
    else
        # Process is dead, remove stale lock file
        rm -f "$LOCK_FILE"
    fi
fi

# Create lock file with current PID
echo $$ > "$LOCK_FILE"

# Trap to ensure lock file is removed on exit
trap "rm -f '$LOCK_FILE'" EXIT INT TERM

# Run queue worker
/usr/bin/php artisan queue:work --stop-when-empty --tries=3 --timeout=50 --memory=128

# Lock file will be automatically removed by trap
exit 0
