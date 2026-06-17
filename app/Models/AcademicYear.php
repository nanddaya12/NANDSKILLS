<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class AcademicYear extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $fillable = [
        'tenant_id',
        'name',
        'start_date',
        'end_date',
        'is_current',
        'status',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AcademicSession::class);
    }

    public function calendars(): HasMany
    {
        return $this->hasMany(AcademicCalendar::class);
    }
}
