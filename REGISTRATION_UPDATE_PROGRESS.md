# Registration System Update - Progress Report

## ✅ Completed

### 1. Database Structure
- ✅ Migration created: `2025_01_31_000002_add_course_status_and_user_type.php`
  - Added `course_status` enum to `trainee_course_access` table
  - Added `whatsapp_link` and `activated_at` to `trainee_course_access`
  - Added `user_type`, `state_code`, `whatsapp_number`, `nysc_start_date` to `trainees` table
  - Added `user_type` to `users` table

### 2. Pricing Service
- ✅ Created `app/Services/CoursePricingService.php`
  - NYSC pricing (Courses 1-9 with prices, installments, durations)
  - Working-Class pricing (9 courses with individual prices)
  - Discount calculation (5%, 15%, 25% based on course count)
  - Installment eligibility logic

### 3. Registration Flow
- ✅ Category selection page (`trainee/auth/category.blade.php`)
- ✅ NYSC registration form (`trainee/auth/register-nysc.blade.php`)
  - Full Name, State Code, WhatsApp Number
  - Course selection (1-9) with pricing display
  - Dynamic pricing summary
- ✅ Working-Class registration form (`trainee/auth/register-working-class.blade.php`)
  - Full Name, Email, WhatsApp Number, Password
  - Course selection with pricing
  - Dynamic discount calculation
  - Installment eligibility display

### 4. Controller Updates
- ✅ Updated `TraineeRegisterController.php`
  - `showCategorySelection()` - Shows category selection
  - `selectCategory()` - Handles category selection
  - `showRegistrationForm()` - Shows appropriate form based on category
  - `registerNysc()` - Handles NYSC registration
  - `registerWorkingClass()` - Handles Working-Class registration

### 5. Model Updates
- ✅ Updated `Trainee` model fillable fields
- ✅ Updated `User` model fillable fields
- ✅ Updated `accessibleCourses()` relationship to include new pivot fields

### 6. Routes
- ✅ Updated routes for new registration flow

## 🚧 Still To Do

### 1. Payment Controller Updates
- [ ] Update `PaymentController` to handle course selections from registration
- [ ] Create trainee_course_access records with `course_status = 'inactive'` during registration
- [ ] Update payment processing to set `course_status = 'active_training_pending'` when paid
- [ ] Handle installment payments for both NYSC and Working-Class

### 2. Dashboard Updates
- [ ] Update dashboard to show:
  - Active Courses (Training Pending)
  - Active Courses (Training Completed)
  - Inactive Courses
  - Amount Paid
  - Outstanding Balance
  - Countdown timer (365 days for NYSC)
  - Option to Add More Courses
  - Forfeit Courses button

### 3. Course Status Management
- [ ] Add methods to Trainee model:
  - `getActiveCoursesPending()`
  - `getActiveCoursesCompleted()`
  - `getInactiveCourses()`
  - `getTotalPaid()`
  - `getOutstandingBalance()`
  - `getNyscDaysRemaining()`

### 4. Forfeit Functionality
- [ ] Add forfeit route and controller method
- [ ] Create forfeit confirmation view
- [ ] Update trainee_course_access to remove forfeited courses
- [ ] Recalculate totals and discounts
- [ ] Send notification to admin for WhatsApp group removal

### 5. Admin Course Activation
- [ ] Add admin route for course activation
- [ ] Create admin view to see courses pending activation
- [ ] Add "Activate Course" button in admin panel
- [ ] Update course_status to 'active_training_completed'
- [ ] Set activated_at timestamp
- [ ] Show WhatsApp link after activation

### 6. Assessment Access Control
- [ ] Update `AssessmentController` to check course_status
- [ ] Only allow assessment if `course_status = 'active_training_completed'`
- [ ] Show message if training is still pending
- [ ] Hide assessment button for inactive courses

### 7. WhatsApp Link Visibility
- [ ] Update course views to show WhatsApp link only when:
  - `course_status = 'active_training_completed'`
  - `whatsapp_link` is not null
- [ ] Add WhatsApp link field in admin course activation

### 8. Add More Courses
- [ ] Add "Add More Courses" button on dashboard
- [ ] Create course selection page (similar to registration)
- [ ] Update pricing based on existing courses
- [ ] Add new courses with `course_status = 'inactive'`
- [ ] Recalculate discounts for Working-Class

### 9. Payment Integration
- [ ] Update PayVibe payment flow to:
  - Handle multiple course selections
  - Set course_status appropriately
  - Calculate correct totals with discounts
  - Handle installment payments

### 10. Course Mapping
- [ ] Map NYSC course numbers (1-9) to actual Course models
- [ ] Map Working-Class course titles to actual Course models
- [ ] Create a mapping service or configuration

## 📝 Notes

1. **Course Mapping**: Currently, the registration forms use course numbers (NYSC) or course titles (Working-Class), but these need to be mapped to actual `Course` model records in the database.

2. **Payment Flow**: The payment controller needs significant updates to handle:
   - Course selections from registration
   - Different pricing for NYSC vs Working-Class
   - Discount calculations
   - Installment plans

3. **Dashboard**: The dashboard needs a complete redesign to show:
   - Course statuses clearly
   - Payment progress
   - NYSC countdown timer
   - Active/Inactive course separation

4. **Admin Panel**: New admin features needed:
   - View courses pending activation
   - Activate courses after training completion
   - Add WhatsApp links
   - View forfeited courses

## 🔄 Next Steps

1. **Immediate**: Test registration flow with category selection
2. **Priority 1**: Update payment controller to handle course selections
3. **Priority 2**: Update dashboard with new course status display
4. **Priority 3**: Add admin course activation functionality
5. **Priority 4**: Implement forfeit functionality
6. **Priority 5**: Update assessment access control

## 🧪 Testing Checklist

- [ ] Category selection works
- [ ] NYSC registration creates user and trainee
- [ ] Working-Class registration creates user and trainee
- [ ] Course selections are stored in session
- [ ] Pricing calculations are correct
- [ ] Discounts apply correctly for Working-Class
- [ ] Installment eligibility is correct

