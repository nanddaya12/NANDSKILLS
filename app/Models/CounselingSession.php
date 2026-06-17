<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CounselingSession extends Model
{
    use HasUuids;

    protected $table = 'counseling_sessions';

    protected $fillable = [
        'case_id',
        'session_date',
        'duration_minutes',
        'notes',
        'next_steps',
        'is_confidential',
    ];

    protected $casts = [
        'session_date' => 'date',
        'duration_minutes' => 'integer',
        'is_confidential' => 'boolean',
    ];

    public function counselingCase(): BelongsTo
    {
        return $this->belongsTo(StudentCounselingCase::class, 'case_id');
    }
}
