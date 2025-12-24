#!/bin/bash

# Fix duplicate route issue on server
# Run: cd /var/www/leveler && bash fix-routes-on-server.sh

echo "🔧 Fixing duplicate route issue..."
echo ""

cd /var/www/leveler || exit

# Pull latest code first
echo "📥 Pulling latest code..."
git pull origin main

# Backup routes file
cp routes/web.php routes/web.php.backup

# Remove any GET route for deactivate (keep only POST)
echo "🔍 Checking for duplicate routes..."
if grep -q "Route::get('/trainees/deactivate'" routes/web.php; then
    echo "⚠️  Found GET route for deactivate, removing it..."
    # Remove GET route line
    sed -i "/Route::get('\/trainees\/deactivate'/d" routes/web.php
    echo "✅ Removed GET route"
else
    echo "✅ No GET route found (already fixed)"
fi

# Verify only POST route exists
if grep -c "trainees.deactivate" routes/web.php | grep -q "^1$"; then
    echo "✅ Only one deactivate route found (correct)"
else
    echo "⚠️  Warning: Multiple deactivate routes may still exist"
fi

# Set cache to file temporarily
export CACHE_STORE=file

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
echo "🔨 Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ Fix complete!"
echo ""
echo "If you still see errors, check routes/web.php manually"
echo "Backup saved at: routes/web.php.backup"

