<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Assignment extends Model
{
    use HasUuids;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'max_points',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'max_points' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
