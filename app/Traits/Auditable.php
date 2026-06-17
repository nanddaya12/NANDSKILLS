<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            self::logAudit('CREATE', $model, null, $model->toArray());
        });

        static::updated(function ($model) {
            $before = array_intersect_key($model->getOriginal(), $model->getDirty());
            $after = $model->getDirty();
            // Don't log if nothing actually changed
            if (empty($after)) {
                return;
            }
            self::logAudit('UPDATE', $model, $before, $after);
        });

        static::deleted(function ($model) {
            self::logAudit('DELETE', $model, $model->toArray(), null);
        });
    }

    protected static function logAudit(string $action, $model, ?array $before, ?array $after): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        // Disable query log auditing during seeding or when CLI runs without requests
        $ip = request() ? request()->ip() : 'CLI';
        $ua = request() ? request()->userAgent() : 'CLI';

        AuditLog::create([
            'tenant_id' => app()->bound('currentTenant') ? app('currentTenant')->id : null,
            'user_id' => Auth::id(),
            'action' => $action,
            'entity' => class_basename($model),
            'entity_id' => $model->id,
            'before_state' => $before,
            'after_state' => $after,
            'ip_address' => $ip,
            'user_agent' => $ua,
        ]);
    }
}
