#!/bin/bash

# Deployment script to run on the server
# This will pull latest changes and clear all caches

echo "========================================="
echo "Deploying latest changes..."
echo "========================================="

cd /var/www/leveler

# Pull latest changes
echo "Pulling latest changes from repository..."
git pull origin main

# Clear all caches (CRITICAL - this fixes the route cache issue)
echo "Clearing Laravel caches..."
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Optional: Rebuild caches for production (uncomment if needed)
# echo "Rebuilding caches..."
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

echo "========================================="
echo "Deployment complete!"
echo "The courseDetails method should now be available."
echo "========================================="

