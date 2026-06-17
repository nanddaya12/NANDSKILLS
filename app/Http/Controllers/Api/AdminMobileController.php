<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Enrollment;
use App\Models\ActivityLog;
use App\Models\NotificationLog;
use App\Models\PushToken;
use App\Services\NotificationDispatcher;

/**
 * Admin Mobile API — super-admin & tenant-admin dashboard endpoints.
 */
class AdminMobileController extends Controller
{
    /** GET /api/v1/admin/dashboard */
    public function dashboard(Request $request)
    {
        $user     = $request->user();
        $tenantId = $user->tenant_id;

        $totalStudents  = StudentProfile::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count();
        $activeUsers    = User::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->where('is_active', true)->count();
        $totalCourses   = Course::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->count();
        $totalRevenue   = Invoice::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->where('status', 'PAID')->sum('total');
        $pendingInvoices = Invoice::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->where('status', 'UNPAID')->count();
        $recentActivity  = ActivityLog::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->whereDate('created_at', today())->count();

        // Month-over-month enrollment
        $thisMonth = Enrollment::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereMonth('created_at', now()->month)->count();
        $lastMonth = Enrollment::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereMonth('created_at', now()->subMonth()->month)->count();

        return response()->json([
            'total_students'   => $totalStudents,
            'active_users'     => $activeUsers,
            'total_courses'    => $totalCourses,
            'total_revenue'    => $totalRevenue,
            'pending_invoices' => $pendingInvoices,
            'activity_today'   => $recentActivity,
            'enrollment_growth' => $lastMonth > 0
                ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
                : 100,
        ]);
    }

    /** GET /api/v1/admin/users */
    public function users(Request $request)
    {
        $user     = $request->user();
        $tenantId = $user->tenant_id;
        $search   = $request->get('search', '');

        $users = User::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            }))
            ->with('roles:id,name')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data'  => $users->map(fn($u) => [
                'id'        => $u->id,
                'name'      => $u->first_name . ' ' . $u->last_name,
                'email'     => $u->email,
                'is_active' => $u->is_active,
                'roles'     => $u->roles->pluck('name'),
                'joined'    => optional($u->created_at)->toDateString(),
            ]),
            'total'       => $users->total(),
            'current_page'=> $users->currentPage(),
            'last_page'   => $users->lastPage(),
        ]);
    }

    /** POST /api/v1/admin/users/{id}/toggle-active */
    public function toggleUserActive(Request $request, string $id)
    {
        $user   = $request->user();
        $target = User::when($user->tenant_id, fn($q) => $q->where('tenant_id', $user->tenant_id))
            ->findOrFail($id);

        $target->update(['is_active' => !$target->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $target->is_active,
        ]);
    }

    /** GET /api/v1/admin/revenue */
    public function revenue(Request $request)
    {
        $user     = $request->user();
        $tenantId = $user->tenant_id;

        $monthly = DB::table('invoices')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'PAID')
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(total) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'labels' => $monthly->pluck('month'),
            'values' => $monthly->pluck('total'),
            'total'  => $monthly->sum('total'),
        ]);
    }

    /** POST /api/v1/admin/notify */
    public function sendNotification(Request $request, NotificationDispatcher $dispatcher)
    {
        $request->validate([
            'subject'  => 'required|string|max:150',
            'body'     => 'required|string',
            'audience' => 'required|in:students,teachers,parents,all',
            'channel'  => 'required|in:in_app,email,sms,push',
        ]);

        $user     = $request->user();
        $tenantId = $user->tenant_id;

        if (!$tenantId) {
            return response()->json(['error' => 'Tenant context required.'], 422);
        }

        $sent = $dispatcher->dispatchAdHoc(
            $tenantId,
            $request->channel,
            $request->audience,
            $request->subject,
            $request->body,
        );

        return response()->json(['success' => true, 'recipients' => $sent]);
    }

    /** POST /api/v1/admin/push-token */
    public function registerPushToken(Request $request)
    {
        $request->validate(['token' => 'required|string|max:512', 'platform' => 'nullable|in:WEB,IOS,ANDROID']);
        PushToken::upsert($request->user()->id, $request->token, $request->platform ?? 'WEB', 'admin');
        return response()->json(['success' => true]);
    }
}
