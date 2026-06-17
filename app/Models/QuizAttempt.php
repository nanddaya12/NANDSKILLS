<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class QuizAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'score',
        'answers',
        'status', // IN_PROGRESS, COMPLETED
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public $timestamps = false; // Custom datetime tracking used

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
