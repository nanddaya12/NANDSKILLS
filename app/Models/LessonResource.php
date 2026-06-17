<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LessonResource extends Model
{
    use HasUuids;

    protected $fillable = [
        'lesson_id',
        'title',
        'file_url',
        'storage_key',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
