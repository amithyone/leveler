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
        'code',
        'duration_hours',
        'assessment_questions_count',
        'passing_score',
        'training_link',
        'whatsapp_link',
        'status',
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

