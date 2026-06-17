<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ApiKey extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'api_keys';

    protected $fillable = [
        'tenant_id',
        'name',
        'key_hash',
        'is_active',
        'rate_limit',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
