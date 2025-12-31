#!/bin/bash

# Diagnose and fix MySQL service issues

set -e

echo "========================================="
echo "MySQL Diagnosis and Fix"
echo "========================================="

# Check MySQL status
echo "1. Checking MySQL service status..."
systemctl status mysql || systemctl status mariadb || true

# Check MySQL error logs
echo ""
echo "2. Checking MySQL error logs..."
if [ -f /var/log/mysql/error.log ]; then
    tail -50 /var/log/mysql/error.log
elif [ -f /var/log/mysqld.log ]; then
    tail -50 /var/log/mysqld.log
else
    journalctl -xeu mysql.service --no-pager | tail -50 || journalctl -xeu mariadb.service --no-pager | tail -50 || true
fi

# Check if MySQL socket exists
echo ""
echo "3. Checking MySQL socket..."
if [ -S /var/run/mysqld/mysqld.sock ]; then
    echo "✓ Socket exists at /var/run/mysqld/mysqld.sock"
elif [ -S /tmp/mysql.sock ]; then
    echo "✓ Socket exists at /tmp/mysql.sock"
else
    echo "✗ MySQL socket not found"
fi

# Check if MySQL process is running
echo ""
echo "4. Checking MySQL processes..."
ps aux | grep -i mysql | grep -v grep || echo "No MySQL processes found"

# Check MySQL data directory
echo ""
echo "5. Checking MySQL data directory..."
if [ -d /var/lib/mysql ]; then
    echo "✓ Data directory exists: /var/lib/mysql"
    ls -la /var/lib/mysql/ | head -10
else
    echo "✗ Data directory not found"
fi

# Try to initialize MySQL if needed
echo ""
echo "6. Attempting to fix MySQL..."

# Stop MySQL if it's trying to start
systemctl stop mysql 2>/dev/null || systemctl stop mariadb 2>/dev/null || true

# Check if MySQL is installed
if command -v mysql_install_db &> /dev/null; then
    echo "MySQL install tool found"
elif command -v mariadb-install-db &> /dev/null; then
    echo "MariaDB install tool found"
fi

# Check MySQL configuration
if [ -f /etc/mysql/mysql.conf.d/mysqld.cnf ]; then
    echo "MySQL config found: /etc/mysql/mysql.conf.d/mysqld.cnf"
    grep -E "^(datadir|socket|pid-file)" /etc/mysql/mysql.conf.d/mysqld.cnf || true
elif [ -f /etc/mysql/my.cnf ]; then
    echo "MySQL config found: /etc/mysql/my.cnf"
    grep -E "^(datadir|socket|pid-file)" /etc/mysql/my.cnf || true
fi

# Try to start MySQL in safe mode to check errors
echo ""
echo "7. Attempting to start MySQL..."
systemctl start mysql 2>&1 || {
    echo "MySQL failed to start. Trying alternative methods..."
    
    # Check if it's a permission issue
    if [ -d /var/lib/mysql ]; then
        chown -R mysql:mysql /var/lib/mysql 2>/dev/null || chown -R mysql:mysql /var/lib/mysql 2>/dev/null || true
        chmod 755 /var/lib/mysql 2>/dev/null || true
    fi
    
    # Try mysqld_safe
    if command -v mysqld_safe &> /dev/null; then
        echo "Trying mysqld_safe..."
        mysqld_safe --user=mysql &
        sleep 5
        if mysql -e "SELECT 1;" &>/dev/null; then
            echo "✓ MySQL started with mysqld_safe"
            pkill mysqld_safe
            systemctl start mysql || true
        fi
    fi
}

# Wait and test connection
sleep 3
if mysql -e "SELECT 1;" 2>/dev/null; then
    echo ""
    echo "========================================="
    echo "✓ MySQL is now working!"
    echo "========================================="
    
    # Now create the mailserver database
    echo "Creating mailserver database..."
    mysql -e "CREATE DATABASE IF NOT EXISTS mailserver;"
    
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

    mysql mailserver << 'EOF'
INSERT IGNORE INTO virtual_domains (name) VALUES ('levelercc.com'), ('biggestlogs.com');
EOF

    echo "✓ Mailserver database created successfully!"
else
    echo ""
    echo "========================================="
    echo "✗ MySQL is still not working"
    echo "========================================="
    echo ""
    echo "Please check the error messages above and try:"
    echo "1. systemctl status mysql"
    echo "2. journalctl -xeu mysql.service"
    echo "3. Check /var/log/mysql/error.log"
    echo ""
    echo "Common fixes:"
    echo "- Reinstall MySQL: apt-get remove --purge mysql-server mysql-client && apt-get install mysql-server"
    echo "- Fix permissions: chown -R mysql:mysql /var/lib/mysql"
    echo "- Remove lock files: rm /var/lib/mysql/*.pid"
fi

