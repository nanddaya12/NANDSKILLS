<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ContentVersion extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'content_versions';

    protected $fillable = [
        'tenant_id',
        'library_id',
        'version_number',
        'file_url',
        'change_log',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function library(): BelongsTo
    {
        return $this->belongsTo(ContentLibrary::class, 'library_id');
    }

}
