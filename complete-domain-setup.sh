#!/bin/bash

# Complete Domain Setup Script for levelercc.com
# Run this on your server: 75.119.139.18

set -e  # Exit on error

echo "========================================="
echo "Complete Domain Setup for levelercc.com"
echo "========================================="
echo ""

# Step 1: Navigate to project directory
echo "Step 1: Navigating to project directory..."
cd /var/www/leveler || { echo "ERROR: /var/www/leveler not found!"; exit 1; }

# Step 2: Pull latest code
echo "Step 2: Pulling latest code from GitHub..."
git pull origin main

# Step 3: Detect and configure web server
echo ""
echo "Step 3: Detecting web server..."

if systemctl is-active --quiet apache2; then
    echo "Apache detected. Configuring Apache..."
    
    # Copy configuration
    sudo cp apache-leveler.conf /etc/apache2/sites-available/leveler.conf
    echo "✓ Configuration file copied"
    
    # Enable modules
    echo "Enabling Apache modules..."
    sudo a2enmod rewrite 2>/dev/null || true
    sudo a2enmod proxy_fcgi 2>/dev/null || true
    sudo a2enmod setenvif 2>/dev/null || true
    echo "✓ Modules enabled"
    
    # Enable site
    echo "Enabling site..."
    sudo a2ensite leveler.conf 2>/dev/null || true
    sudo a2dissite 000-default.conf 2>/dev/null || true
    echo "✓ Site enabled"
    
    # Test configuration
    echo "Testing Apache configuration..."
    if sudo apache2ctl configtest; then
        echo "✓ Configuration test passed"
        sudo systemctl reload apache2
        echo "✓ Apache reloaded"
    else
        echo "✗ Configuration test failed!"
        exit 1
    fi
    
elif systemctl is-active --quiet nginx; then
    echo "Nginx detected. Configuring Nginx..."
    
    # Copy configuration
    sudo cp nginx-leveler.conf /etc/nginx/sites-available/leveler
    echo "✓ Configuration file copied"
    
    # Enable site
    echo "Enabling site..."
    sudo ln -sf /etc/nginx/sites-available/leveler /etc/nginx/sites-enabled/
    sudo rm -f /etc/nginx/sites-enabled/default
    echo "✓ Site enabled"
    
    # Test configuration
    echo "Testing Nginx configuration..."
    if sudo nginx -t; then
        echo "✓ Configuration test passed"
        sudo systemctl reload nginx
        echo "✓ Nginx reloaded"
    else
        echo "✗ Configuration test failed!"
        exit 1
    fi
    
else
    echo "ERROR: Neither Apache nor Nginx is running!"
    echo "Please install and start a web server first."
    exit 1
fi

# Step 4: Set permissions
echo ""
echo "Step 4: Setting file permissions..."
sudo chown -R www-data:www-data /var/www/leveler
sudo chmod -R 755 /var/www/leveler
sudo chmod -R 775 /var/www/leveler/storage
sudo chmod -R 775 /var/www/leveler/bootstrap/cache
echo "✓ Permissions set"

# Step 5: Update .env file
echo ""
echo "Step 5: Updating .env file..."
if [ -f .env ]; then
    # Backup .env
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    echo "✓ .env backed up"
    
    # Update APP_URL
    if grep -q "APP_URL=" .env; then
        sed -i 's|APP_URL=.*|APP_URL=https://levelercc.com|g' .env
    else
        echo "APP_URL=https://levelercc.com" >> .env
    fi
    
    # Update APP_ENV
    if grep -q "APP_ENV=" .env; then
        sed -i 's|APP_ENV=.*|APP_ENV=production|g' .env
    else
        echo "APP_ENV=production" >> .env
    fi
    
    # Update APP_DEBUG
    if grep -q "APP_DEBUG=" .env; then
        sed -i 's|APP_DEBUG=.*|APP_DEBUG=false|g' .env
    else
        echo "APP_DEBUG=false" >> .env
    fi
    
    echo "✓ .env updated"
else
    echo "WARNING: .env file not found!"
    echo "Please create .env file from .env.example"
fi

# Step 6: Clear and cache Laravel config
echo ""
echo "Step 6: Clearing and caching Laravel configuration..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Laravel caches cleared and rebuilt"

# Step 7: Create storage link if not exists
echo ""
echo "Step 7: Creating storage link..."
php artisan storage:link 2>/dev/null || true
echo "✓ Storage link created"

# Step 8: Verify setup
echo ""
echo "Step 8: Verifying setup..."
echo "Checking DNS resolution..."
if nslookup levelercc.com > /dev/null 2>&1; then
    echo "✓ DNS is resolving"
else
    echo "⚠ DNS may not be fully propagated yet"
fi

echo ""
echo "========================================="
echo "Domain setup complete!"
echo "========================================="
echo ""
echo "Your site should now be accessible at:"
echo "  - http://levelercc.com"
echo "  - http://www.levelercc.com"
echo ""
echo "Next steps:"
echo "1. Wait for DNS propagation (if not already done)"
echo "2. Test your site: curl -I http://levelercc.com"
echo "3. Set up SSL certificate:"
if systemctl is-active --quiet apache2; then
    echo "   sudo apt install certbot python3-certbot-apache"
    echo "   sudo certbot --apache -d levelercc.com -d www.levelercc.com"
else
    echo "   sudo apt install certbot python3-certbot-nginx"
    echo "   sudo certbot --nginx -d levelercc.com -d www.levelercc.com"
fi
echo ""

