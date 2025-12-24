<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_id',
        'amount',
        'total_required',
        'course_access_count',
        'package_type',
        'is_installment',
        'installment_number',
        'payment_method',
        'transaction_reference',
        'payment_date',
        'status',
        'notes',
        'receipt_number',
        'manual_payment_details',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'total_required' => 'decimal:2',
        'is_installment' => 'boolean',
        'manual_payment_details' => 'array',
    ];

    /**
     * Get the trainee that owns the payment
     */
    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'Completed';
    }
}
