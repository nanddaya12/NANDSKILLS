<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class PlacementApplication extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'placement_job_id',
        'student_profile_id',
        'resume_text',
        'status',
        'interview_scheduled_at',
    ];

    protected $casts = [
        'interview_scheduled_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(PlacementJob::class, 'placement_job_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}
