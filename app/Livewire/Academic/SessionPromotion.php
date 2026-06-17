<?php

namespace App\Livewire\Academic;

use Livewire\Component;
use App\Models\Program;
use App\Models\AcademicSession;
use App\Models\StudentSessionEnrollment;

class SessionPromotion extends Component
{
    public $programs = [];
    public $sessions = [];
    public $students = [];

    public string $selectedProgramId = '';
    public string $selectedSessionId = '';
    public int $selectedSemester = 1;

    // Promotion target
    public string $targetSessionId = '';
    public int $targetSemester = 2;
    public array $selectedStudentIds = [];

    public string $promotionStatusMessage = '';

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Session Promotion is restricted to Institutional Admins.');
        }

        $this->programs = Program::where('status', 'ACTIVE')->get();
        $this->sessions = AcademicSession::orderBy('start_date', 'desc')->get();
    }

    public function loadStudents()
    {
        $this->validate([
            'selectedProgramId' => 'required|uuid',
            'selectedSessionId' => 'required|uuid',
            'selectedSemester' => 'required|integer',
        ]);

        $this->students = StudentSessionEnrollment::with('student')
            ->where('program_id', $this->selectedProgramId)
            ->where('academic_session_id', $this->selectedSessionId)
            ->where('current_semester', $this->selectedSemester)
            ->get();

        $this->selectedStudentIds = [];
        $this->promotionStatusMessage = '';
    }

    public function promoteStudents()
    {
        $this->validate([
            'targetSessionId' => 'required|uuid',
            'targetSemester' => 'required|integer',
        ]);

        if (empty($this->selectedStudentIds)) {
            $this->addError('promotion', 'Please select at least one student to promote.');
            return;
        }

        $count = 0;
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        foreach ($this->selectedStudentIds as $enrollmentId) {
            $currentEnrollment = StudentSessionEnrollment::find($enrollmentId);

            if ($currentEnrollment) {
                // Update current status to PROMOTED
                $currentEnrollment->update(['status' => 'PROMOTED']);

                // Create new enrollment for the target session/semester
                StudentSessionEnrollment::create([
                    'tenant_id' => $tenantId,
                    'academic_session_id' => $this->targetSessionId,
                    'student_user_id' => $currentEnrollment->student_user_id,
                    'program_id' => $currentEnrollment->program_id,
                    'current_semester' => $this->targetSemester,
                    'cumulative_cgpa' => $currentEnrollment->cumulative_cgpa,
                    'credits_earned' => $currentEnrollment->credits_earned,
                    'academic_standing' => $currentEnrollment->academic_standing,
                    'status' => 'ENROLLED',
                ]);

                $count++;
            }
        }

        $this->promotionStatusMessage = "Successfully promoted $count students to Semester $this->targetSemester!";
        $this->loadStudents();
    }

    public function selectAll()
    {
        $this->selectedStudentIds = collect($this->students)->pluck('id')->toArray();
    }

    public function deselectAll()
    {
        $this->selectedStudentIds = [];
    }

    public function render()
    {
        return view('livewire.academic.session-promotion')
            ->layout('layouts.app');
    }
}
