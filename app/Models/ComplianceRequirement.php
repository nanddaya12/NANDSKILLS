<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ComplianceRequirement extends Model
{
    use HasUuids;

    protected $table = 'compliance_requirements';

    protected $fillable = [
        'framework_id',
        'requirement_code',
        'category',
        'title',
        'description',
        'evidence_required',
        'status',
    ];

    protected $casts = [
        'evidence_required' => 'boolean',
    ];

    public function framework(): BelongsTo
    {
        return $this->belongsTo(ComplianceFramework::class, 'framework_id');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(ComplianceEvidence::class, 'requirement_id');
    }
}
