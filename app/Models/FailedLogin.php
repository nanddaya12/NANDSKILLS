<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class FailedLogin extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'failed_logins';

    protected $fillable = [
        'tenant_id',
        'email',
        'ip_address',
        'user_agent',
        'portal',
        'reason',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
