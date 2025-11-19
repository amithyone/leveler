<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPool extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'question',
        'type',
        'options',
        'correct_answer',
        'points',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

