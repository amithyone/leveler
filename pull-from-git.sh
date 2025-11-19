#!/bin/bash

# Simple script to pull code from GitHub to server
# Run this on your server: 75.119.139.18

echo "Pulling latest code from GitHub..."

# Navigate to /var/www
cd /var/www

# Check if leveler directory exists
if [ -d "leveler" ]; then
    echo "Leveler directory exists. Pulling latest changes..."
    cd leveler
    git pull origin main
    echo "Code updated successfully!"
else
    echo "Leveler directory not found. Cloning repository..."
    git clone https://github.com/amithyone/leveler.git
    cd leveler
    echo "Repository cloned successfully!"
fi

echo "Current directory: $(pwd)"
echo "Latest commit: $(git log -1 --oneline)"

