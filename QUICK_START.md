# Quick Start Guide - Create Admin User

## Step 1: Start MySQL
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL

## Step 2: Create Database
Open phpMyAdmin (http://localhost/phpmyadmin) and run:
```sql
CREATE DATABASE leveler;
```

## Step 3: Run Migrations
```bash
php artisan migrate
```
This will automatically create the admin user!

## Step 4: Login
Visit: http://127.0.0.1:8000/login

**Default Admin Credentials:**
- Email: `admin@leveler.com`
- Password: `password`

---

## Alternative: Create Admin Manually

If migrations already ran, you can create admin user using:

### Option 1: Artisan Command
```bash
php artisan user:create-admin
```

### Option 2: PHP Script
```bash
php create-admin.php
```

### Option 3: Tinker
```bash
php artisan tinker
```
Then run:
```php
User::create([
    'name' => 'Admin User',
    'email' => 'admin@leveler.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'email_verified_at' => now(),
]);
```

---

## After First Login
⚠️ **IMPORTANT**: Change the default password immediately!

