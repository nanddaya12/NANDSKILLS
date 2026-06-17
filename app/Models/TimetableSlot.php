<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class TimetableSlot extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'label',
        'start_time',
        'end_time',
        'sort_order',
        'is_break',
    ];

    protected $casts = [
        'is_break' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class, 'slot_id');
    }
}
