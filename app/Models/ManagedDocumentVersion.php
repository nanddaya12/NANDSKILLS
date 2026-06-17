<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ManagedDocumentVersion extends Model
{
    use HasUuids;

    protected $table = 'managed_document_versions';

    protected $fillable = [
        'document_id',
        'version_no',
        'file_path',
        'file_size',
        'uploaded_by',
        'change_notes',
    ];

    protected $casts = [
        'version_no' => 'integer',
        'file_size' => 'integer',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(ManagedDocument::class, 'document_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
