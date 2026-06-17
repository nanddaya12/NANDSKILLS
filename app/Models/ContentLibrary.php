<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ContentLibrary extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'content_libraries';

    protected $fillable = [
        'tenant_id',
        'title',
        'type',
        'file_url',
        'storage_key',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
