<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class LoginHistory extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'login_histories';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'ip_address',
        'user_agent',
        'logged_in_at',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
