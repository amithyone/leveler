#!/bin/bash

# Fix Git Ownership and Domain Redirect Issue
# Run this on your server

set -e

echo "========================================="
echo "Fixing Git Ownership and Domain Redirect"
echo "========================================="
echo ""

# Step 1: Fix Git ownership issue
echo "Step 1: Fixing Git ownership..."
cd /var/www/leveler

# Add safe directory for git
git config --global --add safe.directory /var/www/leveler

# Fix ownership
sudo chown -R root:root /var/www/leveler/.git
sudo chown -R www-data:www-data /var/www/leveler

echo "✓ Git ownership fixed"

# Step 2: Pull latest code
echo ""
echo "Step 2: Pulling latest code from GitHub..."
git pull origin main
echo "✓ Code pulled successfully"

# Step 3: Make scripts executable
echo ""
echo "Step 3: Making scripts executable..."
chmod +x fix-domain-redirect.sh 2>/dev/null || echo "fix-domain-redirect.sh not found, will create it"
chmod +x complete-domain-setup.sh 2>/dev/null || true
chmod +x setup-domain-levelercc.sh 2>/dev/null || true
echo "✓ Scripts made executable"

# Step 4: Run domain redirect fix
echo ""
echo "Step 4: Running domain redirect fix..."
if [ -f "fix-domain-redirect.sh" ]; then
    sudo ./fix-domain-redirect.sh
else
    echo "⚠ fix-domain-redirect.sh not found, running manual fix..."
    
    # Detect web server
    if systemctl is-active --quiet apache2; then
        echo "Apache detected. Configuring..."
        sudo cp apache-leveler.conf /etc/apache2/sites-available/leveler.conf
        sudo a2dissite 000-default.conf 2>/dev/null || true
        sudo a2ensite leveler.conf
        sudo a2enmod rewrite proxy_fcgi setenvif 2>/dev/null || true
        sudo apache2ctl configtest && sudo systemctl reload apache2
        echo "✓ Apache configured"
    elif systemctl is-active --quiet nginx; then
        echo "Nginx detected. Configuring..."
        sudo cp nginx-leveler.conf /etc/nginx/sites-available/leveler
        sudo rm -f /etc/nginx/sites-enabled/default
        sudo ln -sf /etc/nginx/sites-available/leveler /etc/nginx/sites-enabled/
        sudo nginx -t && sudo systemctl reload nginx
        echo "✓ Nginx configured"
    fi
fi

# Step 5: Set permissions
echo ""
echo "Step 5: Setting file permissions..."
sudo chown -R www-data:www-data /var/www/leveler
sudo chmod -R 755 /var/www/leveler
sudo chmod -R 775 /var/www/leveler/storage
sudo chmod -R 775 /var/www/leveler/bootstrap/cache
echo "✓ Permissions set"

# Step 6: Check DNS
echo ""
echo "Step 6: Checking DNS resolution..."
nslookup levelercc.com || echo "⚠ DNS lookup failed"
host levelercc.com || echo "⚠ Host command failed"

echo ""
echo "========================================="
echo "Setup Complete!"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Verify DNS: nslookup levelercc.com (should show 75.119.139.18)"
echo "2. Test site: curl -I http://levelercc.com"
echo "3. Check for biggestlogs redirects:"
if systemctl is-active --quiet apache2; then
    echo "   grep -r 'biggestlogs' /etc/apache2/sites-enabled/"
elif systemctl is-active --quiet nginx; then
    echo "   grep -r 'biggestlogs' /etc/nginx/sites-enabled/"
fi
echo ""

