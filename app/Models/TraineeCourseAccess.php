<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraineeCourseAccess extends Model
{
    use HasFactory;

    protected $table = 'trainee_course_access';

    protected $fillable = [
        'trainee_id',
        'course_id',
        'payment_id',
        'granted_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
