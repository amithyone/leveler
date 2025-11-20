# Server Fixes for levelercc.com

## Issue 1: Duplicate Route Name (FIXED)

**Error:** `Unable to prepare route [admin/trainees/activate] for serialization. Another route has already been assigned name [admin.trainees.activate].`

**Fix:** Updated `routes/web.php` to use different route names for GET and POST methods.

**Status:** ✅ Fixed in code, needs to be pulled on server

---

## Issue 2: MySQL Authentication Error

**Error:** `SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost'`

**Cause:** MySQL 8.0+ uses `auth_socket` plugin by default for root user, which requires system user authentication.

### Solution Options:

#### Option A: Use MySQL with sudo (Recommended for production)
```bash
# Update .env to use a different MySQL user
# Or access MySQL with sudo
sudo mysql -u root
```

#### Option B: Create a dedicated MySQL user
```bash
sudo mysql <<EOF
CREATE USER 'leveler_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON leveler.* TO 'leveler_user'@'localhost';
FLUSH PRIVILEGES;
EOF
```

Then update `.env`:
```env
DB_USERNAME=leveler_user
DB_PASSWORD=your_secure_password
```

#### Option C: Change root authentication method
```bash
sudo mysql <<EOF
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';
FLUSH PRIVILEGES;
EOF
```

Then update `.env`:
```env
DB_USERNAME=root
DB_PASSWORD=your_password
```

---

## Quick Fix Commands (Run on Server)

```bash
# 1. Pull latest code (includes route fix)
cd /var/www/leveler
git pull origin main

# 2. Fix MySQL authentication (choose one method above)

# 3. Clear route cache (after route fix)
php artisan route:clear
php artisan route:cache

# 4. Test database connection
php artisan migrate:status
```

---

## After Fixes

1. ✅ Route cache should work now
2. ✅ Database connection should work
3. ✅ All artisan commands should work

