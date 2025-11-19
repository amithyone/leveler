# Quick Deployment Guide - Server 75.119.139.18

## Step 1: Connect to Server
```bash
ssh root@75.119.139.18
```

## Step 2: Navigate to /var/www
```bash
cd /var/www
```

## Step 3: Pull from Git

**If leveler folder already exists:**
```bash
cd leveler
git pull origin main
```

**If leveler folder doesn't exist (first time):**
```bash
git clone https://github.com/amithyone/leveler.git
cd leveler
```

## Step 4: Install/Update Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

## Step 5: Set Permissions
```bash
chown -R www-data:www-data /var/www/leveler
chmod -R 755 /var/www/leveler
chmod -R 775 /var/www/leveler/storage
chmod -R 775 /var/www/leveler/bootstrap/cache
```

## Step 6: Setup Environment (if first time)
```bash
cp .env.example .env
php artisan key:generate
# Then edit .env with: nano .env
```

## Step 7: Create Storage Link
```bash
php artisan storage:link
```

## Step 8: Run Migrations (if needed)
```bash
php artisan migrate --force
```

## Step 9: Clear and Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## All-in-One Command (Copy and Paste)
```bash
cd /var/www && \
if [ -d "leveler" ]; then \
    echo "Updating existing repository..." && \
    cd leveler && \
    git pull origin main; \
else \
    echo "Cloning repository..." && \
    git clone https://github.com/amithyone/leveler.git && \
    cd leveler; \
fi && \
echo "Installing dependencies..." && \
composer install --no-dev --optimize-autoloader && \
echo "Setting permissions..." && \
chown -R www-data:www-data /var/www/leveler && \
chmod -R 755 /var/www/leveler && \
chmod -R 775 /var/www/leveler/storage && \
chmod -R 775 /var/www/leveler/bootstrap/cache && \
echo "Creating storage link..." && \
php artisan storage:link && \
echo "Clearing caches..." && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan route:clear && \
php artisan view:clear && \
echo "Caching for production..." && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
echo "=========================================" && \
echo "Deployment Complete!" && \
echo "Location: /var/www/leveler" && \
echo "========================================="
```

