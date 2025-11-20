# Domain Setup Guide - Leveler Application

## Server Information
- **Server IP**: 75.119.139.18
- **Application Path**: /var/www/leveler/public
- **Domain**: yourdomain.com (replace with your actual domain)

## Step 1: Point Domain to Server

### Update DNS Records
In your domain registrar's DNS settings, add an A record:

```
Type: A
Name: @ (or leave blank)
Value: 75.119.139.18
TTL: 3600 (or default)
```

For www subdomain:
```
Type: A
Name: www
Value: 75.119.139.18
TTL: 3600
```

Wait for DNS propagation (can take a few minutes to 48 hours).

## Step 2: Configure Web Server

### For Apache

1. **Copy configuration file:**
   ```bash
   sudo cp /var/www/leveler/apache-leveler.conf /etc/apache2/sites-available/leveler.conf
   ```

2. **Edit the configuration:**
   ```bash
   sudo nano /etc/apache2/sites-available/leveler.conf
   ```
   Replace `yourdomain.com` with your actual domain name.

3. **Enable required modules:**
   ```bash
   sudo a2enmod rewrite
   sudo a2enmod proxy_fcgi
   sudo a2enmod setenvif
   ```

4. **Enable the site:**
   ```bash
   sudo a2ensite leveler.conf
   ```

5. **Disable default site (optional):**
   ```bash
   sudo a2dissite 000-default.conf
   ```

6. **Test configuration:**
   ```bash
   sudo apache2ctl configtest
   ```

7. **Reload Apache:**
   ```bash
   sudo systemctl reload apache2
   ```

### For Nginx

1. **Copy configuration file:**
   ```bash
   sudo cp /var/www/leveler/nginx-leveler.conf /etc/nginx/sites-available/leveler
   ```

2. **Edit the configuration:**
   ```bash
   sudo nano /etc/nginx/sites-available/leveler
   ```
   Replace `yourdomain.com` with your actual domain name.

3. **Create symbolic link:**
   ```bash
   sudo ln -s /etc/nginx/sites-available/leveler /etc/nginx/sites-enabled/
   ```

4. **Remove default site (optional):**
   ```bash
   sudo rm /etc/nginx/sites-enabled/default
   ```

5. **Test configuration:**
   ```bash
   sudo nginx -t
   ```

6. **Reload Nginx:**
   ```bash
   sudo systemctl reload nginx
   ```

## Step 3: Update Laravel .env File

Edit the `.env` file:
```bash
nano /var/www/leveler/.env
```

Update these values:
```env
APP_URL=https://yourdomain.com
APP_ENV=production
APP_DEBUG=false
```

Then clear and recache:
```bash
cd /var/www/leveler
php artisan config:clear
php artisan config:cache
```

## Step 4: Set Up SSL Certificate (Recommended)

### Using Let's Encrypt (Free SSL)

1. **Install Certbot:**
   ```bash
   # For Apache
   sudo apt install certbot python3-certbot-apache
   
   # For Nginx
   sudo apt install certbot python3-certbot-nginx
   ```

2. **Obtain SSL Certificate:**
   ```bash
   # For Apache
   sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
   
   # For Nginx
   sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
   ```

3. **Auto-renewal (already set up by certbot):**
   ```bash
   sudo certbot renew --dry-run
   ```

## Step 5: Verify Setup

1. **Check DNS propagation:**
   ```bash
   nslookup yourdomain.com
   ping yourdomain.com
   ```

2. **Test website:**
   - Visit: `http://yourdomain.com`
   - Should see the Leveler homepage

3. **Test admin panel:**
   - Visit: `http://yourdomain.com/admin/dashboard`
   - Should see login page

4. **Test trainee portal:**
   - Visit: `http://yourdomain.com/trainee/login`
   - Should see trainee login page

## Troubleshooting

### Domain not resolving?
- Check DNS records are correct
- Wait for DNS propagation (can take up to 48 hours)
- Use `nslookup` or `dig` to check DNS

### 403 Forbidden?
```bash
sudo chown -R www-data:www-data /var/www/leveler
sudo chmod -R 755 /var/www/leveler
sudo chmod -R 775 /var/www/leveler/storage
sudo chmod -R 775 /var/www/leveler/bootstrap/cache
```

### 500 Internal Server Error?
- Check Laravel logs: `tail -f /var/www/leveler/storage/logs/laravel.log`
- Check web server error logs
- Verify .env file is configured correctly
- Run: `php artisan config:clear && php artisan cache:clear`

### Permission Issues?
```bash
sudo chown -R www-data:www-data /var/www/leveler
sudo find /var/www/leveler -type f -exec chmod 644 {} \;
sudo find /var/www/leveler -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/leveler/storage
sudo chmod -R 775 /var/www/leveler/bootstrap/cache
```

## Quick Setup Commands

**For Apache:**
```bash
sudo cp /var/www/leveler/apache-leveler.conf /etc/apache2/sites-available/leveler.conf
sudo nano /etc/apache2/sites-available/leveler.conf  # Edit domain name
sudo a2enmod rewrite proxy_fcgi setenvif
sudo a2ensite leveler.conf
sudo a2dissite 000-default.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

**For Nginx:**
```bash
sudo cp /var/www/leveler/nginx-leveler.conf /etc/nginx/sites-available/leveler
sudo nano /etc/nginx/sites-available/leveler  # Edit domain name
sudo ln -s /etc/nginx/sites-available/leveler /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

## After Domain Setup

1. Update APP_URL in .env
2. Clear Laravel caches
3. Test all pages
4. Set up SSL certificate
5. Configure firewall if needed

