<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class StudentCounselingCase extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'student_counseling_cases';

    protected $fillable = [
        'tenant_id',
        'student_user_id',
        'counselor_user_id',
        'case_type',
        'priority',
        'status',
        'summary',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_user_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CounselingSession::class, 'case_id');
    }

    public function interventionPlans(): HasMany
    {
        return $this->hasMany(InterventionPlan::class, 'case_id');
    }
}
