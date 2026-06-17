<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class CampusComparisonDashboard extends Component
{
    public string $metric     = 'students';   // students | revenue | attendance | courses
    public string $period     = 'month';      // week | month | year
    public array  $campusData = [];

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Campus Intelligence is restricted.');
        }
        $this->loadData();
    }

    public function setMetric(string $metric): void
    {
        $this->metric = $metric;
        $this->loadData();
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $this->loadData();
    }

    public function loadData(): void
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $since = match ($this->period) {
            'week'  => now()->subWeek(),
            'year'  => now()->subYear(),
            default => now()->subMonth(),
        };

        $branches = Branch::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('name')
            ->get();

        $this->campusData = $branches->map(function ($branch) use ($since) {
            return [
                'id'   => $branch->id,
                'name' => $branch->name,
                'city' => $branch->city ?? '',
                'students'   => DB::table('student_profiles')->where('branch_id', $branch->id)->count(),
                'staff'      => DB::table('employees')->where('branch_id', $branch->id)->count(),
                'courses'    => DB::table('courses')->where('branch_id', $branch->id)->count(),
                'attendance' => DB::table('attendances')
                    ->where('branch_id', $branch->id)
                    ->where('status', 'PRESENT')
                    ->where('date', '>=', $since->toDateString())
                    ->count(),
                'revenue'    => DB::table('invoices')
                    ->where('branch_id', $branch->id)
                    ->where('status', 'PAID')
                    ->where('created_at', '>=', $since)
                    ->sum('total'),
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.admin.campus-comparison-dashboard')
            ->layout('layouts.app');
    }
}
