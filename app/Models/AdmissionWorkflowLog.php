<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AdmissionWorkflowLog extends Model
{
    use HasUuids;

    protected $table = 'admission_workflow_logs';

    protected $fillable = [
        'admission_id',
        'from_status',
        'to_status',
        'changed_by',
        'notes',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
