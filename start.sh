#!/usr/bin/env bash
# Start the Mount Heaven school website on http://localhost:8000
# Usage:  ./start.sh          (run in a terminal and KEEP IT OPEN while browsing)

cd "$(dirname "$0")" || exit 1

PORT="${1:-8000}"

# Stop any previous instance on this port
if lsof -ti tcp:"$PORT" > /dev/null 2>&1; then
    echo "Port $PORT is already in use — stopping the old server..."
    lsof -ti tcp:"$PORT" | xargs kill 2>/dev/null
    sleep 1
fi

echo "🚀 Starting server:  http://localhost:$PORT"
echo "   (press Ctrl+C to stop — keep this terminal open)"
php -S "localhost:$PORT" server.php
