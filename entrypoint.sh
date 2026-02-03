#!/bin/sh
set -e

# Ensure permissions at container start
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force || true
fi

# Cache config & routes (safe to ignore failures in edge cases)
php artisan config:cache || true
php artisan route:cache || true

# Retry migrations until DB is ready (10 tries)
RETRIES=10
COUNT=0
until php artisan migrate --force -q; do
  COUNT=$((COUNT+1))
  if [ "$COUNT" -ge "$RETRIES" ]; then
    echo "Migrations failed after $RETRIES attempts — continuing to start container."
    break
  fi
  echo "Migration attempt $COUNT failed — retrying in 5s..."
  sleep 5
done

# Finally start supervisor to run nginx + php-fpm
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
