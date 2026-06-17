<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class AcademicWarning extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'academic_warnings';

    protected $fillable = [
        'tenant_id',
        'student_user_id',
        'issued_by',
        'warning_type',
        'cgpa_at_warning',
        'attendance_pct_at_warning',
        'description',
        'is_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'cgpa_at_warning' => 'decimal:2',
        'attendance_pct_at_warning' => 'decimal:2',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
