<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class QuizQuestion extends Model
{
    use HasUuids;

    protected $fillable = [
        'quiz_id',
        'text',
        'type', // MCQ, MULTIPLE_CHOICE, TRUE_FALSE, FILL_IN_BLANKS, SUBJECTIVE
        'points',
        'options',
        'correct_answer',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
        'points' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
