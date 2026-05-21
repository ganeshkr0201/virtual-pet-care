#!/bin/bash
# ─────────────────────────────────────────────────────────────────
# Virtual Pet Care — Start Script
# Starts the Laravel dev server + scheduler worker in parallel
# Usage: bash start.sh
# ─────────────────────────────────────────────────────────────────

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_DIR"

echo ""
echo "🐾 Virtual Pet Care"
echo "─────────────────────────────────────────────────────────────"

# Check if DB has data
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "⚙  Database empty — running migrations and seeding..."
    php artisan migrate:fresh --seed --force
    echo "✓  Database ready."
fi

# Generate today's reminder logs
echo "⚙  Generating today's reminder logs..."
php artisan reminders:generate-logs
echo ""

echo "Starting services..."
echo "  → Web server:        http://localhost:8000"
echo "  → Scheduler worker:  checks reminders every minute"
echo "  → Feature guide:     http://localhost:8000/feature-guide.html"
echo ""
echo "Demo accounts:"
echo "  Pet Owner:  demo@virtualpetcare.com  / password"
echo "  Admin:      admin@virtualpetcare.com / password"
echo ""
echo "Press Ctrl+C to stop all services."
echo "─────────────────────────────────────────────────────────────"
echo ""

# Run web server and scheduler worker in parallel
php artisan serve --host=127.0.0.1 --port=8000 &
SERVER_PID=$!

php artisan scheduler:work &
SCHEDULER_PID=$!

# Trap Ctrl+C and kill both processes
trap "echo ''; echo 'Stopping...'; kill $SERVER_PID $SCHEDULER_PID 2>/dev/null; exit 0" INT TERM

# Wait for both
wait $SERVER_PID $SCHEDULER_PID
