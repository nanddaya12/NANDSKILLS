<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StudentFee extends Model
{
    use HasUuids;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'discount_amount',
        'scholarship_amount',
        'net_amount',
        'status', // UNPAID, PARTIALLY_PAID, PAID
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'scholarship_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
