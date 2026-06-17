<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class PermissionGroup extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'permission_groups';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
