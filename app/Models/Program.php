<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class Program extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $fillable = [
        'tenant_id',
        'faculty_id',
        'department_id',
        'name',
        'code',
        'degree_type',
        'duration_years',
        'total_semesters',
        'credit_hours_required',
        'min_cgpa_required',
        'description',
        'status',
    ];

    protected $casts = [
        'duration_years' => 'integer',
        'total_semesters' => 'integer',
        'credit_hours_required' => 'decimal:2',
        'min_cgpa_required' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'program_subjects')
            ->withPivot('semester_no', 'is_elective', 'is_prerequisite_required', 'prerequisite_subject_id')
            ->withTimestamps();
    }

    public function gradingRules(): HasMany
    {
        return $this->hasMany(GradingRule::class);
    }

    public function cgpaRules(): HasMany
    {
        return $this->hasMany(CgpaRule::class);
    }

    public function sessionEnrollments(): HasMany
    {
        return $this->hasMany(StudentSessionEnrollment::class);
    }
}
