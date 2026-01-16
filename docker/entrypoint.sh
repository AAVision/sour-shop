#!/bin/sh

echo "Starting eCommerce Laravel Application..."

# Wait for MySQL (with timeout)
echo "Waiting for MySQL ($DB_HOST:$DB_PORT)..."
MAX_ATTEMPTS=30
ATTEMPT=0
while ! nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do
  ATTEMPT=$((ATTEMPT + 1))
  if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
    echo "✗ MySQL did not become available after ${MAX_ATTEMPTS} attempts"
    echo "Continuing anyway (will retry on first request)..."
    break
  fi
  echo "  Attempt $ATTEMPT/$MAX_ATTEMPTS: Waiting for MySQL..."
  sleep 1
done
echo "✓ MySQL connection available"

# Fix permissions
echo "Setting up storage directories..."
chmod -R 775 /app/storage 2>/dev/null || true
chmod -R 775 /app/bootstrap/cache 2>/dev/null || true

# Clear and cache config (non-blocking)
echo "Optimizing application..."
php /app/artisan config:cache 2>/dev/null || true
php /app/artisan route:cache 2>/dev/null || true
php /app/artisan view:cache 2>/dev/null || true
php /app/artisan cache:clear 2>/dev/null || true

# Run migrations in background (don't block startup)
echo "Running migrations (in background)..."
(php /app/artisan migrate --force 2>/dev/null || echo "⚠ Migration skipped or failed") &

# Create storage link
php /app/artisan storage:link 2>/dev/null || true

echo "✓ Application ready!"
echo "Starting PHP-FPM..."
exec "$@"
