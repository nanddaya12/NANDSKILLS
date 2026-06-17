<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class MeritList extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'merit_lists';

    protected $fillable = [
        'tenant_id',
        'program_id',
        'academic_session_id',
        'admission_id',
        'merit_score',
        'rank',
        'status',
        'notes',
    ];

    protected $casts = [
        'merit_score' => 'decimal:2',
        'rank' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}
