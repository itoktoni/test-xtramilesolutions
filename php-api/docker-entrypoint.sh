#!/bin/bash

# Run migrations (docker-compose healthcheck ensures MySQL is ready)
echo "Running migrations..."
php artisan migrate --force || echo "Migrations failed or already exist"

# Start Laravel server
echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8080