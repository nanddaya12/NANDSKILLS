<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class AcademicCalendar extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'academic_calendars';

    protected $fillable = [
        'tenant_id',
        'academic_year_id',
        'academic_session_id',
        'title',
        'event_date',
        'event_end_date',
        'event_type',
        'description',
        'is_holiday',
        'color',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_end_date' => 'date',
        'is_holiday' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
