#!/bin/bash

# Deployment script for Leveler application
# Server: 75.119.139.18
# Target: /var/www

echo "========================================="
echo "Leveler Application Deployment Script"
echo "========================================="

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "Please run as root or use sudo"
    exit 1
fi

# Navigate to /var/www
cd /var/www

# Check if leveler directory exists
if [ -d "leveler" ]; then
    echo "Leveler directory exists. Updating..."
    cd leveler
    git pull origin main
else
    echo "Cloning repository..."
    git clone https://github.com/amithyone/leveler.git
    cd leveler
fi

# Install/Update dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/leveler
chmod -R 755 /var/www/leveler
chmod -R 775 /var/www/leveler/storage
chmod -R 775 /var/www/leveler/bootstrap/cache

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Run migrations (optional - uncomment if needed)
# echo "Running migrations..."
# php artisan migrate --force

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "========================================="
echo "Deployment completed!"
echo "========================================="
echo "Application location: /var/www/leveler"
echo "Make sure to:"
echo "1. Copy .env.example to .env and configure it"
echo "2. Set APP_ENV=production in .env"
echo "3. Configure your web server (Apache/Nginx)"
echo "4. Set up SSL certificate if needed"
echo "========================================="

