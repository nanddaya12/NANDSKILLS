<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProgramSubject extends Model
{
    use HasUuids;

    protected $table = 'program_subjects';

    protected $fillable = [
        'program_id',
        'subject_id',
        'semester_no',
        'is_elective',
        'is_prerequisite_required',
        'prerequisite_subject_id',
    ];

    protected $casts = [
        'semester_no' => 'integer',
        'is_elective' => 'boolean',
        'is_prerequisite_required' => 'boolean',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function prerequisiteSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'prerequisite_subject_id');
    }
}
