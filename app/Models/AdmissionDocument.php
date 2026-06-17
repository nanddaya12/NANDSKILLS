<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AdmissionDocument extends Model
{
    use HasUuids;

    protected $fillable = [
        'admission_id',
        'document_type',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'is_verified',
        'verified_by',
        'verified_at',
        'verification_notes',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
