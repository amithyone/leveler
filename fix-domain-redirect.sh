#!/bin/bash

# Fix Domain Redirect Issue - levelercc.com redirecting to biggestlogs.com
# Run this on your server to diagnose and fix the redirect

set -e

echo "========================================="
echo "Fixing levelercc.com Redirect Issue"
echo "========================================="
echo ""

# Step 1: Check which web server is running
echo "Step 1: Detecting web server..."
if systemctl is-active --quiet apache2; then
    WEB_SERVER="apache"
    echo "✓ Apache detected"
elif systemctl is-active --quiet nginx; then
    WEB_SERVER="nginx"
    echo "✓ Nginx detected"
else
    echo "✗ No web server detected!"
    exit 1
fi

echo ""
echo "Step 2: Checking current configuration..."

if [ "$WEB_SERVER" = "apache" ]; then
    echo "--- Checking Apache enabled sites ---"
    ls -la /etc/apache2/sites-enabled/
    echo ""
    echo "--- Checking for biggestlogs references ---"
    grep -r "biggestlogs" /etc/apache2/sites-enabled/ 2>/dev/null || echo "No biggestlogs found in enabled sites"
    echo ""
    echo "--- Checking for redirects ---"
    grep -r "Redirect\|RewriteRule.*biggestlogs" /etc/apache2/sites-enabled/ 2>/dev/null || echo "No redirects to biggestlogs found"
    
elif [ "$WEB_SERVER" = "nginx" ]; then
    echo "--- Checking Nginx enabled sites ---"
    ls -la /etc/nginx/sites-enabled/
    echo ""
    echo "--- Checking for biggestlogs references ---"
    grep -r "biggestlogs" /etc/nginx/sites-enabled/ 2>/dev/null || echo "No biggestlogs found in enabled sites"
    echo ""
    echo "--- Checking for redirects ---"
    grep -r "return.*301\|rewrite.*biggestlogs" /etc/nginx/sites-enabled/ 2>/dev/null || echo "No redirects to biggestlogs found"
fi

echo ""
echo "Step 3: Ensuring levelercc.com configuration is active..."

cd /var/www/leveler
git pull origin main

if [ "$WEB_SERVER" = "apache" ]; then
    echo "--- Configuring Apache for levelercc.com ---"
    
    # Copy configuration
    sudo cp apache-leveler.conf /etc/apache2/sites-available/leveler.conf
    
    # Disable any default site that might be catching all requests
    sudo a2dissite 000-default.conf 2>/dev/null || true
    sudo a2dissite default-ssl.conf 2>/dev/null || true
    
    # Enable leveler site
    sudo a2ensite leveler.conf
    
    # Ensure leveler.conf has higher priority (lower number = higher priority)
    # Check if there are other sites that might be catching requests
    echo "--- Checking site priorities ---"
    ls -la /etc/apache2/sites-enabled/ | grep -E "\.conf$"
    
    # Test and reload
    if sudo apache2ctl configtest; then
        sudo systemctl reload apache2
        echo "✓ Apache reloaded"
    else
        echo "✗ Apache configuration test failed!"
        exit 1
    fi
    
elif [ "$WEB_SERVER" = "nginx" ]; then
    echo "--- Configuring Nginx for levelercc.com ---"
    
    # Copy configuration
    sudo cp nginx-leveler.conf /etc/nginx/sites-available/leveler
    
    # Remove default site
    sudo rm -f /etc/nginx/sites-enabled/default
    
    # Enable leveler site
    sudo ln -sf /etc/nginx/sites-available/leveler /etc/nginx/sites-enabled/
    
    # Check for other sites that might be catching requests
    echo "--- Checking enabled sites ---"
    ls -la /etc/nginx/sites-enabled/
    
    # Test and reload
    if sudo nginx -t; then
        sudo systemctl reload nginx
        echo "✓ Nginx reloaded"
    else
        echo "✗ Nginx configuration test failed!"
        exit 1
    fi
fi

echo ""
echo "Step 4: Checking DNS resolution..."
echo "--- Testing DNS ---"
nslookup levelercc.com || echo "DNS lookup failed"
echo ""
host levelercc.com || echo "Host command failed"

echo ""
echo "Step 5: Verifying document root..."
if [ -d "/var/www/leveler/public" ]; then
    echo "✓ Document root exists: /var/www/leveler/public"
    ls -la /var/www/leveler/public/index.php || echo "⚠ index.php not found!"
else
    echo "✗ Document root not found!"
fi

echo ""
echo "Step 6: Setting permissions..."
sudo chown -R www-data:www-data /var/www/leveler
sudo chmod -R 755 /var/www/leveler
sudo chmod -R 775 /var/www/leveler/storage
sudo chmod -R 775 /var/www/leveler/bootstrap/cache
echo "✓ Permissions set"

echo ""
echo "Step 7: Testing local access..."
curl -I http://localhost -H "Host: levelercc.com" 2>/dev/null | head -5 || echo "Local test failed"

echo ""
echo "========================================="
echo "Diagnostic Complete"
echo "========================================="
echo ""
echo "IMPORTANT: If levelercc.com still redirects to biggestlogs.com:"
echo ""
echo "1. Check DNS records:"
echo "   - Run: nslookup levelercc.com"
echo "   - Should point to: 75.119.139.18"
echo "   - If it points elsewhere, update DNS at your registrar"
echo ""
echo "2. Check for other virtual hosts catching the domain:"
if [ "$WEB_SERVER" = "apache" ]; then
    echo "   - Run: sudo grep -r 'ServerName.*levelercc\|ServerAlias.*levelercc' /etc/apache2/sites-enabled/"
    echo "   - Only leveler.conf should have levelercc.com"
elif [ "$WEB_SERVER" = "nginx" ]; then
    echo "   - Run: sudo grep -r 'server_name.*levelercc' /etc/nginx/sites-enabled/"
    echo "   - Only leveler should have levelercc.com"
fi
echo ""
echo "3. Check domain registrar for redirects:"
echo "   - Some registrars have 'domain parking' or redirect features"
echo "   - Disable any redirects at the registrar level"
echo ""
echo "4. Clear browser cache and try again"
echo ""
