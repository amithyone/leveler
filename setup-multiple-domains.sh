#!/bin/bash

# Setup Multiple Domains on Same Server
# biggestlogs.com → /var/www/biggestlogs
# levelercc.com → /var/www/leveler

set -e

echo "========================================="
echo "Setting Up Multiple Domains"
echo "========================================="
echo ""

# Detect web server
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
echo "Step 1: Checking existing configurations..."

if [ "$WEB_SERVER" = "apache" ]; then
    echo "--- Current Apache enabled sites ---"
    ls -la /etc/apache2/sites-enabled/
    echo ""
    echo "--- Checking for biggestlogs configuration ---"
    grep -r "biggestlogs\|ServerName.*biggestlogs" /etc/apache2/sites-available/ 2>/dev/null || echo "No biggestlogs config found"
    echo ""
    echo "--- Checking for levelercc configuration ---"
    grep -r "levelercc\|ServerName.*levelercc" /etc/apache2/sites-available/ 2>/dev/null || echo "No levelercc config found"
    
elif [ "$WEB_SERVER" = "nginx" ]; then
    echo "--- Current Nginx enabled sites ---"
    ls -la /etc/nginx/sites-enabled/
    echo ""
    echo "--- Checking for biggestlogs configuration ---"
    grep -r "biggestlogs\|server_name.*biggestlogs" /etc/nginx/sites-available/ 2>/dev/null || echo "No biggestlogs config found"
    echo ""
    echo "--- Checking for levelercc configuration ---"
    grep -r "levelercc\|server_name.*levelercc" /etc/nginx/sites-available/ 2>/dev/null || echo "No levelercc config found"
fi

echo ""
echo "Step 2: Configuring levelercc.com..."

cd /var/www/leveler
git pull origin main

if [ "$WEB_SERVER" = "apache" ]; then
    echo "--- Creating Apache config for levelercc.com ---"
    
    # Create levelercc.com virtual host
    sudo tee /etc/apache2/sites-available/levelercc.conf > /dev/null <<EOF
<VirtualHost *:80>
    ServerName levelercc.com
    ServerAlias www.levelercc.com
    
    DocumentRoot /var/www/leveler/public
    
    <Directory /var/www/leveler/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/levelercc_error.log
    CustomLog \${APACHE_LOG_DIR}/levelercc_access.log combined
    
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.2-fpm.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
EOF
    
    # Enable levelercc site
    sudo a2ensite levelercc.conf
    echo "✓ levelercc.com Apache config created and enabled"
    
    # Check if biggestlogs has its own config
    if [ -f "/etc/apache2/sites-available/biggestlogs.conf" ] || [ -f "/etc/apache2/sites-available/000-default.conf" ]; then
        echo "--- Keeping biggestlogs.com configuration separate ---"
        # Don't disable if it's a separate config file
    else
        echo "⚠ Warning: No separate biggestlogs config found"
        echo "   Make sure biggestlogs.com has its own virtual host"
    fi
    
    # Disable default site only if it's catching all domains
    if grep -q "ServerName.*biggestlogs\|DocumentRoot.*biggestlogs" /etc/apache2/sites-available/000-default.conf 2>/dev/null; then
        echo "⚠ Default site appears to be for biggestlogs, keeping it enabled"
    else
        sudo a2dissite 000-default.conf 2>/dev/null || true
    fi
    
    # Enable required modules
    sudo a2enmod rewrite proxy_fcgi setenvif 2>/dev/null || true
    
    # Test and reload
    if sudo apache2ctl configtest; then
        sudo systemctl reload apache2
        echo "✓ Apache reloaded"
    else
        echo "✗ Apache configuration test failed!"
        exit 1
    fi
    
elif [ "$WEB_SERVER" = "nginx" ]; then
    echo "--- Creating Nginx config for levelercc.com ---"
    
    # Create levelercc.com server block
    sudo tee /etc/nginx/sites-available/levelercc > /dev/null <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name levelercc.com www.levelercc.com;
    root /var/www/leveler/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    
    # Enable levelercc site
    sudo ln -sf /etc/nginx/sites-available/levelercc /etc/nginx/sites-enabled/
    echo "✓ levelercc.com Nginx config created and enabled"
    
    # Check if biggestlogs has its own config
    if [ -f "/etc/nginx/sites-available/biggestlogs" ]; then
        echo "--- Keeping biggestlogs.com configuration separate ---"
        sudo ln -sf /etc/nginx/sites-available/biggestlogs /etc/nginx/sites-enabled/ 2>/dev/null || true
    else
        echo "⚠ Warning: No separate biggestlogs config found"
        echo "   Make sure biggestlogs.com has its own server block"
    fi
    
    # Remove default only if it's not for biggestlogs
    if [ -L "/etc/nginx/sites-enabled/default" ]; then
        if ! grep -q "server_name.*biggestlogs" /etc/nginx/sites-available/default 2>/dev/null; then
            sudo rm -f /etc/nginx/sites-enabled/default
        fi
    fi
    
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
echo "Step 3: Setting permissions..."
sudo chown -R www-data:www-data /var/www/leveler
sudo chmod -R 755 /var/www/leveler
sudo chmod -R 775 /var/www/leveler/storage
sudo chmod -R 775 /var/www/leveler/bootstrap/cache
echo "✓ Permissions set"

echo ""
echo "Step 4: Verifying configurations..."

if [ "$WEB_SERVER" = "apache" ]; then
    echo "--- Enabled Apache sites ---"
    ls -la /etc/apache2/sites-enabled/
    echo ""
    echo "--- levelercc.com config ---"
    grep -E "ServerName|DocumentRoot" /etc/apache2/sites-available/levelercc.conf
    echo ""
    echo "--- biggestlogs.com config (if exists) ---"
    grep -E "ServerName|DocumentRoot" /etc/apache2/sites-available/biggestlogs.conf 2>/dev/null || echo "No separate biggestlogs.conf found"
    
elif [ "$WEB_SERVER" = "nginx" ]; then
    echo "--- Enabled Nginx sites ---"
    ls -la /etc/nginx/sites-enabled/
    echo ""
    echo "--- levelercc.com config ---"
    grep -E "server_name|root" /etc/nginx/sites-available/levelercc
    echo ""
    echo "--- biggestlogs.com config (if exists) ---"
    grep -E "server_name|root" /etc/nginx/sites-available/biggestlogs 2>/dev/null || echo "No separate biggestlogs config found"
fi

echo ""
echo "========================================="
echo "Setup Complete!"
echo "========================================="
echo ""
echo "Summary:"
echo "  ✓ biggestlogs.com → /var/www/biggestlogs"
echo "  ✓ levelercc.com → /var/www/leveler/public"
echo ""
echo "Next steps:"
echo "1. Verify DNS: nslookup levelercc.com (should show 75.119.139.18)"
echo "2. Test levelercc.com: curl -I http://levelercc.com"
echo "3. Test biggestlogs.com: curl -I http://biggestlogs.com"
echo "4. Both domains should work independently"
echo ""

