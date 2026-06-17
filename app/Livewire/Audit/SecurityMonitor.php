<?php

namespace App\Livewire\Audit;

use Livewire\Component;
use App\Models\FailedLogin;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SecurityMonitor extends Component
{
    public array $failedLoginsByHour = [];
    public array $suspiciousUsers    = [];
    public int   $failedLoginsToday  = 0;
    public int   $lockedAccounts     = 0;
    public int   $activeSessions     = 0;
    public string $selectedPeriod    = '24h';  // 24h, 7d, 30d

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Security Monitor is restricted.');
        }
        $this->loadData();
    }

    public function setPeriod(string $period): void
    {
        $this->selectedPeriod = $period;
        $this->loadData();
    }

    public function unlockUser(string $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => true]);
        $this->loadData();
    }

    public function loadData(): void
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $since = match ($this->selectedPeriod) {
            '7d'  => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subHours(24),
        };

        // Failed logins today
        $this->failedLoginsToday = FailedLogin::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereDate('created_at', today())
            ->count();

        // Failed logins by hour (last 24h)
        $this->failedLoginsByHour = FailedLogin::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', $since)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // Suspicious users (>5 failed logins)
        $this->suspiciousUsers = FailedLogin::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', $since)
            ->select('email', DB::raw('COUNT(*) as attempts'), DB::raw('MAX(created_at) as last_attempt'))
            ->groupBy('email')
            ->having('attempts', '>', 5)
            ->orderByDesc('attempts')
            ->limit(10)
            ->get()
            ->toArray();

        // Locked accounts
        $this->lockedAccounts = User::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('is_active', false)
            ->count();
    }

    public function render()
    {
        return view('livewire.audit.security-monitor')
            ->layout('layouts.app');
    }
}
