<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Str;

class ActivityLogger
{
    /**
     * Log any action taken on a model or standalone event.
     *
     * @param  string       $action      CREATE | UPDATE | DELETE | LOGIN | LOGOUT | APPROVE | FINANCIAL | EXAM
     * @param  string|null  $description Human-readable description
     * @param  mixed|null   $model       Eloquent model instance (optional)
     * @param  array|null   $before      Previous state (for UPDATE/DELETE)
     * @param  array|null   $after       New state (for UPDATE/CREATE)
     */
    public static function log(
        string $action,
        ?string $description = null,
        mixed $model = null,
        ?array $before = null,
        ?array $after = null
    ): void {
        try {
            $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
            $user     = auth()->user();

            ActivityLog::create([
                'tenant_id'    => $tenantId,
                'user_id'      => $user?->id,
                'role_name'    => $user?->roles()->pluck('name')->first(),
                'model_type'   => $model ? get_class($model) : null,
                'model_id'     => $model?->id,
                'action'       => $action,
                'before_value' => $before ? self::sanitize($before) : null,
                'after_value'  => $after  ? self::sanitize($after)  : null,
                'ip_address'   => request()->ip(),
                'device'       => self::guessDevice(),
                'browser'      => self::guessBrowser(),
                'user_agent'   => Str::limit(request()->userAgent() ?? '', 250),
                'description'  => $description,
            ]);
        } catch (\Throwable $e) {
            // Never let logging crash the application
            \Illuminate\Support\Facades\Log::warning('ActivityLogger failed: ' . $e->getMessage());
        }
    }

    /**
     * Convenience shortcut: log a login event.
     */
    public static function login(?string $description = null): void
    {
        static::log('LOGIN', $description ?? 'User logged in');
    }

    /**
     * Convenience shortcut: log a logout event.
     */
    public static function logout(): void
    {
        static::log('LOGOUT', 'User logged out');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private static function sanitize(array $data): array
    {
        $sensitive = ['password', 'password_hash', 'token', 'secret', 'api_key', 'remember_token'];
        foreach ($sensitive as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***';
            }
        }
        return $data;
    }

    private static function guessDevice(): string
    {
        $ua = strtolower(request()->userAgent() ?? '');
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'Mobile';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'Tablet';
        }
        return 'Desktop';
    }

    private static function guessBrowser(): string
    {
        $ua = strtolower(request()->userAgent() ?? '');
        if (str_contains($ua, 'edg'))    return 'Edge';
        if (str_contains($ua, 'chrome')) return 'Chrome';
        if (str_contains($ua, 'firefox')) return 'Firefox';
        if (str_contains($ua, 'safari')) return 'Safari';
        if (str_contains($ua, 'opera'))  return 'Opera';
        return 'Unknown';
    }
}
