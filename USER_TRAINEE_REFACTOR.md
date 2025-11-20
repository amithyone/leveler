# User/Trainee Refactoring Guide

## Overview
The system has been refactored so that:
- **Registration** creates a **User** account (not a Trainee)
- **Enrollment** (selecting a course and making payment) creates a **Trainee** record linked to the User
- Users log in with **email/password** (User credentials)
- Trainees are created automatically when users enroll in courses

## Key Changes

### 1. Database Migration
- Added `user_id` foreign key to `trainees` table
- Links each trainee to a user account

**To apply on server:**
```bash
php artisan migrate
```

### 2. Registration Flow
- **Before:** Registration created a Trainee directly
- **After:** Registration creates a User account
- Registration form now requires **email** instead of username
- User becomes a Trainee when they select a course and make payment

### 3. Authentication
- **Before:** Trainees logged in with username/password (trainee guard)
- **After:** Users log in with email/password (web guard)
- Login form updated to use email field
- All trainee routes now use `auth` middleware instead of `auth.trainee`

### 4. Trainee Creation
- Trainee record is created automatically when:
  - User selects a course package
  - User initiates payment
- Uses `TraineeHelper::getOrCreateTrainee()` method
- Trainee is linked to User via `user_id`

### 5. Controllers Updated
All trainee controllers now:
- Use `Auth::user()` instead of `Auth::guard('trainee')->user()`
- Use `TraineeHelper::getCurrentTrainee()` to get trainee record
- Redirect to payment page if user doesn't have trainee record yet

## Migration Steps (On Server)

1. **Pull latest code:**
   ```bash
   cd /var/www/leveler
   git pull origin main
   ```

2. **Run migration:**
   ```bash
   php artisan migrate
   ```

3. **Update existing trainees (if any):**
   If you have existing trainee records without user_id, you'll need to:
   - Create User accounts for them, OR
   - Link them to existing users, OR
   - They can re-register with email

4. **Clear caches:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   ```

## User Flow

### New User Registration:
1. User fills registration form (email, password, personal info)
2. User account is created
3. User is logged in
4. User sees dashboard with message to enroll in course

### Becoming a Trainee:
1. User clicks "Select Course" or "Make Payment"
2. User selects package (single course or 4 courses)
3. System creates Trainee record linked to User
4. User makes payment
5. Trainee status becomes "Active" when payment is completed
6. Trainee gains access to courses

## Important Notes

- **Existing Trainees:** If you have existing trainee records, they may not have user_id. You'll need to either:
  - Create User accounts for them and link them
  - Have them re-register with email
  - Run a data migration script

- **Backward Compatibility:** The system still supports old trainee login (username) during transition, but new registrations use email.

- **Admin "View As Trainee":** Updated to log in as the User associated with the trainee, not the trainee directly.

## Files Changed

### Models:
- `app/Models/User.php` - Added `trainee()` relationship
- `app/Models/Trainee.php` - Added `user_id` and `user()` relationship

### Controllers:
- `app/Http/Controllers/Trainee/TraineeRegisterController.php` - Creates User instead of Trainee
- `app/Http/Controllers/Trainee/TraineeAuthController.php` - Uses email login
- `app/Http/Controllers/Trainee/PaymentController.php` - Creates Trainee on enrollment
- `app/Http/Controllers/Trainee/DashboardController.php` - Uses User auth
- `app/Http/Controllers/Trainee/CourseController.php` - Uses User auth
- `app/Http/Controllers/Trainee/AssessmentController.php` - Uses User auth
- `app/Http/Controllers/Trainee/CertificateController.php` - Uses User auth
- `app/Http/Controllers/Admin/TraineeController.php` - Updated viewAs method

### Views:
- `resources/views/trainee/auth/login.blade.php` - Email field instead of username
- `resources/views/trainee/auth/register.blade.php` - Email field added

### Helpers:
- `app/Helpers/TraineeHelper.php` - New helper to get/create trainee from user

### Routes:
- `routes/web.php` - Trainee routes now use `auth` middleware

### Migrations:
- `database/migrations/2025_01_31_000001_add_user_id_to_trainees_table.php` - New migration

## Testing Checklist

- [ ] User can register with email
- [ ] User can login with email
- [ ] User sees enrollment message if not a trainee
- [ ] User becomes trainee when selecting course package
- [ ] Trainee can access courses after payment
- [ ] Admin can view as trainee
- [ ] Existing functionality still works

