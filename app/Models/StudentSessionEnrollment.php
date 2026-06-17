<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class StudentSessionEnrollment extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'student_session_enrollments';

    protected $fillable = [
        'tenant_id',
        'academic_session_id',
        'student_user_id',
        'program_id',
        'current_semester',
        'semester_gpa',
        'cumulative_cgpa',
        'credits_earned',
        'academic_standing',
        'status',
    ];

    protected $casts = [
        'current_semester' => 'integer',
        'semester_gpa' => 'decimal:2',
        'cumulative_cgpa' => 'decimal:2',
        'credits_earned' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}
