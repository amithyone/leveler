#!/bin/bash

# Complete Mail Server Setup and MySQL Fix
# Run this script directly on the server

set -e

echo "========================================="
echo "Complete Mail Server Setup"
echo "========================================="

# Navigate to project
cd /var/www/leveler || exit 1

# Pull latest
echo "Pulling latest changes..."
git pull origin main

# Fix MySQL first
echo ""
echo "========================================="
echo "Fixing MySQL..."
echo "========================================="

# Stop MySQL
systemctl stop mysql 2>/dev/null || true
pkill mysqld 2>/dev/null || true
sleep 2

# Fix permissions
if [ -d /var/lib/mysql ]; then
    chown -R mysql:mysql /var/lib/mysql
    chmod 755 /var/lib/mysql
fi

# Remove locks
rm -f /var/lib/mysql/*.pid 2>/dev/null || true
rm -f /var/run/mysqld/mysqld.pid 2>/dev/null || true

# Start MySQL
systemctl start mysql || {
    echo "Trying alternative start method..."
    mysqld_safe --user=mysql &
    sleep 5
}

# Wait for MySQL
echo "Waiting for MySQL..."
for i in {1..30}; do
    if mysql -e "SELECT 1;" &>/dev/null 2>&1; then
        echo "✓ MySQL is ready!"
        break
    fi
    sleep 1
done

# Create mailserver database
echo ""
echo "Creating mailserver database..."
mysql -e "CREATE DATABASE IF NOT EXISTS mailserver;" 2>/dev/null || {
    echo "MySQL not ready. Checking status..."
    systemctl status mysql || true
    journalctl -xeu mysql.service --no-pager | tail -20 || true
    exit 1
}

# Create tables
mysql mailserver << 'EOF'
CREATE TABLE IF NOT EXISTS virtual_domains (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS virtual_users (
  id INT NOT NULL AUTO_INCREMENT,
  domain_id INT NOT NULL,
  password VARCHAR(106) NOT NULL,
  email VARCHAR(120) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  FOREIGN KEY (domain_id) REFERENCES virtual_domains(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS virtual_aliases (
  id INT NOT NULL AUTO_INCREMENT,
  domain_id INT NOT NULL,
  source VARCHAR(100) NOT NULL,
  destination VARCHAR(100) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (domain_id) REFERENCES virtual_domains(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOF

# Insert domains
mysql mailserver << 'EOF'
INSERT IGNORE INTO virtual_domains (name) VALUES ('levelercc.com'), ('biggestlogs.com');
EOF

# Create mailuser
MAIL_PASS="MailServer2024!Secure"
mysql -e "CREATE USER IF NOT EXISTS 'mailuser'@'localhost' IDENTIFIED BY '$MAIL_PASS';" 2>/dev/null || true
mysql -e "GRANT SELECT ON mailserver.* TO 'mailuser'@'localhost';" 2>/dev/null || true
mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true

# Configure Postfix MySQL files
echo ""
echo "Configuring Postfix MySQL files..."
cat > /etc/postfix/mysql-virtual-mailbox-domains.cf << EOF
user = mailuser
password = $MAIL_PASS
hosts = 127.0.0.1
dbname = mailserver
query = SELECT 1 FROM virtual_domains WHERE name='%s'
EOF

cat > /etc/postfix/mysql-virtual-mailbox-maps.cf << EOF
user = mailuser
password = $MAIL_PASS
hosts = 127.0.0.1
dbname = mailserver
query = SELECT 1 FROM virtual_users WHERE email='%s'
EOF

cat > /etc/postfix/mysql-virtual-alias-maps.cf << EOF
user = mailuser
password = $MAIL_PASS
hosts = 127.0.0.1
dbname = mailserver
query = SELECT destination FROM virtual_aliases WHERE source='%s'
EOF

chmod 600 /etc/postfix/mysql-virtual-*.cf
chown root:postfix /etc/postfix/mysql-virtual-*.cf

# Configure Dovecot SQL
echo "Configuring Dovecot SQL..."
cat > /etc/dovecot/dovecot-sql.conf.ext << EOF
driver = mysql
connect = host=127.0.0.1 dbname=mailserver user=mailuser password=$MAIL_PASS
default_pass_scheme = SHA512-CRYPT
password_query = SELECT email as user, password FROM virtual_users WHERE email='%u';
EOF

chmod 600 /etc/dovecot/dovecot-sql.conf.ext
chown root:dovecot /etc/dovecot/dovecot-sql.conf.ext

# Restart services
echo ""
echo "Restarting mail services..."
systemctl restart postfix
systemctl restart dovecot
systemctl restart opendkim

# Test MySQL connection
echo ""
echo "Testing MySQL connection..."
if mysql -u mailuser -p"$MAIL_PASS" mailserver -e "SELECT COUNT(*) FROM virtual_domains;" &>/dev/null; then
    echo "✓ MySQL connection working!"
else
    echo "✗ MySQL connection test failed"
fi

# Display summary
echo ""
echo "========================================="
echo "Setup Complete!"
echo "========================================="
echo ""
echo "MySQL Status:"
systemctl is-active mysql && echo "✓ MySQL is running" || echo "✗ MySQL is not running"
echo ""
echo "Mail Services Status:"
systemctl is-active postfix && echo "✓ Postfix is running" || echo "✗ Postfix is not running"
systemctl is-active dovecot && echo "✓ Dovecot is running" || echo "✗ Dovecot is not running"
echo ""
echo "IMPORTANT: Change password '$MAIL_PASS' in:"
echo "  - /etc/postfix/mysql-virtual-*.cf"
echo "  - /etc/dovecot/dovecot-sql.conf.ext"
echo ""
echo "DKIM Keys are already generated. View them with:"
echo "  cat /etc/opendkim/keys/levelercc.com/default.txt"
echo "  cat /etc/opendkim/keys/biggestlogs.com/default.txt"
echo ""
echo "Create email accounts with:"
echo "  ./create-email-account.sh email@levelercc.com password"
echo ""

