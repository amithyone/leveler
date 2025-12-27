<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TraineeController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\QuestionPoolController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TrainedController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ManualPaymentController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Trainee\TraineeAuthController;
use App\Http\Controllers\Trainee\TraineeRegisterController;
use App\Http\Controllers\Trainee\DashboardController as TraineeDashboardController;
use App\Http\Controllers\Trainee\CourseController as TraineeCourseController;
use App\Http\Controllers\Trainee\AssessmentController;
use App\Http\Controllers\Trainee\CertificateController;
use App\Http\Controllers\Trainee\PaymentController as TraineePaymentController;

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']); // Alias for /home redirects
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/partners', [HomeController::class, 'partners'])->name('partners');
Route::get('/tips-updates', [HomeController::class, 'tipsUpdates'])->name('tips-updates');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/faqs', [HomeController::class, 'faqs'])->name('faqs');
Route::get('/careers', [HomeController::class, 'careers'])->name('careers');
Route::get('/courses', [HomeController::class, 'courses'])->name('courses');
Route::get('/courses/{id}', [HomeController::class, 'courseDetails'])->name('course.details');

// Blog Routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');

Route::get('/e-learning', [HomeController::class, 'eLearning'])->name('e-learning');
Route::get('/register', [HomeController::class, 'register'])->name('register');
Route::get('/news', [HomeController::class, 'news'])->name('news');
Route::get('/terms-of-use', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/legal', [HomeController::class, 'legal'])->name('legal');
Route::get('/trainee/login', [TraineeAuthController::class, 'showLoginForm'])->name('trainee.login');
Route::post('/trainee/login', [TraineeAuthController::class, 'login']);
Route::get('/trainee/register', [TraineeRegisterController::class, 'showCategorySelection'])->name('trainee.register');
Route::get('/trainee/register/category', function() {
    return redirect()->route('trainee.register');
})->name('trainee.register.category.get');
Route::post('/trainee/register/category', [TraineeRegisterController::class, 'selectCategory'])->name('trainee.register.category');
Route::get('/trainee/register/form', [TraineeRegisterController::class, 'showRegistrationForm'])->name('trainee.register.form');
Route::post('/trainee/register/nysc', [TraineeRegisterController::class, 'registerNysc'])->name('trainee.register.nysc');
Route::post('/trainee/register/working-class', [TraineeRegisterController::class, 'registerWorkingClass'])->name('trainee.register.working-class');
Route::post('/trainee/logout', [TraineeAuthController::class, 'logout'])->name('trainee.logout');
Route::get('/page/{slug}', [HomeController::class, 'showPage'])->name('page');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Admin Routes - Protected with authentication and admin role check
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Trainees
    Route::get('/trainees', [TraineeController::class, 'index'])->name('trainees.index');
    Route::get('/trainees/view-profile', [TraineeController::class, 'viewProfile'])->name('trainees.view-profile');
    Route::get('/trainees/add', [TraineeController::class, 'create'])->name('trainees.create');
    Route::post('/trainees', [TraineeController::class, 'store'])->name('trainees.store');
    Route::get('/trainees/manage', [TraineeController::class, 'manage'])->name('trainees.manage');
    Route::post('/trainees/manage/bulk-action', [TraineeController::class, 'bulkAction'])->name('trainees.bulk-action');
    Route::get('/trainees/activate', [TraineeController::class, 'activate'])->name('trainees.activate');
    Route::post('/trainees/activate', [TraineeController::class, 'activate'])->name('trainees.activate.post');
    Route::post('/trainees/deactivate', [TraineeController::class, 'deactivate'])->name('trainees.deactivate');
    Route::get('/trainees/stop-impersonating', [TraineeController::class, 'stopImpersonating'])->name('trainees.stop-impersonating');
    Route::get('/trainees/{id}', [TraineeController::class, 'show'])->name('trainees.show');
    Route::get('/trainees/{id}/edit', [TraineeController::class, 'edit'])->name('trainees.edit');
    Route::put('/trainees/{id}', [TraineeController::class, 'update'])->name('trainees.update');
    Route::get('/trainees/{id}/view-as', [TraineeController::class, 'viewAs'])->name('trainees.view-as');
    
    // Schedules
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{id}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    
    // Question Pool
    Route::get('/question-pool', [QuestionPoolController::class, 'index'])->name('question-pool.index');
    Route::get('/question-pool/course/{courseId}', [QuestionPoolController::class, 'showCourseQuestions'])->name('question-pool.course');
    Route::get('/question-pool/{id}/edit', [QuestionPoolController::class, 'edit'])->name('question-pool.edit');
    Route::put('/question-pool/{id}', [QuestionPoolController::class, 'update'])->name('question-pool.update');
    Route::delete('/question-pool/{id}', [QuestionPoolController::class, 'destroy'])->name('question-pool.destroy');
    
    // Admin Users
    Route::get('/admin-users', [AdminUserController::class, 'index'])->name('admin-users.index');
    Route::get('/admin-users/view', [AdminUserController::class, 'view'])->name('admin-users.view');
    
    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/view', [CourseController::class, 'view'])->name('courses.view');
    Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->name('courses.update');
    
    // Results
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{id}', [ResultController::class, 'show'])->name('results.show');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Trained
    Route::get('/trained', [TrainedController::class, 'index'])->name('trained.index');
    
    // Pages Management
    Route::resource('pages', PageController::class);
    
    // Partners Management
    Route::resource('partners', PartnerController::class)->except(['show']);
    
    // Blog Management
    Route::resource('blog', AdminBlogController::class);
    Route::get('/blog/categories/manage', [AdminBlogController::class, 'categories'])->name('blog.categories');
    Route::post('/blog/categories', [AdminBlogController::class, 'storeCategory'])->name('blog.categories.store');
    Route::put('/blog/categories/{id}', [AdminBlogController::class, 'updateCategory'])->name('blog.categories.update');
    Route::delete('/blog/categories/{id}', [AdminBlogController::class, 'deleteCategory'])->name('blog.categories.destroy');
    Route::get('/blog/tags/manage', [AdminBlogController::class, 'tags'])->name('blog.tags');
    Route::post('/blog/tags', [AdminBlogController::class, 'storeTag'])->name('blog.tags.store');
    Route::delete('/blog/tags/{id}', [AdminBlogController::class, 'deleteTag'])->name('blog.tags.destroy');
    
    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{id}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    
    // Manual Payment Settings
    Route::resource('manual-payments', ManualPaymentController::class)->except(['show']);
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{id}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});

// Trainee Routes
Route::prefix('trainee')->name('trainee.')->group(function () {
    // Authentication
    Route::middleware('guest')->group(function () {
        Route::get('/login', [TraineeAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [TraineeAuthController::class, 'login']);
    });

    Route::post('/logout', [TraineeAuthController::class, 'logout'])->middleware('auth')->name('logout');

    // Protected Trainee Routes - Users can access, they become trainees when they enroll
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [TraineeDashboardController::class, 'index'])->name('dashboard');
        
        // Courses
        Route::get('/courses', [TraineeCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{id}', [TraineeCourseController::class, 'show'])->name('courses.show');
        
        // Assessments
        Route::get('/assessment/{courseId}/start', [AssessmentController::class, 'start'])->name('assessment.start');
        Route::post('/assessment/{courseId}/submit', [AssessmentController::class, 'submit'])->name('assessment.submit');
        Route::get('/assessment/result/{resultId}', [AssessmentController::class, 'result'])->name('assessment.result');
        
        // Certificates
        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{resultId}/view', [CertificateController::class, 'view'])->name('certificates.view');
        Route::get('/certificates/{resultId}/download', [CertificateController::class, 'download'])->name('certificates.download');
        
        // Payments
        Route::get('/payments', [TraineePaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [TraineePaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [TraineePaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{paymentId}/check-status', [TraineePaymentController::class, 'checkStatus'])->name('payments.check-status');
    });
});
