<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\StudentProfile;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\Invoice;

class IntelligentAnalytics extends Component
{
    public string $activeTab  = 'risk';   // risk | recommendations | trends | cohort
    public string $period     = 'month';  // week | month | semester | year
    public array  $atRiskStudents     = [];
    public array  $topPerformers      = [];
    public array  $enrollmentTrend    = [];
    public array  $attendanceTrend    = [];
    public array  $revenueTrend       = [];
    public array  $courseCompletion   = [];

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Intelligent Analytics is restricted.');
        }
        $this->loadData();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
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
            'week'     => now()->subWeek(),
            'semester' => now()->subMonths(6),
            'year'     => now()->subYear(),
            default    => now()->subMonth(),
        };

        // At-risk students: low attendance OR failing grades
        $this->atRiskStudents = DB::table('student_profiles as sp')
            ->leftJoin('attendances as a', function ($join) use ($since) {
                $join->on('a.student_id', '=', 'sp.id')
                     ->where('a.date', '>=', $since->toDateString());
            })
            ->when($tenantId, fn($q) => $q->where('sp.tenant_id', $tenantId))
            ->select(
                'sp.id', 'sp.student_id_no',
                DB::raw('COUNT(a.id) as total_sessions'),
                DB::raw('SUM(CASE WHEN a.status = "PRESENT" THEN 1 ELSE 0 END) as present_count')
            )
            ->groupBy('sp.id', 'sp.student_id_no')
            ->havingRaw('total_sessions > 0 AND (present_count / total_sessions) < 0.75')
            ->orderByRaw('(present_count / total_sessions) ASC')
            ->limit(20)
            ->get()
            ->map(fn($s) => [
                'id'          => $s->id,
                'student_no'  => $s->student_id_no,
                'attendance'  => $s->total_sessions > 0
                    ? round(($s->present_count / $s->total_sessions) * 100, 1)
                    : 0,
                'risk_level'  => ($s->total_sessions > 0 && ($s->present_count / $s->total_sessions) < 0.5) ? 'HIGH' : 'MEDIUM',
            ])
            ->toArray();

        // Top performers: highest exam scores
        $this->topPerformers = DB::table('exam_results as er')
            ->join('student_profiles as sp', 'sp.id', '=', 'er.student_id')
            ->when($tenantId, fn($q) => $q->where('sp.tenant_id', $tenantId))
            ->where('er.created_at', '>=', $since)
            ->select('sp.id', 'sp.student_id_no', DB::raw('AVG(er.marks_obtained) as avg_score'), DB::raw('COUNT(er.id) as exams_taken'))
            ->groupBy('sp.id', 'sp.student_id_no')
            ->having('exams_taken', '>=', 1)
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get()
            ->toArray();

        // Enrollment trend (last 12 months)
        $this->enrollmentTrend = DB::table('enrollments')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Revenue trend (last 12 months)
        $this->revenueTrend = DB::table('invoices')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'PAID')
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(total) as revenue'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        // Course completion rates
        $this->courseCompletion = DB::table('courses as c')
            ->leftJoin('enrollments as e', function ($join) {
                $join->on('e.course_id', '=', 'c.id');
            })
            ->when($tenantId, fn($q) => $q->where('c.tenant_id', $tenantId))
            ->select(
                'c.id', 'c.title',
                DB::raw('COUNT(e.id) as total'),
                DB::raw('SUM(CASE WHEN e.completion_percentage >= 100 THEN 1 ELSE 0 END) as completed')
            )
            ->groupBy('c.id', 'c.title')
            ->having('total', '>', 0)
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'title'      => $c->title,
                'total'      => $c->total,
                'completed'  => $c->completed,
                'rate'       => $c->total > 0 ? round(($c->completed / $c->total) * 100, 1) : 0,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.intelligent-analytics')
            ->layout('layouts.app');
    }
}
