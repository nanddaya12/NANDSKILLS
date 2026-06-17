<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class Invoice extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $fillable = [
        'tenant_id',
        'student_fee_id',
        'invoice_number',
        'amount',
        'tax',
        'total',
        'status', // UNPAID, PAID, PARTIALLY_PAID, OVERDUE, VOID
        'due_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
