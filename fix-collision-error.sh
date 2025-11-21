#!/bin/bash

# Fix Collision Service Provider Error
# This script fixes the issue where Collision package is not found in production

echo "🔧 Fixing Collision Service Provider Error..."
echo ""

cd /var/www/leveler || exit

# Step 1: Pull latest code (includes composer.json fix)
echo "📥 Pulling latest code..."
git pull origin main

# Step 2: Regenerate autoload files
echo "🔄 Regenerating autoload files..."
composer dump-autoload --no-dev --optimize

# Step 3: Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Step 4: Rebuild caches
echo "🔨 Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ Fix complete! The Collision error should be resolved."
echo ""
echo "If you still see errors, try:"
echo "  - Check that composer.json has 'nunomaduro/collision' in dont-discover"
echo "  - Run: composer dump-autoload --no-dev --optimize"
echo "  - Run: php artisan config:clear && php artisan config:cache"

