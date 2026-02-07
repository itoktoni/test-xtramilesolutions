#!/bin/bash

# Wait for MySQL to be ready using netcat or timeout
echo "Waiting for MySQL to be ready..."
max_attempts=30
attempt=0
while [ $attempt -lt $max_attempts ]; do
    if nc -z mysql 3306 2>/dev/null || php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; then
        echo "MySQL is ready!"
        break
    fi
    attempt=$((attempt + 1))
    echo "Attempt $attempt/$max_attempts - MySQL is unavailable, sleeping..."
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "MySQL did not become ready in time, proceeding anyway..."
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Start Laravel server
echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8080