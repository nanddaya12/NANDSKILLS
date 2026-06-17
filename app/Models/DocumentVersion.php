<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class DocumentVersion extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'document_versions';

    protected $fillable = [
        'tenant_id',
        'document_id',
        'version_number',
        'file_url',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

}
