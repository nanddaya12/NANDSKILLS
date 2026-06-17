<?php

namespace App\Livewire\Audit;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuditDashboard extends Component
{
    use WithPagination;

    public string $searchUser   = '';
    public string $filterAction = '';
    public string $filterModel  = '';
    public string $dateFrom     = '';
    public string $dateTo       = '';
    public string $activeTab    = 'logs';  // logs | stats | security

    // Stats cache
    public array $actionStats   = [];
    public array $topUsers      = [];
    public int   $todayCount    = 0;
    public int   $weekCount     = 0;

    protected $queryString = ['searchUser', 'filterAction', 'filterModel'];

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Audit Dashboard is restricted.');
        }
        $this->computeStats();
    }

    public function computeStats(): void
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $base = ActivityLog::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId));

        // Action breakdown
        $this->actionStats = $base->clone()
            ->select('action', DB::raw('COUNT(*) as total'))
            ->groupBy('action')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'action')
            ->toArray();

        // Top users by activity
        $this->topUsers = $base->clone()
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('user:id,name')
            ->get()
            ->map(fn($log) => [
                'user'  => User::find($log->user_id)?->name ?? 'Unknown',
                'total' => $log->total,
            ])
            ->toArray();

        $this->todayCount = $base->clone()->whereDate('created_at', today())->count();
        $this->weekCount  = $base->clone()->where('created_at', '>=', now()->subDays(7))->count();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->searchUser = '';
        $this->filterAction = '';
        $this->filterModel = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function updatedSearchUser(): void { $this->resetPage(); }
    public function updatedFilterAction(): void { $this->resetPage(); }

    public function render()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $logs = ActivityLog::with('user:id,name,email')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($this->searchUser, fn($q) => $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->searchUser . '%')))
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->when($this->filterModel, fn($q) => $q->where('model_type', 'like', '%' . $this->filterModel . '%'))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderByDesc('created_at')
            ->paginate(25);

        $actions = ['CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT', 'APPROVE', 'FINANCIAL', 'EXAM', 'ATTENDANCE'];

        return view('livewire.audit.audit-dashboard', compact('logs', 'actions'))
            ->layout('layouts.app');
    }
}
