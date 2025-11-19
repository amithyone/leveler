# Creating an Admin User

## Option 1: Using Artisan Command (Recommended)

Once your database is set up and migrations are run, you can create an admin user using:

```bash
php artisan user:create-admin
```

This will create an admin user with:
- **Email**: admin@leveler.com
- **Password**: password
- **Name**: Admin User

### Custom Options

You can customize the admin user:

```bash
php artisan user:create-admin --name="John Doe" --email="john@leveler.com" --password="your-secure-password"
```

## Option 2: Using Database Seeder

Run the seeder:

```bash
php artisan db:seed --class=AdminUserSeeder
```

## Option 3: Manual Registration

1. Visit: http://127.0.0.1:8000/register
2. Register an account
3. Update the user's role to 'admin' in the database:

```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```

## Default Admin Credentials

After running the seeder or command:
- **Email**: admin@leveler.com
- **Password**: password

⚠️ **Important**: Change the password immediately after first login!

## Steps to Set Up Database First

1. Start MySQL in XAMPP Control Panel
2. Create the database:
   ```sql
   CREATE DATABASE leveler;
   ```
3. Run migrations:
   ```bash
   php artisan migrate
   ```
4. Create admin user:
   ```bash
   php artisan user:create-admin
   ```

