<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Lesson extends Model
{
    use HasUuids;

    protected $fillable = [
        'chapter_id',
        'title',
        'description',
        'content',
        'type', // VIDEO, AUDIO, PDF, DOC, PPT, HTML, SCORM, ASSIGNMENT, QUIZ
        'url',
        'storage_key',
        'is_downloadable',
        'order_index',
        'duration_minutes',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'order_index' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LessonNote::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LessonResource::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }
}
