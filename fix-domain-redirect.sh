#!/bin/bash

# Fix Domain Redirect Issue - levelercc.com redirecting to biggestlogs.com
# Run this on your server

set -e

echo "========================================="
echo "Fixing Domain Redirect Issue"
echo "========================================="
echo ""

# Step 1: Check current web server configuration
echo "Step 1: Checking web server configuration..."

if systemctl is-active --quiet apache2; then
    echo "Apache detected"
    echo ""
    echo "Checking Apache virtual hosts..."
    echo "--- Active sites ---"
    ls -la /etc/apache2/sites-enabled/
    echo ""
    echo "--- Checking for biggestlogs references ---"
    grep -r "biggestlogs" /etc/apache2/sites-enabled/ 2>/dev/null || echo "No biggestlogs found in enabled sites"
    echo ""
    echo "--- Checking default site ---"
    if [ -f /etc/apache2/sites-enabled/000-default.conf ]; then
        echo "WARNING: Default site is still enabled!"
        cat /etc/apache2/sites-enabled/000-default.conf | grep -i "ServerName\|ServerAlias\|Redirect" || echo "No redirects found"
    fi
    
elif systemctl is-active --quiet nginx; then
    echo "Nginx detected"
    echo ""
    echo "Checking Nginx server blocks..."
    echo "--- Active sites ---"
    ls -la /etc/nginx/sites-enabled/
    echo ""
    echo "--- Checking for biggestlogs references ---"
    grep -r "biggestlogs" /etc/nginx/sites-enabled/ 2>/dev/null || echo "No biggestlogs found in enabled sites"
    echo ""
    echo "--- Checking default site ---"
    if [ -f /etc/nginx/sites-enabled/default ]; then
        echo "WARNING: Default site is still enabled!"
        cat /etc/nginx/sites-enabled/default | grep -i "server_name\|return\|rewrite" || echo "No redirects found"
    fi
fi

# Step 2: Check DNS
echo ""
echo "Step 2: Checking DNS resolution..."
echo "Resolving levelercc.com:"
nslookup levelercc.com || dig levelercc.com
echo ""
echo "Resolving www.levelercc.com:"
nslookup www.levelercc.com || dig www.levelercc.com

# Step 3: Pull latest code and update configuration
echo ""
echo "Step 3: Updating configuration..."
cd /var/www/leveler
git pull origin main

# Step 4: Disable default sites and enable leveler site
echo ""
echo "Step 4: Configuring web server..."

if systemctl is-active --quiet apache2; then
    echo "Configuring Apache..."
    
    # Disable default site
    sudo a2dissite 000-default.conf 2>/dev/null || true
    
    # Copy and enable leveler configuration
    sudo cp apache-leveler.conf /etc/apache2/sites-available/leveler.conf
    
    # Make sure ServerName is correct
    sudo sed -i 's/ServerName.*/ServerName levelercc.com/' /etc/apache2/sites-available/leveler.conf
    sudo sed -i 's/ServerAlias.*/ServerAlias www.levelercc.com/' /etc/apache2/sites-available/leveler.conf
    
    # Enable leveler site
    sudo a2ensite leveler.conf
    
    # Test configuration
    echo "Testing Apache configuration..."
    if sudo apache2ctl configtest; then
        echo "✓ Configuration is valid"
        sudo systemctl reload apache2
        echo "✓ Apache reloaded"
    else
        echo "✗ Configuration test failed!"
        exit 1
    fi
    
elif systemctl is-active --quiet nginx; then
    echo "Configuring Nginx..."
    
    # Remove default site
    sudo rm -f /etc/nginx/sites-enabled/default
    
    # Copy and enable leveler configuration
    sudo cp nginx-leveler.conf /etc/nginx/sites-available/leveler
    
    # Make sure server_name is correct
    sudo sed -i 's/server_name.*/server_name levelercc.com www.levelercc.com;/' /etc/nginx/sites-available/leveler
    
    # Enable leveler site
    sudo ln -sf /etc/nginx/sites-available/leveler /etc/nginx/sites-enabled/
    
    # Test configuration
    echo "Testing Nginx configuration..."
    if sudo nginx -t; then
        echo "✓ Configuration is valid"
        sudo systemctl reload nginx
        echo "✓ Nginx reloaded"
    else
        echo "✗ Configuration test failed!"
        exit 1
    fi
fi

# Step 5: Update .env
echo ""
echo "Step 5: Updating .env file..."
if [ -f .env ]; then
    sed -i 's|APP_URL=.*|APP_URL=https://levelercc.com|g' .env
    echo "✓ APP_URL updated to https://levelercc.com"
else
    echo "WARNING: .env file not found!"
fi

# Step 6: Clear Laravel caches
echo ""
echo "Step 6: Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
echo "✓ Caches cleared and rebuilt"

# Step 7: Test the site
echo ""
echo "Step 7: Testing site..."
echo "Testing HTTP response..."
curl -I http://levelercc.com 2>&1 | head -20 || echo "Could not connect to levelercc.com"
echo ""
echo "Testing localhost..."
curl -I http://localhost 2>&1 | head -20 || echo "Could not connect to localhost"

echo ""
echo "========================================="
echo "Domain redirect fix complete!"
echo "========================================="
echo ""
echo "IMPORTANT: If levelercc.com still redirects to biggestlogs.com:"
echo "1. Check your domain registrar's DNS settings"
echo "2. Verify DNS is pointing to: 75.119.139.18"
echo "3. Wait for DNS propagation (can take up to 48 hours)"
echo "4. Check if there's a redirect at the domain registrar level"
echo "5. Verify no other virtual host is catching the request"
echo ""

