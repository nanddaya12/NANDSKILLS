<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * PerformanceOptimizer — Phase 28
 *
 * Provides caching helpers, eager-loading recommendations,
 * and warm-up utilities to keep NANDSKILLS blazing fast.
 */
class PerformanceOptimizer
{
    /** Default cache TTL in seconds (15 minutes) */
    private const TTL = 900;

    // ── Dashboard Stats Caching ──────────────────────────────────────────────

    /**
     * Cache tenant dashboard stats to avoid repeated heavy queries.
     */
    public static function tenantStats(string $tenantId): array
    {
        $key = "tenant_stats:{$tenantId}";

        return Cache::remember($key, self::TTL, function () use ($tenantId) {
            return [
                'total_students'  => DB::table('student_profiles')->where('tenant_id', $tenantId)->count(),
                'active_courses'  => DB::table('courses')->where('tenant_id', $tenantId)->where('status', 'published')->count(),
                'total_revenue'   => DB::table('invoices')->where('tenant_id', $tenantId)->where('status', 'PAID')->sum('total'),
                'open_tickets'    => DB::table('helpdesk_tickets')->where('tenant_id', $tenantId)->whereIn('status', ['OPEN', 'IN_PROGRESS'])->count(),
                'pending_fees'    => DB::table('invoices')->where('tenant_id', $tenantId)->where('status', 'UNPAID')->count(),
                'today_attendance'=> DB::table('attendances')->where('tenant_id', $tenantId)->whereDate('date', today())->count(),
                'cached_at'       => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Bust the tenant stats cache (call after any data change).
     */
    public static function bustTenantStats(string $tenantId): void
    {
        Cache::forget("tenant_stats:{$tenantId}");
    }

    // ── Notification Count Caching ───────────────────────────────────────────

    public static function unreadNotifications(string $userId): int
    {
        return Cache::remember("unread_notif:{$userId}", 60, fn() =>
            DB::table('notification_logs')
                ->where('user_id', $userId)
                ->where('status', 'SENT')
                ->whereNull('read_at')
                ->count()
        );
    }

    public static function bustNotifications(string $userId): void
    {
        Cache::forget("unread_notif:{$userId}");
    }

    // ── SaaS Metrics Caching (Super Admin) ──────────────────────────────────

    public static function saasMetrics(): array
    {
        return Cache::remember('saas_metrics', 300, function () {
            return [
                'active_tenants' => DB::table('tenant_subscriptions')->where('status', 'ACTIVE')->count(),
                'mrr'            => DB::table('tenant_subscriptions')
                    ->join('tenant_plans', 'tenant_subscriptions.plan_id', '=', 'tenant_plans.id')
                    ->where('tenant_subscriptions.status', 'ACTIVE')
                    ->sum('tenant_plans.monthly_price'),
                'total_users'    => DB::table('users')->where('is_active', true)->count(),
                'cached_at'      => now()->toIso8601String(),
            ];
        });
    }

    public static function bustSaasMetrics(): void
    {
        Cache::forget('saas_metrics');
    }

    // ── Eager Loading Audit ───────────────────────────────────────────────────

    /**
     * Returns a list of recommended eager-load patterns for common queries.
     * Use this as documentation for developers to avoid N+1 queries.
     */
    public static function eagerLoadingGuide(): array
    {
        return [
            'Course list'         => "Course::with(['trainer', 'enrollments', 'chapters'])->paginate(20)",
            'StudentProfile list' => "StudentProfile::with(['user', 'branch', 'program'])->paginate(25)",
            'Invoice list'        => "Invoice::with(['studentFee.student.user', 'payments'])->paginate(20)",
            'Admission tracker'   => "Admission::with(['form', 'documents', 'interviews', 'reviewer'])->paginate(15)",
            'Timetable entries'   => "TimetableEntry::with(['slot', 'subject', 'trainer', 'room'])->get()",
            'Audit log'           => "ActivityLog::with('user:id,name,email')->paginate(25)",
        ];
    }

    // ── Queue-based Notification Dispatch ────────────────────────────────────

    /**
     * Queue a notification dispatch instead of sending synchronously.
     * Requires a queue worker: php artisan queue:work
     */
    public static function queueNotification(array $payload): void
    {
        dispatch(function () use ($payload) {
            app(\App\Services\NotificationDispatcher::class)->dispatchAdHoc(
                $payload['tenant_id'],
                $payload['channel'],
                $payload['audience'],
                $payload['subject'],
                $payload['body'],
            );
        })->onQueue('notifications');
    }

    // ── Cache Warm-up ─────────────────────────────────────────────────────────

    /**
     * Warm up caches for all active tenants.
     * Call from: php artisan app:cache-warm
     */
    public static function warmAll(): void
    {
        $tenants = DB::table('tenants')->where('is_active', true)->pluck('id');
        foreach ($tenants as $tenantId) {
            static::tenantStats($tenantId);
        }
        static::saasMetrics();
    }
}
