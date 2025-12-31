#!/bin/bash
# Run this script AFTER you've added the DNS A record for webmin.levelercc.com
# This will get the SSL certificate and configure HTTPS

echo "========================================="
echo "Setting up SSL for webmin.levelercc.com"
echo "========================================="
echo ""
echo "Checking DNS..."
if dig +short webmin.levelercc.com | grep -q "75.119.139.18"; then
    echo "DNS record found! Proceeding with SSL setup..."
    
    # Get certificate
    certbot certonly --apache --non-interactive --agree-tos --email admin@levelercc.com -d webmin.levelercc.com
    
    # Update config with SSL
    cat > /etc/apache2/sites-available/webmin.levelercc.com.conf << 'EOF'
<VirtualHost *:80>
    ServerName webmin.levelercc.com
    Redirect permanent / https://webmin.levelercc.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName webmin.levelercc.com
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/webmin.levelercc.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/webmin.levelercc.com/privkey.pem
    Include /etc/letsencrypt/options-ssl-apache.conf
    
    ProxyPreserveHost On
    ProxyRequests Off
    ProxyPass / http://127.0.0.1:10000/
    ProxyPassReverse / http://127.0.0.1:10000/
    
    ErrorLog /var/log/apache2/webmin.levelercc.com_error.log
    CustomLog /var/log/apache2/webmin.levelercc.com_access.log combined
</VirtualHost>
EOF
    
    apache2ctl configtest && systemctl reload apache2
    
    echo ""
    echo "========================================="
    echo "SSL Setup Complete!"
    echo "========================================="
    echo "Access Webmin at: https://webmin.levelercc.com"
    echo "No browser warnings - fully trusted certificate!"
else
    echo "DNS record not found yet."
    echo "Please add A record: webmin.levelercc.com -> 75.119.139.18"
    echo "Then run this script again."
fi
