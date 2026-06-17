<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CrmActivity extends Model
{
    use HasUuids;

    protected $fillable = [
        'lead_id',
        'deal_id',
        'type', // CALL, EMAIL, MEETING, NOTE, TASK
        'description',
        'scheduled_at',
        'completed_at',
        'performed_by_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_id');
    }
}
