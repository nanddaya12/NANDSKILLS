<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssignmentSubmission extends Model
{
    use HasUuids;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'status', // SUBMITTED, GRADED, RESUBMIT_REQUESTED
        'submission_url',
        'storage_key',
        'grade',
        'feedback',
        'submitted_at',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'grade' => 'integer',
    ];

    public $timestamps = false; // Manually tracking submitted_at and graded_at

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
