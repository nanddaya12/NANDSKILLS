<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ApiLog extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'api_logs';

    protected $fillable = [
        'tenant_id',
        'api_key_id',
        'endpoint',
        'method',
        'ip_address',
        'status_code',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class, 'api_key_id');
    }

}
