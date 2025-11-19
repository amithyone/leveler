#!/bin/bash

# Complete server setup script for Leveler
# Run this on server: 75.119.139.18

echo "========================================="
echo "Leveler Server Setup Script"
echo "========================================="

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "Please run as root or use sudo"
    exit 1
fi

# Step 1: Install Composer if not installed
if ! command -v composer &> /dev/null; then
    echo "Installing Composer..."
    cd /tmp
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    echo "Composer installed successfully!"
else
    echo "Composer is already installed"
fi

# Step 2: Navigate to /var/www
cd /var/www

# Step 3: Clone or update repository
if [ -d "leveler" ]; then
    echo "Leveler directory exists. Updating..."
    cd leveler
    git pull origin main
else
    echo "Cloning repository..."
    git clone https://github.com/amithyone/leveler.git
    cd leveler
fi

# Step 4: Install Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Step 5: Set permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/leveler
chmod -R 755 /var/www/leveler
chmod -R 775 /var/www/leveler/storage
chmod -R 775 /var/www/leveler/bootstrap/cache

# Step 6: Setup environment (if first time)
if [ ! -f ".env" ]; then
    echo "Setting up environment file..."
    cp .env.example .env
    php artisan key:generate
    echo "Please edit .env file with your database credentials: nano .env"
fi

# Step 7: Create storage link
echo "Creating storage link..."
php artisan storage:link

# Step 8: Clear and cache
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "========================================="
echo "Setup Complete!"
echo "Location: /var/www/leveler"
echo "Next steps:"
echo "1. Edit .env file: nano /var/www/leveler/.env"
echo "2. Run migrations: cd /var/www/leveler && php artisan migrate --force"
echo "3. Seed database (optional): php artisan db:seed"
echo "========================================="

