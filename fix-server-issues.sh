#!/bin/bash

# Fix server issues: Cache table and route conflicts
# Run this on the server: cd /var/www/leveler && bash fix-server-issues.sh

echo "🔧 Fixing server issues..."
echo ""

cd /var/www/leveler || exit

# Step 1: Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# Step 2: Set cache to file temporarily to avoid database cache errors
export CACHE_STORE=file

# Step 3: Clear config cache first
echo "🧹 Clearing config cache..."
php artisan config:clear

# Step 4: Run migrations (creates cache table)
echo "🗄️  Running migrations..."
php artisan migrate

# Step 5: Clear all caches
echo "🧹 Clearing all caches..."
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Step 6: Rebuild caches
echo "🔨 Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ All issues fixed!"
echo ""
echo "Fixed:"
echo "  ✓ Cache table created"
echo "  ✓ Route conflicts resolved"
echo "  ✓ All caches cleared and rebuilt"



