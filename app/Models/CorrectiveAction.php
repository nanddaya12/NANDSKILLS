<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CorrectiveAction extends Model
{
    use HasUuids;

    protected $table = 'corrective_actions';

    protected $fillable = [
        'audit_id',
        'requirement_id',
        'title',
        'description',
        'responsible_user_id',
        'due_date',
        'priority',
        'status',
        'resolution_notes',
        'closed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(ComplianceAudit::class, 'audit_id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(ComplianceRequirement::class, 'requirement_id');
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
}
