<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class BehaviorReport extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'behavior_reports';

    protected $fillable = [
        'tenant_id',
        'student_user_id',
        'reported_by',
        'incident_date',
        'incident_type',
        'severity',
        'description',
        'action_taken',
        'parent_notified',
        'parent_notified_at',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'parent_notified' => 'boolean',
        'parent_notified_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
