#!/bin/bash

# Fix "Service Unavailable" Error
# This usually means PHP-FPM is not running or misconfigured

set -e

echo "========================================="
echo "Fixing Service Unavailable Error"
echo "========================================="
echo ""

# Step 1: Check PHP-FPM status
echo "Step 1: Checking PHP-FPM status..."
if systemctl is-active --quiet php8.2-fpm || systemctl is-active --quiet php8.1-fpm || systemctl is-active --quiet php-fpm; then
    echo "✓ PHP-FPM is running"
    PHP_VERSION=$(systemctl list-units --type=service | grep -oP 'php\d+\.\d+-fpm' | head -1 || echo "php8.2-fpm")
    echo "  Detected: $PHP_VERSION"
else
    echo "✗ PHP-FPM is NOT running!"
    echo "  Attempting to start PHP-FPM..."
    
    # Try different PHP versions
    sudo systemctl start php8.2-fpm 2>/dev/null || \
    sudo systemctl start php8.1-fpm 2>/dev/null || \
    sudo systemctl start php8.0-fpm 2>/dev/null || \
    sudo systemctl start php-fpm 2>/dev/null || \
    echo "⚠ Could not start PHP-FPM automatically"
    
    # Check which one is available
    if systemctl list-unit-files | grep -q php8.2-fpm; then
        PHP_VERSION="php8.2-fpm"
    elif systemctl list-unit-files | grep -q php8.1-fpm; then
        PHP_VERSION="php8.1-fpm"
    elif systemctl list-unit-files | grep -q php8.0-fpm; then
        PHP_VERSION="php8.0-fpm"
    else
        PHP_VERSION="php-fpm"
    fi
    
    sudo systemctl enable $PHP_VERSION
    sudo systemctl start $PHP_VERSION
    echo "✓ PHP-FPM started: $PHP_VERSION"
fi

# Step 2: Check PHP-FPM socket
echo ""
echo "Step 2: Checking PHP-FPM socket..."
PHP_VERSION=$(systemctl list-units --type=service --state=running | grep -oP 'php\d+\.\d+-fpm' | head -1 || echo "php8.2-fpm")
SOCKET_PATH="/var/run/php/${PHP_VERSION}.sock"

if [ -S "$SOCKET_PATH" ]; then
    echo "✓ Socket exists: $SOCKET_PATH"
    ls -la $SOCKET_PATH
else
    echo "✗ Socket not found: $SOCKET_PATH"
    echo "  Checking alternative locations..."
    
    # Check common socket locations
    find /var/run/php -name "*.sock" 2>/dev/null || echo "No PHP sockets found"
    
    # Try to find the correct socket
    ACTUAL_SOCKET=$(find /var/run/php -name "*.sock" 2>/dev/null | head -1)
    if [ -n "$ACTUAL_SOCKET" ]; then
        echo "  Found socket: $ACTUAL_SOCKET"
        SOCKET_PATH="$ACTUAL_SOCKET"
    fi
fi

# Step 3: Check Apache/Nginx configuration
echo ""
echo "Step 3: Checking web server configuration..."

if systemctl is-active --quiet apache2; then
    echo "Apache detected. Checking configuration..."
    
    # Check if socket path in config matches actual socket
    if grep -q "$SOCKET_PATH" /etc/apache2/sites-available/levelercc.conf 2>/dev/null; then
        echo "✓ Apache config has correct socket path"
    else
        echo "⚠ Apache config may have wrong socket path"
        echo "  Current socket: $SOCKET_PATH"
        echo "  Updating Apache config..."
        
        # Update the socket path in the config
        sudo sed -i "s|unix:/var/run/php/php[0-9.]*-fpm\.sock|unix:$SOCKET_PATH|g" /etc/apache2/sites-available/levelercc.conf
        
        # Test and reload
        if sudo apache2ctl configtest; then
            sudo systemctl reload apache2
            echo "✓ Apache config updated and reloaded"
        else
            echo "✗ Apache config test failed!"
        fi
    fi
    
elif systemctl is-active --quiet nginx; then
    echo "Nginx detected. Checking configuration..."
    
    # Check if socket path in config matches actual socket
    if grep -q "$SOCKET_PATH" /etc/nginx/sites-available/levelercc 2>/dev/null; then
        echo "✓ Nginx config has correct socket path"
    else
        echo "⚠ Nginx config may have wrong socket path"
        echo "  Current socket: $SOCKET_PATH"
        echo "  Updating Nginx config..."
        
        # Update the socket path in the config
        sudo sed -i "s|unix:/var/run/php/php[0-9.]*-fpm\.sock|unix:$SOCKET_PATH|g" /etc/nginx/sites-available/levelercc
        
        # Test and reload
        if sudo nginx -t; then
            sudo systemctl reload nginx
            echo "✓ Nginx config updated and reloaded"
        else
            echo "✗ Nginx config test failed!"
        fi
    fi
fi

# Step 4: Check Laravel application
echo ""
echo "Step 4: Checking Laravel application..."
cd /var/www/leveler

# Check if .env exists
if [ ! -f .env ]; then
    echo "⚠ .env file not found!"
    if [ -f .env.example ]; then
        echo "  Copying .env.example to .env..."
        cp .env.example .env
        php artisan key:generate
        echo "✓ .env file created"
    fi
fi

# Check storage permissions
echo "Checking storage permissions..."
if [ ! -w storage ]; then
    echo "⚠ Storage directory not writable"
    sudo chown -R www-data:www-data storage
    sudo chmod -R 775 storage
    echo "✓ Storage permissions fixed"
fi

# Check bootstrap/cache permissions
if [ ! -w bootstrap/cache ]; then
    echo "⚠ Bootstrap cache not writable"
    sudo chown -R www-data:www-data bootstrap/cache
    sudo chmod -R 775 bootstrap/cache
    echo "✓ Bootstrap cache permissions fixed"
fi

# Step 5: Check for PHP errors
echo ""
echo "Step 5: Checking for PHP errors..."
php artisan config:clear 2>&1 | head -5 || echo "⚠ Could not clear config"
php artisan cache:clear 2>&1 | head -5 || echo "⚠ Could not clear cache"

# Step 6: Test PHP-FPM
echo ""
echo "Step 6: Testing PHP-FPM..."
php -v
php -m | grep -i fpm || echo "PHP-FPM module check"

# Step 7: Check web server error logs
echo ""
echo "Step 7: Recent web server errors..."
if systemctl is-active --quiet apache2; then
    echo "--- Apache Error Log (last 10 lines) ---"
    sudo tail -10 /var/log/apache2/error.log 2>/dev/null || echo "No error log found"
    echo ""
    echo "--- levelercc Error Log (last 10 lines) ---"
    sudo tail -10 /var/log/apache2/levelercc_error.log 2>/dev/null || echo "No levelercc error log found"
elif systemctl is-active --quiet nginx; then
    echo "--- Nginx Error Log (last 10 lines) ---"
    sudo tail -10 /var/log/nginx/error.log 2>/dev/null || echo "No error log found"
fi

# Step 8: Check Laravel logs
echo ""
echo "Step 8: Recent Laravel errors..."
if [ -f storage/logs/laravel.log ]; then
    tail -20 storage/logs/laravel.log | grep -i error || echo "No recent errors in Laravel log"
else
    echo "No Laravel log file found"
fi

echo ""
echo "========================================="
echo "Diagnostic Complete"
echo "========================================="
echo ""
echo "Summary:"
echo "  PHP-FPM: $(systemctl is-active php8.2-fpm php8.1-fpm php8.0-fpm php-fpm 2>/dev/null | grep -v inactive | head -1 || echo 'NOT RUNNING')"
echo "  Socket: $SOCKET_PATH"
echo "  Web Server: $(systemctl is-active apache2 nginx 2>/dev/null | grep -v inactive | head -1 || echo 'NOT RUNNING')"
echo ""
echo "Next steps if still not working:"
echo "1. Check PHP-FPM: sudo systemctl status php8.2-fpm"
echo "2. Check web server: sudo systemctl status apache2 (or nginx)"
echo "3. Check logs: sudo tail -f /var/log/apache2/error.log"
echo "4. Test PHP: php -v"
echo ""

