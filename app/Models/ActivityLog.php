<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ActivityLog extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'activity_logs';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role_name',
        'model_type',
        'model_id',
        'action',
        'before_value',
        'after_value',
        'ip_address',
        'device',
        'browser',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'before_value' => 'array',
        'after_value' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
