<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PushToken extends Model
{
    use HasUuids;

    protected $table = 'push_tokens';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'token',
        'platform',
        'app_type',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Register or refresh a push token for a user.
     */
    public static function upsert(string $userId, string $token, string $platform = 'WEB', string $appType = 'student'): self
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        return static::updateOrCreate(
            ['user_id' => $userId, 'token' => $token],
            [
                'tenant_id'    => $tenantId,
                'platform'     => $platform,
                'app_type'     => $appType,
                'is_active'    => true,
                'last_used_at' => now(),
            ]
        );
    }
}
