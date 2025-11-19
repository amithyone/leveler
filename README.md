# Dexter & Heros Consulting Website

A Laravel-based website clone for Dexter & Heros Consulting with an admin dashboard.

## Features

### Frontend
- Homepage with hero slider
- About page
- Services page
- Partners page
- Tips & Updates page
- Contact page
- Responsive mobile-first design

### Admin Dashboard
- Dashboard overview
- **Trainee Management:**
  - View all trainee profiles in a table
  - Add new trainees with auto-generated username/password
  - Search trainees by surname
  - Activate/Deactivate trainees in bulk
  - Pagination support
- Schedules management
- Question Pool
- Admin Users management
- Courses management
- Results viewing
- Reports
- Trained trainees tracking

## Database Structure

The application includes the following database tables:
- `trainees` - Trainee information
- `admin_users` - Admin user accounts
- `courses` - Course information
- `schedules` - Training schedules
- `question_pools` - Exam questions
- `results` - Trainee exam results

## Installation

1. Install dependencies:
```bash
composer install
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Configure your database in `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dexterheros
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
```

6. Seed sample data (optional):
```bash
php artisan db:seed
```

7. Start the development server:
```bash
php artisan serve
```

## Access

- Frontend: http://localhost:8000
- Admin Dashboard: http://localhost:8000/admin/dashboard
- Trainee Profiles: http://localhost:8000/admin/trainees

## Project Structure

```
├── app/
│   └── Http/
│       └── Controllers/
│           ├── HomeController.php
│           └── Admin/
│               ├── DashboardController.php
│               ├── TraineeController.php
│               └── ...
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── admin.blade.php
│       │   └── frontend.blade.php
│       ├── admin/
│       └── frontend/
├── routes/
│   └── web.php
└── public/
    ├── css/
    │   ├── admin.css
    │   └── frontend.css
    └── js/
        ├── admin.js
        └── frontend.js
```

## Technologies Used

- Laravel 10
- Blade Templates
- CSS3
- JavaScript (Vanilla)
- Font Awesome Icons

