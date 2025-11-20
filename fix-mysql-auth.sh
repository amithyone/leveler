#!/bin/bash

# Fix MySQL Authentication Issue
# This script fixes the "Access denied for user 'root'@'localhost'" error

echo "========================================="
echo "Fixing MySQL Authentication"
echo "========================================="
echo ""

# Method 1: Try to login with sudo (if auth_socket is enabled)
echo "Attempting to access MySQL with sudo..."
sudo mysql -e "SELECT 1;" 2>/dev/null && echo "✓ MySQL accessible with sudo" || echo "✗ Cannot access MySQL with sudo"

# Method 2: Create/Update MySQL user with password authentication
echo ""
echo "Creating MySQL user with password authentication..."
echo "Please enter the MySQL root password when prompted, or press Enter if using sudo authentication:"

# Try to create a new user or update existing one
sudo mysql <<EOF
-- Create user if doesn't exist, or update existing
CREATE USER IF NOT EXISTS 'root'@'localhost' IDENTIFIED BY '';
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';
FLUSH PRIVILEGES;
SELECT 'MySQL user updated successfully' AS Status;
EOF

# Alternative: If the above doesn't work, try this
echo ""
echo "Alternative: Setting up MySQL user with password..."
sudo mysql <<EOF
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password_here';
FLUSH PRIVILEGES;
EOF

echo ""
echo "========================================="
echo "MySQL Authentication Fix Complete"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Update your .env file with the correct MySQL password"
echo "2. Test connection: php artisan migrate:status"
echo ""

