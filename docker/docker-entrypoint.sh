#!/bin/bash
set -e

echo "🚀 Starting Production Container Orchestration..."

# 1. Bind Nginx to the dynamic port supplied by Render (defaults to 80)
PORT=${PORT:-80}
echo "→ Configuring Nginx to listen on port: $PORT"
sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/http.d/default.conf
sed -i "s/listen \[::\]:80;/listen \[::\]:${PORT};/g" /etc/nginx/http.d/default.conf

# 2. Re-create storage symlink if not present
if [ ! -L public/storage ]; then
    echo "→ Creating storage symbolic link..."
    php artisan storage:link || true
fi

# 3. Wait for the database connection (robust migration guard)
DB_CONN_TRIES=0
MAX_DB_CONN_TRIES=5
until [ $DB_CONN_TRIES -ge $MAX_DB_CONN_TRIES ]
do
    echo "→ Checking database connection (Try $((DB_CONN_TRIES+1))/$MAX_DB_CONN_TRIES)..."
    if php artisan db:monitor --quiet 2>/dev/null; then
        echo "✓ Database connection established successfully!"
        break
    fi
    DB_CONN_TRIES=$((DB_CONN_TRIES+1))
    echo "⚠ Database not ready yet, sleeping 3 seconds..."
    sleep 3
done

if [ $DB_CONN_TRIES -eq $MAX_DB_CONN_TRIES ]; then
    echo "⚠ Warning: Could not verify database connection. Proceeding with migration attempt anyway..."
fi

# 4. Run database migrations automatically in production
echo "→ Running database migrations..."
php artisan migrate --force

# 5. Check if the database has data, if not seed the initial admin & demo users
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "⚙ Database is empty — seeding initial admin and demo accounts..."
    php artisan db:seed --force
fi

# 6. Warm up Laravel caches for maximum production speed
echo "→ Warming up Laravel configuration caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Start PHP-FPM in background
echo "→ Launching PHP-FPM..."
php-fpm -D

# 8. Start Nginx in background
echo "→ Launching Nginx..."
nginx -g "daemon off;" &
NGINX_PID=$!

# 9. Start the Laravel Queue Listener (keeps processing email notifications etc.)
echo "→ Launching Queue Listener (background)..."
php artisan queue:listen --tries=3 --timeout=90 &
QUEUE_PID=$!

# 10. Start the Laravel Custom Scheduler Daemon (fires reminder logs & notifications)
echo "→ Launching Scheduler Worker Daemon (background)..."
php artisan scheduler:work &
SCHEDULER_PID=$!

echo "✓ All services are running! Active monitoring started..."

# 11. Monitoring loop: If Nginx or PHP-FPM dies, terminate the container so Render can restart it
while true; do
    # Check if Nginx process is alive
    if ! kill -0 $NGINX_PID 2>/dev/null; then
        echo "❌ Fatal Error: Nginx has stopped running."
        exit 1
    fi

    # Check if PHP-FPM process is alive
    if ! pgrep php-fpm > /dev/null; then
        echo "❌ Fatal Error: PHP-FPM has stopped running."
        exit 1
    fi

    # Check if Queue Listener is alive (self-healing: restart if it dies)
    if ! kill -0 $QUEUE_PID 2>/dev/null; then
        echo "⚠ Warning: Queue listener died. Restarting..."
        php artisan queue:listen --tries=3 --timeout=90 &
        QUEUE_PID=$!
    fi

    # Check if Scheduler Daemon is alive (self-healing: restart if it dies)
    if ! kill -0 $SCHEDULER_PID 2>/dev/null; then
        echo "⚠ Warning: Scheduler worker died. Restarting..."
        php artisan scheduler:work &
        SCHEDULER_PID=$!
    fi

    sleep 10
done
