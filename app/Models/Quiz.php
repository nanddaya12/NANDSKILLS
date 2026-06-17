<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Quiz extends Model
{
    use HasUuids;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'time_limit_minutes',
        'max_attempts',
        'passing_score',
        'shuffle_questions',
    ];

    protected $casts = [
        'time_limit_minutes' => 'integer',
        'max_attempts' => 'integer',
        'passing_score' => 'integer',
        'shuffle_questions' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
