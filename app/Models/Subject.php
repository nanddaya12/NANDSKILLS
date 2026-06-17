<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class Subject extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $fillable = [
        'tenant_id',
        'department_id',
        'name',
        'code',
        'credit_hours',
        'type',
        'is_elective',
        'description',
        'status',
    ];

    protected $casts = [
        'credit_hours' => 'decimal:1',
        'is_elective' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_subjects')
            ->withPivot('semester_no', 'is_elective', 'is_prerequisite_required', 'prerequisite_subject_id')
            ->withTimestamps();
    }
}
