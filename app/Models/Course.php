<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'overview',
        'objectives',
        'what_you_will_learn',
        'requirements',
        'who_is_this_for',
        'level',
        'language',
        'instructor',
        'image',
        'curriculum',
        'code',
        'duration_hours',
        'status',
        'rating',
        'total_reviews',
        'total_enrollments',
    ];

    protected $casts = [
        'objectives' => 'array',
        'what_you_will_learn' => 'array',
        'requirements' => 'array',
        'curriculum' => 'array',
        'rating' => 'decimal:2',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function questionPools()
    {
        return $this->hasMany(QuestionPool::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get trainees who have access to this course
     */
    public function accessibleTrainees()
    {
        return $this->belongsToMany(Trainee::class, 'trainee_course_access', 'course_id', 'trainee_id')
            ->withPivot('payment_id', 'granted_at')
            ->withTimestamps();
    }
}

