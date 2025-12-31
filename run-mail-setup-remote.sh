#!/bin/bash

# Remote mail server setup script
# This script will be executed on the server after SSH connection

set -e

echo "========================================="
echo "Starting Mail Server Setup"
echo "========================================="

# Navigate to project directory
cd /var/www/leveler

# Pull latest changes
echo "Pulling latest changes from git..."
git pull origin main

# Make scripts executable
chmod +x setup-mail-server.sh
chmod +x create-email-account.sh

# Run the mail server setup
echo "Running mail server setup script..."
./setup-mail-server.sh

echo "========================================="
echo "Mail server setup completed!"
echo "========================================="

