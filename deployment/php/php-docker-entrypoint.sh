#!/bin/bash
set -e

php artisan migrate || echo "migrate failed with code $?"

echo "Starting queue worker in the background..."
php artisan queue:listen --queue=default &

echo "Starting php-fpm..."
exec php-fpm
