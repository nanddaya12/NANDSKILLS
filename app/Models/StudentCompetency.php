<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class StudentCompetency extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'student_competencies';

    protected $fillable = [
        'tenant_id',
        'student_id',
        'competency_id',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }

}
