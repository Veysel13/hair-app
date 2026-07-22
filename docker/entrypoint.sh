#!/bin/sh
set -e

# Ensure persistent database directory exists
mkdir -p /var/www/html/database/data
chmod 777 /var/www/html/database/data

RUN_SEEDER=false

# Check if SQLite file exists, otherwise create it
if [ ! -f "/var/www/html/database/data/database.sqlite" ]; then
    echo "First run: Creating SQLite database file..."
    touch /var/www/html/database/data/database.sqlite
    chmod 666 /var/www/html/database/data/database.sqlite
    RUN_SEEDER=true
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Seed database on first run
if [ "$RUN_SEEDER" = true ]; then
    echo "Seeding database with default records..."
    php artisan db:seed --force
    php artisan db:seed --class=ShowcaseSeeder --force
fi

# Clear and cache configurations
echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start php-fpm in background and nginx in foreground
echo "Starting services..."
php-fpm -D
nginx -g "daemon off;"
