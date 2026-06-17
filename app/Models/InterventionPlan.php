<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InterventionPlan extends Model
{
    use HasUuids;

    protected $table = 'intervention_plans';

    protected $fillable = [
        'case_id',
        'goal',
        'action_steps',
        'target_date',
        'status',
        'outcome',
    ];

    protected $casts = [
        'action_steps' => 'array',
        'target_date' => 'date',
    ];

    public function counselingCase(): BelongsTo
    {
        return $this->belongsTo(StudentCounselingCase::class, 'case_id');
    }
}
