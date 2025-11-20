#!/bin/bash

# Domain Setup Script for levelercc.com
# Run this script on your server (75.119.139.18) to configure the domain

echo "========================================="
echo "Setting up domain: levelercc.com"
echo "========================================="

# Check which web server is installed
if systemctl is-active --quiet apache2; then
    echo "Apache detected. Configuring Apache..."
    
    # Pull latest code
    cd /var/www/leveler
    git pull origin main
    
    # Copy configuration
    sudo cp apache-leveler.conf /etc/apache2/sites-available/leveler.conf
    
    # Enable modules
    sudo a2enmod rewrite
    sudo a2enmod proxy_fcgi
    sudo a2enmod setenvif
    
    # Enable site
    sudo a2ensite leveler.conf
    sudo a2dissite 000-default.conf 2>/dev/null
    
    # Test and reload
    sudo apache2ctl configtest
    sudo systemctl reload apache2
    
    echo "Apache configuration complete!"
    
elif systemctl is-active --quiet nginx; then
    echo "Nginx detected. Configuring Nginx..."
    
    # Pull latest code
    cd /var/www/leveler
    git pull origin main
    
    # Copy configuration
    sudo cp nginx-leveler.conf /etc/nginx/sites-available/leveler
    
    # Enable site
    sudo ln -sf /etc/nginx/sites-available/leveler /etc/nginx/sites-enabled/
    sudo rm -f /etc/nginx/sites-enabled/default
    
    # Test and reload
    sudo nginx -t
    sudo systemctl reload nginx
    
    echo "Nginx configuration complete!"
    
else
    echo "ERROR: Neither Apache nor Nginx is running!"
    echo "Please install and start a web server first."
    exit 1
fi

# Update .env file
echo ""
echo "Updating .env file..."
cd /var/www/leveler
if [ -f .env ]; then
    # Update APP_URL
    sed -i 's|APP_URL=.*|APP_URL=https://levelercc.com|g' .env
    sed -i 's|APP_ENV=.*|APP_ENV=production|g' .env
    sed -i 's|APP_DEBUG=.*|APP_DEBUG=false|g' .env
    
    # Clear and cache config
    php artisan config:clear
    php artisan config:cache
    
    echo ".env file updated!"
else
    echo "WARNING: .env file not found. Please create it manually."
fi

echo ""
echo "========================================="
echo "Domain setup complete!"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Point your domain DNS to: 75.119.139.18"
echo "2. Wait for DNS propagation (5 minutes to 48 hours)"
echo "3. Test: http://levelercc.com"
echo "4. Set up SSL: sudo certbot --apache -d levelercc.com -d www.levelercc.com"
echo "   (or: sudo certbot --nginx -d levelercc.com -d www.levelercc.com)"
echo ""

