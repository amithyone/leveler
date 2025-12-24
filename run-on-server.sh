#!/bin/bash
# This script will be run on the server to fix all issues

cd /var/www/leveler || exit

echo "🔧 Fixing server issues..."
echo ""

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# Remove duplicate GET route for deactivate if it exists
if grep -q "Route::get('/trainees/deactivate'" routes/web.php; then
    echo "🔍 Removing duplicate GET route..."
    sed -i "/Route::get('\/trainees\/deactivate'/d" routes/web.php
    echo "✅ Removed duplicate route"
fi

# Set cache to file temporarily
export CACHE_STORE=file

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Rebuild caches
echo "🔨 Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ All fixes applied!"
echo "  ✓ Code updated"
echo "  ✓ Duplicate routes fixed"
echo "  ✓ Cache table created"
echo "  ✓ All caches cleared and rebuilt"



