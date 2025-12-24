#!/bin/bash

# Run Course Details Migration on Server
# SSH into your server and run this script

echo "🔄 Running course details migration..."
echo ""

cd /var/www/leveler || exit

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# Run migration
echo "🗄️  Running migration..."
php artisan migrate

# Clear caches
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
echo "✅ Migration complete!"
echo ""
echo "The following fields have been added to courses table:"
echo "  - overview"
echo "  - objectives (JSON)"
echo "  - what_you_will_learn (JSON)"
echo "  - requirements (JSON)"
echo "  - who_is_this_for"
echo "  - level"
echo "  - language"
echo "  - instructor"
echo "  - image"
echo "  - curriculum (JSON)"
echo "  - rating"
echo "  - total_reviews"
echo "  - total_enrollments"

