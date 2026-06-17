<?php

namespace App\Livewire\Counseling;

use Livewire\Component;
use App\Models\StudentCounselingCase;
use App\Models\CounselingSession;
use App\Models\InterventionPlan;
use App\Models\User;
use App\Models\StudentSessionEnrollment;
use App\Models\AcademicWarning;
use App\Models\BehaviorReport;

class CaseManager extends Component
{
    // Tabs: 'cases', 'early_warning'
    public string $activeTab = 'cases';

    public $cases = [];
    public $counselors = [];
    public $students = [];
    public $warningList = [];

    // Selected case context
    public $selectedCase = null;
    public $selectedCaseSessions = [];
    public $selectedCasePlans = [];

    // Case Form fields
    public bool $isCreatingCase = false;
    public string $new_student_id = '';
    public string $new_counselor_id = '';
    public string $new_case_type = 'ACADEMIC'; // ACADEMIC, PERSONAL, CAREER, FINANCIAL, BEHAVIORAL
    public string $new_priority = 'MEDIUM'; // LOW, MEDIUM, HIGH, URGENT
    public string $new_summary = '';

    // Session Form fields
    public bool $isAddingSession = false;
    public string $sess_date = '';
    public int $sess_duration = 30;
    public string $sess_notes = '';
    public string $sess_next_steps = '';
    public bool $sess_confidential = true;

    // Intervention Form fields
    public bool $isAddingPlan = false;
    public string $plan_goal = '';
    public string $plan_target_date = '';
    public string $plan_steps_text = ''; // comma-separated or list

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Counseling Dashboard is restricted to staff.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($tenantId) {
            $this->cases = StudentCounselingCase::with('student', 'counselor')
                ->where('tenant_id', $tenantId)
                ->orderBy('updated_at', 'desc')
                ->get();

            $this->counselors = User::where('tenant_id', $tenantId)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Trainer', 'Tenant Admin']);
                })->get();

            // Load student list for new cases
            $this->students = User::where('tenant_id', $tenantId)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'Student');
                })->get();
        }

        $this->runEarlyWarningEngine();
    }

    // Early Warning detection engine
    public function runEarlyWarningEngine()
    {
        $this->warningList = [];

        // Fetch students with active warning models
        $warnings = AcademicWarning::with('student')->where('is_resolved', false)->get();
        foreach ($warnings as $w) {
            $this->warningList[] = [
                'student_id' => $w->student_user_id,
                'name' => $w->student->first_name . ' ' . $w->student->last_name,
                'reason' => 'Active Warning: ' . str_replace('_', ' ', $w->warning_type),
                'metric' => 'CGPA: ' . ($w->cgpa_at_warning ?? 'N/A'),
                'severity' => 'HIGH',
            ];
        }

        // Fetch students with CGPA < 2.0 or low standing
        $lowPerformers = StudentSessionEnrollment::with('student')
            ->where('cumulative_cgpa', '<', 2.00)
            ->orWhere('academic_standing', 'WARNING')
            ->get();
        foreach ($lowPerformers as $lp) {
            if ($lp->student) {
                $this->warningList[] = [
                    'student_id' => $lp->student_user_id,
                    'name' => $lp->student->first_name . ' ' . $lp->student->last_name,
                    'reason' => 'Critical CGPA Performance',
                    'metric' => 'CGPA: ' . $lp->cumulative_cgpa,
                    'severity' => 'URGENT',
                ];
            }
        }

        // Behavior incidents with HIGH or CRITICAL severity
        $incidents = BehaviorReport::with('student')->where('severity', 'HIGH')->orWhere('severity', 'CRITICAL')->get();
        foreach ($incidents as $inc) {
            if ($inc->student) {
                $this->warningList[] = [
                    'student_id' => $inc->student_user_id,
                    'name' => $inc->student->first_name . ' ' . $inc->student->last_name,
                    'reason' => 'High Severity Conduct incident',
                    'metric' => $inc->incident_type,
                    'severity' => 'HIGH',
                ];
            }
        }

        // Make list unique
        $this->warningList = collect($this->warningList)->unique('student_id')->toArray();
    }

    public function selectCase(string $id)
    {
        $this->selectedCase = StudentCounselingCase::with('student', 'counselor')->findOrFail($id);
        $this->selectedCaseSessions = CounselingSession::where('case_id', $id)->orderBy('session_date', 'desc')->get();
        $this->selectedCasePlans = InterventionPlan::where('case_id', $id)->orderBy('created_at', 'desc')->get();

        $this->isCreatingCase = false;
        $this->isAddingSession = false;
        $this->isAddingPlan = false;
    }

    public function saveCase()
    {
        $this->validate([
            'new_student_id' => 'required|uuid',
            'new_counselor_id' => 'required|uuid',
            'new_summary' => 'required|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $newCase = StudentCounselingCase::create([
            'tenant_id' => $tenantId,
            'student_user_id' => $this->new_student_id,
            'counselor_user_id' => $this->new_counselor_id,
            'case_type' => $this->new_case_type,
            'priority' => $this->new_priority,
            'status' => 'OPEN',
            'summary' => $this->new_summary,
            'opened_at' => now(),
        ]);

        $this->isCreatingCase = false;
        $this->loadData();
        $this->selectCase($newCase->id);
    }

    public function saveSession()
    {
        $this->validate([
            'sess_date' => 'required|date',
            'sess_notes' => 'required|string',
        ]);

        CounselingSession::create([
            'case_id' => $this->selectedCase->id,
            'session_date' => $this->sess_date,
            'duration_minutes' => $this->sess_duration,
            'notes' => $this->sess_notes,
            'next_steps' => $this->sess_next_steps,
            'is_confidential' => $this->sess_confidential,
        ]);

        $this->isAddingSession = false;
        $this->reset(['sess_date', 'sess_notes', 'sess_next_steps']);
        $this->selectCase($this->selectedCase->id);
    }

    public function savePlan()
    {
        $this->validate([
            'plan_goal' => 'required|string',
        ]);

        // Parse action steps
        $steps = array_filter(array_map('trim', explode(',', $this->plan_steps_text)));

        InterventionPlan::create([
            'case_id' => $this->selectedCase->id,
            'goal' => $this->plan_goal,
            'action_steps' => $steps,
            'target_date' => $this->plan_target_date ?: null,
            'status' => 'ACTIVE',
        ]);

        $this->isAddingPlan = false;
        $this->reset(['plan_goal', 'plan_target_date', 'plan_steps_text']);
        $this->selectCase($this->selectedCase->id);
    }

    public function updateCaseStatus(string $status)
    {
        if ($this->selectedCase) {
            $this->selectedCase->update([
                'status' => $status,
                'closed_at' => $status === 'CLOSED' ? now() : null,
            ]);
            $this->selectCase($this->selectedCase->id);
            $this->loadData();
        }
    }

    public function updatePlanStatus(string $planId, string $status)
    {
        $plan = InterventionPlan::findOrFail($planId);
        $plan->update(['status' => $status]);
        $this->selectCase($this->selectedCase->id);
    }

    public function render()
    {
        return view('livewire.counseling.case-manager')
            ->layout('layouts.app');
    }
}
