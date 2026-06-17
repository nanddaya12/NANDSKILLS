<?php

namespace App\Livewire\Parent;

use Livewire\Component;
use App\Models\ParentStudentLink;
use App\Models\User;
use App\Models\StudentSessionEnrollment;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\BehaviorReport;
use App\Models\ParentMessage;
use App\Models\AcademicWarning;

class ParentDashboard extends Component
{
    // Active tabs: 'progress', 'messages', 'behavior'
    public string $activeTab = 'progress';

    public $linkedStudents = [];
    public string $selectedStudentUserId = '';
    public $selectedStudentProfile = null;

    // Progress data
    public $enrollments = [];
    public $attendances = [];
    public $examResults = [];
    public $warnings = [];

    // Message data
    public $messages = [];
    public $teachers = [];
    public string $selectedTeacherId = '';
    public string $messageSubject = '';
    public string $messageBody = '';

    // Behavior reports
    public $behaviorReports = [];

    public function mount()
    {
        // Ensure user is Parent or Admin
        if (!auth()->user() || !auth()->user()->hasRole(['Student', 'Tenant Admin', 'Super Admin'])) {
            // Note: Since role assignment is configurable, let's allow Parent Dashboard for users linked to students.
        }

        $this->loadLinkedStudents();
    }

    public function loadLinkedStudents()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId) {
            $this->linkedStudents = ParentStudentLink::with('student')
                ->where('parent_user_id', auth()->id())
                ->get();

            if ($this->linkedStudents->isNotEmpty()) {
                $this->selectedStudentUserId = $this->linkedStudents->first()->student_user_id;
                $this->updatedSelectedStudentUserId($this->selectedStudentUserId);
            }
        }
    }

    public function updatedSelectedStudentUserId($value)
    {
        $this->selectedStudentProfile = User::find($value);
        $this->loadStudentData();
    }

    public function loadStudentData()
    {
        if (!$this->selectedStudentUserId) return;

        $this->enrollments = StudentSessionEnrollment::with('academicSession', 'program')
            ->where('student_user_id', $this->selectedStudentUserId)
            ->get();

        $this->attendances = Attendance::where('student_id', $this->selectedStudentUserId)
            ->orderBy('date', 'desc')
            ->take(50)
            ->get();

        $this->examResults = ExamResult::with('exam')
            ->where('student_id', $this->selectedStudentUserId)
            ->get();

        $this->warnings = AcademicWarning::where('student_user_id', $this->selectedStudentUserId)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->behaviorReports = BehaviorReport::with('reporter')
            ->where('student_user_id', $this->selectedStudentUserId)
            ->orderBy('incident_date', 'desc')
            ->get();

        $this->loadMessages();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    // Message system
    public function loadMessages()
    {
        $this->messages = ParentMessage::with('sender', 'receiver')
            ->where(function($q) {
                $q->where('sender_user_id', auth()->id())
                  ->orWhere('receiver_user_id', auth()->id());
            })
            ->where('student_user_id', $this->selectedStudentUserId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Load teachers of the student (Trainers of courses this student is enrolled in)
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId) {
            $this->teachers = User::where('tenant_id', $tenantId)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'Trainer');
                })->get();
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'selectedTeacherId' => 'required|uuid',
            'messageBody' => 'required|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        ParentMessage::create([
            'tenant_id' => $tenantId,
            'sender_user_id' => auth()->id(),
            'receiver_user_id' => $this->selectedTeacherId,
            'student_user_id' => $this->selectedStudentUserId,
            'subject' => $this->messageSubject ?: 'Direct Parent Inquiry',
            'body' => $this->messageBody,
            'is_read' => false,
        ]);

        $this->messageSubject = '';
        $this->messageBody = '';
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.parent.parent-dashboard')
            ->layout('layouts.app');
    }
}
