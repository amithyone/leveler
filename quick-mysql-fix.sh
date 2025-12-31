#!/bin/bash

# Quick MySQL fix - tries common solutions

set -e

echo "========================================="
echo "Quick MySQL Fix"
echo "========================================="

# Stop MySQL
systemctl stop mysql 2>/dev/null || true
pkill mysqld 2>/dev/null || true
sleep 2

# Fix permissions
echo "Fixing permissions..."
if [ -d /var/lib/mysql ]; then
    chown -R mysql:mysql /var/lib/mysql
    chmod 755 /var/lib/mysql
fi

# Remove lock files
echo "Removing lock files..."
rm -f /var/lib/mysql/*.pid 2>/dev/null || true
rm -f /var/run/mysqld/mysqld.pid 2>/dev/null || true
rm -f /var/run/mysqld/mysqld.sock.lock 2>/dev/null || true

# Check if MySQL data directory is empty (needs initialization)
if [ ! -f /var/lib/mysql/ibdata1 ]; then
    echo "MySQL data directory appears empty. Initializing..."
    if command -v mysql_install_db &> /dev/null; then
        mysql_install_db --user=mysql --datadir=/var/lib/mysql
    elif command -v mariadb-install-db &> /dev/null; then
        mariadb-install-db --user=mysql --datadir=/var/lib/mysql
    else
        echo "MySQL install tool not found. Installing MySQL..."
        apt-get update
        apt-get install -y mysql-server
    fi
fi

# Start MySQL
echo "Starting MySQL..."
systemctl start mysql || {
    echo "Systemd start failed, trying direct start..."
    mysqld_safe --user=mysql --datadir=/var/lib/mysql &
    sleep 5
}

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
for i in {1..30}; do
    if mysql -e "SELECT 1;" &>/dev/null 2>&1; then
        echo "✓ MySQL is ready!"
        break
    fi
    echo "Waiting... ($i/30)"
    sleep 1
done

# Test connection
if mysql -e "SELECT 1;" 2>/dev/null; then
    echo ""
    echo "========================================="
    echo "✓ MySQL Fixed!"
    echo "========================================="
    
    # Create mailserver database
    echo "Creating mailserver database..."
    mysql -e "CREATE DATABASE IF NOT EXISTS mailserver;" || true
    
    # Create tables
    mysql mailserver << 'EOF' 2>/dev/null || true
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
    mysql mailserver << 'EOF' 2>/dev/null || true
INSERT IGNORE INTO virtual_domains (name) VALUES ('levelercc.com'), ('biggestlogs.com');
EOF

    # Create mailuser
    mysql -e "CREATE USER IF NOT EXISTS 'mailuser'@'localhost' IDENTIFIED BY 'MailServer2024!Secure';" 2>/dev/null || true
    mysql -e "GRANT SELECT ON mailserver.* TO 'mailuser'@'localhost';" 2>/dev/null || true
    mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true
    
    echo "✓ Mailserver database setup complete!"
    echo ""
    echo "You can now create email accounts using:"
    echo "  ./create-email-account.sh email@levelercc.com password"
else
    echo ""
    echo "========================================="
    echo "✗ MySQL still not working"
    echo "========================================="
    echo ""
    echo "Run diagnose script for detailed info:"
    echo "  ./diagnose-and-fix-mysql.sh"
fi

