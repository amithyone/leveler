<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualPaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bank_name',
        'account_name',
        'account_number',
        'payment_instructions',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        // Check if order column exists before ordering by it
        $connection = $this->getConnection();
        $schema = $connection->getSchemaBuilder();
        
        if ($schema->hasColumn($this->getTable(), 'order')) {
            return $query->orderBy('order')->orderBy('name');
        }
        
        // Fallback to ordering by name only if order column doesn't exist
        return $query->orderBy('name');
    }
}
