#!/bin/bash

# Fix MySQL connection issue and continue mail server setup

set -e

echo "========================================="
echo "Fixing MySQL and Continuing Setup"
echo "========================================="

# Check if MySQL/MariaDB is installed
if ! command -v mysql &> /dev/null && ! command -v mariadb &> /dev/null; then
    echo "MySQL/MariaDB not found. Installing..."
    apt-get update
    apt-get install -y mariadb-server mariadb-client
fi

# Start MySQL service
echo "Starting MySQL service..."
systemctl start mysql || systemctl start mariadb
systemctl enable mysql || systemctl enable mariadb

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 5

# Check MySQL status
if systemctl is-active --quiet mysql || systemctl is-active --quiet mariadb; then
    echo "✓ MySQL is running"
else
    echo "✗ MySQL failed to start. Checking status..."
    systemctl status mysql || systemctl status mariadb
    exit 1
fi

# Test MySQL connection
echo "Testing MySQL connection..."
mysql -e "SELECT 1;" || {
    echo "MySQL connection failed. Trying to reset root password..."
    # Try to start MySQL in safe mode or reset
    systemctl stop mysql || systemctl stop mariadb
    mysqld_safe --skip-grant-tables &
    sleep 3
    mysql -e "FLUSH PRIVILEGES; ALTER USER 'root'@'localhost' IDENTIFIED BY '';"
    pkill mysqld
    systemctl start mysql || systemctl start mariadb
    sleep 5
}

# Now continue with mail server setup
echo "Continuing with mail server setup..."
cd /var/www/leveler

# Create virtual user database
echo "Setting up virtual mail database..."
mysql -e "CREATE DATABASE IF NOT EXISTS mailserver;" || {
    echo "Failed to create database. Trying with root access..."
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS mailserver;"
}

mysql -e "CREATE USER IF NOT EXISTS 'mailuser'@'localhost' IDENTIFIED BY 'MailServer2024!Secure';" || {
    mysql -u root -e "CREATE USER IF NOT EXISTS 'mailuser'@'localhost' IDENTIFIED BY 'MailServer2024!Secure';" 2>/dev/null || true
}

mysql -e "GRANT SELECT ON mailserver.* TO 'mailuser'@'localhost';" || {
    mysql -u root -e "GRANT SELECT ON mailserver.* TO 'mailuser'@'localhost';"
}

mysql -e "FLUSH PRIVILEGES;"

# Create mail tables
echo "Creating mail tables..."
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
echo "Inserting domains..."
mysql mailserver << 'EOF'
INSERT IGNORE INTO virtual_domains (name) VALUES ('levelercc.com'), ('biggestlogs.com');
EOF

# Set password variable
MAIL_DB_PASSWORD="MailServer2024!Secure"

# Create Postfix MySQL config files
echo "Creating Postfix MySQL config files..."
cat > /etc/postfix/mysql-virtual-mailbox-domains.cf << EOF
user = mailuser
password = $MAIL_DB_PASSWORD
hosts = 127.0.0.1
dbname = mailserver
query = SELECT 1 FROM virtual_domains WHERE name='%s'
EOF

cat > /etc/postfix/mysql-virtual-mailbox-maps.cf << EOF
user = mailuser
password = $MAIL_DB_PASSWORD
hosts = 127.0.0.1
dbname = mailserver
query = SELECT 1 FROM virtual_users WHERE email='%s'
EOF

cat > /etc/postfix/mysql-virtual-alias-maps.cf << EOF
user = mailuser
password = $MAIL_DB_PASSWORD
hosts = 127.0.0.1
dbname = mailserver
query = SELECT destination FROM virtual_aliases WHERE source='%s'
EOF

# Set permissions
chmod 600 /etc/postfix/mysql-virtual-*.cf
chown root:postfix /etc/postfix/mysql-virtual-*.cf

# Update Dovecot SQL config
echo "Updating Dovecot SQL config..."
cat > /etc/dovecot/dovecot-sql.conf.ext << EOF
driver = mysql
connect = host=127.0.0.1 dbname=mailserver user=mailuser password=$MAIL_DB_PASSWORD
default_pass_scheme = SHA512-CRYPT
password_query = SELECT email as user, password FROM virtual_users WHERE email='%u';
EOF

chmod 600 /etc/dovecot/dovecot-sql.conf.ext
chown root:dovecot /etc/dovecot/dovecot-sql.conf.ext

echo ""
echo "========================================="
echo "MySQL Fixed and Database Created!"
echo "========================================="
echo ""
echo "IMPORTANT: Change the password '$MAIL_DB_PASSWORD' in:"
echo "  - /etc/postfix/mysql-virtual-*.cf"
echo "  - /etc/dovecot/dovecot-sql.conf.ext"
echo ""
echo "You can now continue with the rest of the mail server setup."
echo "Run the remaining parts of setup-mail-server.sh manually or"
echo "continue with Postfix/Dovecot configuration."
echo ""

