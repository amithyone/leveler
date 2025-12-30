#!/bin/bash

# Script to clear Laravel caches
# Run this on the server after deploying code changes

echo "Clearing Laravel caches..."

cd /var/www/leveler

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# If using opcache, restart PHP-FPM (uncomment if needed)
# sudo systemctl restart php8.1-fpm  # Adjust version as needed

echo "Caches cleared successfully!"
echo "The application should now use the updated code."

