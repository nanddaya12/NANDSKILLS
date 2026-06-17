<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AdmissionInterview extends Model
{
    use HasUuids;

    protected $fillable = [
        'admission_id',
        'interviewer_id',
        'scheduled_at',
        'format',
        'meeting_link',
        'status',
        'notes',
        'result',
        'score',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'score' => 'integer',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
