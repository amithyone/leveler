#!/bin/bash
# Setup Roundcube webmail for mail.levelercc.com and mail.biggestlogs.com

echo "========================================="
echo "Setting up Roundcube Webmail"
echo "========================================="

# Configure Roundcube database
mysql -e "CREATE DATABASE IF NOT EXISTS roundcube;"
mysql -e "GRANT ALL PRIVILEGES ON roundcube.* TO 'roundcube'@'localhost' IDENTIFIED BY 'Roundcube2024!Secure';"
mysql -e "FLUSH PRIVILEGES;"

# Initialize Roundcube database if not already done
if [ ! -f /var/lib/roundcube/db_initialized ]; then
    echo "Initializing Roundcube database..."
    mysql roundcube < /usr/share/roundcube/SQL/mysql.initial.sql
    touch /var/lib/roundcube/db_initialized
fi

# Configure Roundcube
cat > /etc/roundcube/config.inc.php << 'EOF'
<?php
$config['db_dsnw'] = 'mysql://roundcube:Roundcube2024!Secure@localhost/roundcube';
$config['default_host'] = 'localhost';
$config['default_port'] = 143;
$config['imap_conn_options'] = array(
    'ssl' => array('verify_peer' => false, 'verify_peer_name' => false),
);
$config['smtp_server'] = 'localhost';
$config['smtp_port'] = 587;
$config['smtp_user'] = '%u';
$config['smtp_pass'] = '%p';
$config['smtp_conn_options'] = array(
    'ssl' => array('verify_peer' => false, 'verify_peer_name' => false),
);
$config['support_url'] = '';
$config['product_name'] = 'Leveler Mail';
$config['des_key'] = '$(openssl rand -base64 24)';
$config['plugins'] = array('archive', 'zipdownload');
$config['skin'] = 'elastic';
EOF

# Create Apache virtual hosts
cat > /etc/apache2/sites-available/mail.levelercc.com.conf << 'EOF'
<VirtualHost *:80>
    ServerName mail.levelercc.com
    ServerAlias www.mail.levelercc.com
    
    DocumentRoot /usr/share/roundcube
    
    <Directory /usr/share/roundcube>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mail.levelercc.com_error.log
    CustomLog ${APACHE_LOG_DIR}/mail.levelercc.com_access.log combined
</VirtualHost>
EOF

cat > /etc/apache2/sites-available/mail.biggestlogs.com.conf << 'EOF'
<VirtualHost *:80>
    ServerName mail.biggestlogs.com
    ServerAlias www.mail.biggestlogs.com
    
    DocumentRoot /usr/share/roundcube
    
    <Directory /usr/share/roundcube>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mail.biggestlogs.com_error.log
    CustomLog ${APACHE_LOG_DIR}/mail.biggestlogs.com_access.log combined
</VirtualHost>
EOF

# Enable sites
a2ensite mail.levelercc.com.conf
a2ensite mail.biggestlogs.com.conf
a2enmod rewrite
systemctl reload apache2

echo "========================================="
echo "Webmail Setup Complete!"
echo "========================================="
echo "Access webmail at:"
echo "  - http://mail.levelercc.com"
echo "  - http://mail.biggestlogs.com"
echo ""
echo "SMTP is enabled on ports:"
echo "  - Port 25 (SMTP)"
echo "  - Port 587 (Submission/SMTP)"
echo ""
echo "IMAP/POP3 ports:"
echo "  - Port 143 (IMAP)"
echo "  - Port 993 (IMAPS)"
echo "  - Port 110 (POP3)"
echo "  - Port 995 (POP3S)"
echo ""
echo "Next steps:"
echo "1. Set up DNS A records for mail.levelercc.com and mail.biggestlogs.com"
echo "2. Create email accounts using: ./create-email-account.sh email@levelercc.com password"
echo "3. Configure SSL certificates for HTTPS access"
