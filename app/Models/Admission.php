<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class Admission extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $fillable = [
        'tenant_id',
        'admission_form_id',
        'program_id',
        'academic_session_id',
        'applicant_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'nationality',
        'previous_qualification',
        'previous_grade',
        'form_responses',
        'status',
        'application_number',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
        'decision_at',
        'rejection_reason',
        'admin_notes',
        'created_user_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'form_responses' => 'array',
        'previous_grade' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'decision_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function admissionForm(): BelongsTo
    {
        return $this->belongsTo(AdmissionForm::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AdmissionDocument::class);
    }

    public function tests(): HasMany
    {
        return $this->hasMany(AdmissionTest::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(AdmissionInterview::class);
    }

    public function meritListings(): HasMany
    {
        return $this->hasMany(MeritList::class);
    }

    public function workflowLogs(): HasMany
    {
        return $this->hasMany(AdmissionWorkflowLog::class);
    }
}
