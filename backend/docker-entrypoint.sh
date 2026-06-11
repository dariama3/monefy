#!/bin/sh

echo "Waiting for DB..."

until nc -z monefy_db 3306; do
  sleep 2
done

echo "DB is up - running migrations"

php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port=8000
