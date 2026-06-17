<?php

namespace App\Livewire\Academic;

use Livewire\Component;
use App\Models\AcademicYear;
use App\Models\AcademicSession;
use App\Models\Faculty;
use App\Models\GradingRule;
use App\Models\CgpaRule;
use App\Models\AcademicCalendar;
use App\Models\User;

class AcademicYearManager extends Component
{
    // Active tab: 'years', 'sessions', 'faculties', 'grading', 'cgpa', 'calendar'
    public string $activeTab = 'years';

    // Year variables
    public $years = [];
    public string $year_name = '';
    public string $year_start_date = '';
    public string $year_end_date = '';
    public string $year_description = '';
    public bool $year_is_current = false;
    public string $selectedYearId = '';

    // Session variables
    public $sessions = [];
    public string $selectedSessionYearId = '';
    public string $session_name = '';
    public string $session_type = 'SEMESTER'; // SEMESTER, TERM, QUARTER, TRIMESTER
    public string $session_start_date = '';
    public string $session_end_date = '';
    public bool $session_is_current = false;
    public string $session_status = 'UPCOMING'; // UPCOMING, ACTIVE, COMPLETED

    // Faculty variables
    public $faculties = [];
    public string $faculty_name = '';
    public string $faculty_code = '';
    public string $faculty_description = '';
    public string $faculty_head_user_id = '';
    public $staffMembers = [];

    // Grading rules
    public $gradingRules = [];
    public string $grade_letter = '';
    public float $grade_min_score = 0;
    public float $grade_max_score = 100;
    public float $grade_points = 4.0;
    public string $grade_status = 'PASS';

    // CGPA rules
    public $cgpaRules = [];
    public string $cgpa_standing_name = '';
    public float $cgpa_min = 0.0;
    public float $cgpa_max = 4.0;
    public string $cgpa_status_tag = 'GOOD'; // EXCELLENT, GOOD, WARNING, PROBATION, DISMISSED
    public string $cgpa_description = '';

    // Calendar variables
    public $calendarEvents = [];
    public string $event_year_id = '';
    public string $event_session_id = '';
    public string $event_title = '';
    public string $event_date = '';
    public string $event_end_date = '';
    public string $event_type = 'GENERAL'; // HOLIDAY, EXAM, REGISTRATION, ORIENTATION, GENERAL, BREAK
    public string $event_description = '';
    public bool $event_is_holiday = false;
    public string $event_color = '#3B82F6';

    public bool $isFormOpen = false;

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Academic Setup is restricted to Institutional Admins.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->years = AcademicYear::orderBy('start_date', 'desc')->get();
        $this->sessions = AcademicSession::with('academicYear')->orderBy('start_date', 'desc')->get();
        $this->faculties = Faculty::with('head')->get();
        $this->gradingRules = GradingRule::orderBy('min_score', 'desc')->get();
        $this->cgpaRules = CgpaRule::orderBy('min_cgpa', 'desc')->get();
        $this->calendarEvents = AcademicCalendar::with('academicYear', 'academicSession')->orderBy('event_date', 'desc')->get();

        // Load staff members for department head selector
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId) {
            $this->staffMembers = User::where('tenant_id', $tenantId)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Trainer', 'Tenant Admin']);
                })->get();
        }
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->isFormOpen = false;
        $this->resetFormFields();
    }

    private function resetFormFields()
    {
        // Reset Year fields
        $this->year_name = '';
        $this->year_start_date = '';
        $this->year_end_date = '';
        $this->year_description = '';
        $this->year_is_current = false;
        $this->selectedYearId = '';

        // Reset Session fields
        $this->selectedSessionYearId = '';
        $this->session_name = '';
        $this->session_type = 'SEMESTER';
        $this->session_start_date = '';
        $this->session_end_date = '';
        $this->session_is_current = false;
        $this->session_status = 'UPCOMING';

        // Reset Faculty fields
        $this->faculty_name = '';
        $this->faculty_code = '';
        $this->faculty_description = '';
        $this->faculty_head_user_id = '';

        // Reset Grading fields
        $this->grade_letter = '';
        $this->grade_min_score = 0;
        $this->grade_max_score = 100;
        $this->grade_points = 4.0;
        $this->grade_status = 'PASS';

        // Reset CGPA fields
        $this->cgpa_standing_name = '';
        $this->cgpa_min = 0.0;
        $this->cgpa_max = 4.0;
        $this->cgpa_status_tag = 'GOOD';
        $this->cgpa_description = '';

        // Reset Calendar fields
        $this->event_year_id = '';
        $this->event_session_id = '';
        $this->event_title = '';
        $this->event_date = '';
        $this->event_end_date = '';
        $this->event_type = 'GENERAL';
        $this->event_description = '';
        $this->event_is_holiday = false;
        $this->event_color = '#3B82F6';
    }

    public function openForm()
    {
        $this->resetFormFields();
        $this->isFormOpen = true;
    }

    public function saveYear()
    {
        $this->validate([
            'year_name' => 'required|string|max:100',
            'year_start_date' => 'required|date',
            'year_end_date' => 'required|date|after:year_start_date',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($this->year_is_current && $tenantId) {
            AcademicYear::where('tenant_id', $tenantId)->update(['is_current' => false]);
        }

        AcademicYear::create([
            'tenant_id' => $tenantId,
            'name' => $this->year_name,
            'start_date' => $this->year_start_date,
            'end_date' => $this->year_end_date,
            'description' => $this->year_description,
            'is_current' => $this->year_is_current,
            'status' => 'ACTIVE',
        ]);

        $this->isFormOpen = false;
        $this->loadData();
    }

    public function makeCurrentYear($id)
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId) {
            AcademicYear::where('tenant_id', $tenantId)->update(['is_current' => false]);
            AcademicYear::where('id', $id)->update(['is_current' => true]);
        }
        $this->loadData();
    }

    public function saveSession()
    {
        $this->validate([
            'selectedSessionYearId' => 'required|uuid',
            'session_name' => 'required|string|max:100',
            'session_start_date' => 'required|date',
            'session_end_date' => 'required|date|after:session_start_date',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($this->session_is_current && $tenantId) {
            AcademicSession::where('tenant_id', $tenantId)->update(['is_current' => false]);
        }

        AcademicSession::create([
            'tenant_id' => $tenantId,
            'academic_year_id' => $this->selectedSessionYearId,
            'name' => $this->session_name,
            'type' => $this->session_type,
            'start_date' => $this->session_start_date,
            'end_date' => $this->session_end_date,
            'is_current' => $this->session_is_current,
            'status' => $this->session_status,
        ]);

        $this->isFormOpen = false;
        $this->loadData();
    }

    public function makeCurrentSession($id)
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId) {
            AcademicSession::where('tenant_id', $tenantId)->update(['is_current' => false]);
            AcademicSession::where('id', $id)->update(['is_current' => true]);
        }
        $this->loadData();
    }

    public function saveFaculty()
    {
        $this->validate([
            'faculty_name' => 'required|string|max:100',
            'faculty_code' => 'required|string|max:20',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        Faculty::create([
            'tenant_id' => $tenantId,
            'name' => $this->faculty_name,
            'code' => $this->faculty_code,
            'description' => $this->faculty_description,
            'head_user_id' => $this->faculty_head_user_id ?: null,
            'status' => 'ACTIVE',
        ]);

        $this->isFormOpen = false;
        $this->loadData();
    }

    public function saveGradingRule()
    {
        $this->validate([
            'grade_letter' => 'required|string|max:5',
            'grade_min_score' => 'required|numeric|min:0|max:100',
            'grade_max_score' => 'required|numeric|gt:grade_min_score|max:100',
            'grade_points' => 'required|numeric|min:0|max:4.00',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        GradingRule::create([
            'tenant_id' => $tenantId,
            'grade_letter' => $this->grade_letter,
            'min_score' => $this->grade_min_score,
            'max_score' => $this->grade_max_score,
            'grade_points' => $this->grade_points,
            'status' => $this->grade_status,
        ]);

        $this->isFormOpen = false;
        $this->loadData();
    }

    public function saveCgpaRule()
    {
        $this->validate([
            'cgpa_standing_name' => 'required|string|max:100',
            'cgpa_min' => 'required|numeric|min:0|max:4.00',
            'cgpa_max' => 'required|numeric|gt:cgpa_min|max:4.00',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        CgpaRule::create([
            'tenant_id' => $tenantId,
            'standing_name' => $this->cgpa_standing_name,
            'min_cgpa' => $this->cgpa_min,
            'max_cgpa' => $this->cgpa_max,
            'status_tag' => $this->cgpa_status_tag,
            'description' => $this->cgpa_description,
        ]);

        $this->isFormOpen = false;
        $this->loadData();
    }

    public function saveCalendarEvent()
    {
        $this->validate([
            'event_year_id' => 'required|uuid',
            'event_title' => 'required|string|max:150',
            'event_date' => 'required|date',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        AcademicCalendar::create([
            'tenant_id' => $tenantId,
            'academic_year_id' => $this->event_year_id,
            'academic_session_id' => $this->event_session_id ?: null,
            'title' => $this->event_title,
            'event_date' => $this->event_date,
            'event_end_date' => $this->event_end_date ?: null,
            'event_type' => $this->event_type,
            'description' => $this->event_description,
            'is_holiday' => $this->event_is_holiday,
            'color' => $this->event_color,
        ]);

        $this->isFormOpen = false;
        $this->loadData();
    }

    public function deleteItem(string $model, string $id)
    {
        $class = "App\\Models\\" . $model;
        if (class_exists($class)) {
            $class::destroy($id);
        }
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.academic.academic-year-manager')
            ->layout('layouts.app');
    }
}
