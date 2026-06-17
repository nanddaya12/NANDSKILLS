<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class ManagedDocument extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'managed_documents';

    protected $fillable = [
        'tenant_id',
        'category_id',
        'owner_type',
        'owner_id',
        'title',
        'description',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'disk',
        'version',
        'status',
        'expiry_date',
        'uploaded_by',
        'is_public',
        'download_count',
        'tags',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'version' => 'integer',
        'expiry_date' => 'date',
        'is_public' => 'boolean',
        'download_count' => 'integer',
        'tags' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ManagedDocumentVersion::class, 'document_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ManagedDocumentApproval::class, 'document_id');
    }

    public function auditTrails(): HasMany
    {
        return $this->hasMany(DocumentAuditTrail::class, 'document_id');
    }
}
