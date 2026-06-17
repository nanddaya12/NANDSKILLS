<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\LoginHistory;
use App\Models\FailedLogin;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class SecurityCenter extends Component
{
    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized. Security Center is restricted to Administrators.');
        }
    }

    public function revokeSession($sessionId)
    {
        DB::table('sessions')->where('id', $sessionId)->delete();
        session()->flash('status', 'Active user session revoked successfully.');
    }

    public function render()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        // Active database sessions
        $activeSessions = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('users.tenant_id', $tenantId)
            ->select('sessions.id', 'sessions.ip_address', 'sessions.user_agent', 'sessions.last_activity', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('sessions.last_activity', 'desc')
            ->get();

        $failedLogins = FailedLogin::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        $loginHistories = LoginHistory::where('tenant_id', $tenantId)
            ->with('user')
            ->orderBy('logged_in_at', 'desc')
            ->take(15)
            ->get();

        $auditLogs = AuditLog::where('tenant_id', $tenantId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        return view('livewire.admin.security-center', [
            'activeSessions' => $activeSessions,
            'failedLogins' => $failedLogins,
            'loginHistories' => $loginHistories,
            'auditLogs' => $auditLogs,
        ])->layout('layouts.app');
    }
}
