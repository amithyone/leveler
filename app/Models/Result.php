<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_id',
        'course_id',
        'score',
        'total_questions',
        'questions_asked',
        'file_path',
        'file_link',
        'percentage',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'percentage' => 'decimal:2',
        'questions_asked' => 'array',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

