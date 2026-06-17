<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ComplianceEvidence extends Model
{
    use HasUuids;

    protected $table = 'compliance_evidences';

    protected $fillable = [
        'requirement_id',
        'title',
        'description',
        'file_path',
        'uploaded_by',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(ComplianceRequirement::class, 'requirement_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
