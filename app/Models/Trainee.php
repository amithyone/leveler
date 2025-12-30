<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Trainee extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'surname',
        'first_name',
        'middle_name',
        'gender',
        'username',
        'password',
        'phone_number',
        'whatsapp_number',
        'status',
        'available_courses',
        'total_paid',
        'total_required',
        'user_type',
        'state_code',
        'nysc_start_date',
        'package_type',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Get the password for authentication.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get trainee's enrolled courses (all active courses for now)
     */
    public function enrolledCourses()
    {
        return Course::where('status', 'Active')->get();
    }

    /**
     * Get trainee's results
     */
    public function myResults()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Check if trainee has passed a course
     */
    public function hasPassedCourse($courseId)
    {
        return $this->myResults()
            ->where('course_id', $courseId)
            ->where('status', 'passed')
            ->exists();
    }

    /**
     * Get trainee's certificate for a course
     */
    public function getCertificateForCourse($courseId)
    {
        $result = $this->myResults()
            ->where('course_id', $courseId)
            ->where('status', 'passed')
            ->first();
        
        return $result;
    }

    /**
     * Get the full name of the trainee
     */
    public function getFullNameAttribute()
    {
        $name = strtoupper($this->surname . ' ' . $this->first_name);
        if ($this->middle_name) {
            $name .= ' ' . strtoupper($this->middle_name);
        }
        return $name;
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute()
    {
        return $this->phone_number;
    }

    /**
     * Get all payments for this trainee
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if trainee has completed payment
     */
    public function hasCompletedPayment()
    {
        return $this->payments()->where('status', 'Completed')->exists();
    }

    /**
     * Get latest payment
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Get courses the trainee has access to
     */
    public function accessibleCourses()
    {
        return $this->belongsToMany(Course::class, 'trainee_course_access', 'trainee_id', 'course_id')
            ->withPivot('payment_id', 'granted_at', 'course_status', 'whatsapp_link', 'activated_at')
            ->withTimestamps();
    }

    /**
     * Check if trainee has access to a specific course
     */
    public function hasAccessToCourse($courseId)
    {
        // Admins have access to all courses for testing purposes
        if ($this->user && $this->user->isAdmin()) {
            return true;
        }
        
        return $this->accessibleCourses()->where('courses.id', $courseId)->exists();
    }

    /**
     * Grant access to courses
     */
    public function grantCourseAccess($courseIds, $paymentId = null)
    {
        foreach ($courseIds as $courseId) {
            $this->accessibleCourses()->syncWithoutDetaching([
                $courseId => [
                    'payment_id' => $paymentId,
                    'granted_at' => now()
                ]
            ]);
        }
        
        // Update available courses count
        $this->available_courses = $this->accessibleCourses()->count();
        $this->save();
    }

    /**
     * Get total amount paid by trainee for a specific package
     */
    public function getTotalPaid($packageType = null)
    {
        $query = $this->payments()->where('status', 'Completed');
        
        if ($packageType) {
            $query->where('package_type', $packageType);
        }
        
        return $query->sum('amount');
    }

    /**
     * Get current package type based on payments
     */
    public function getCurrentPackageType()
    {
        $latestPayment = $this->payments()
            ->where('status', 'Completed')
            ->whereNotNull('package_type')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return $latestPayment ? $latestPayment->package_type : null;
    }

    /**
     * Get total required for current package
     */
    public function getTotalRequiredForPackage()
    {
        $packageType = $this->getCurrentPackageType();
        
        if ($packageType === 'package') {
            return 22500;
        } elseif ($packageType === 'single') {
            return 10000;
        }
        
        // Fallback to stored total_required
        return $this->total_required > 0 ? $this->total_required : 0;
    }

    /**
     * Check if trainee has fully paid for their package
     */
    public function hasFullyPaid()
    {
        $totalRequired = $this->getTotalRequiredForPackage();
        
        if ($totalRequired <= 0) {
            return false; // No package selected
        }
        
        $packageType = $this->getCurrentPackageType();
        $totalPaid = $this->getTotalPaid($packageType);
        
        return $totalPaid >= $totalRequired;
    }

    /**
     * Get payment progress percentage
     */
    public function getPaymentProgress()
    {
        $totalRequired = $this->getTotalRequiredForPackage();
        
        if ($totalRequired <= 0) {
            return 0;
        }
        
        $packageType = $this->getCurrentPackageType();
        $totalPaid = $this->getTotalPaid($packageType);
        
        return min(100, ($totalPaid / $totalRequired) * 100);
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalance()
    {
        $totalRequired = $this->getTotalRequiredForPackage();
        
        if ($totalRequired <= 0) {
            return 0;
        }
        
        $packageType = $this->getCurrentPackageType();
        $totalPaid = $this->getTotalPaid($packageType);
        $remaining = $totalRequired - $totalPaid;
        
        return max(0, $remaining);
    }

    /**
     * Get the user associated with this trainee
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

