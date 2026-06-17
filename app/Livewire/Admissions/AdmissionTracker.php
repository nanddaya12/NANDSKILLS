<?php

namespace App\Livewire\Admissions;

use Livewire\Component;
use App\Models\Admission;
use App\Models\AdmissionWorkflowLog;
use App\Models\AdmissionInterview;

class AdmissionTracker extends Component
{
    public $admissions = [];
    public $statuses = ['SUBMITTED', 'UNDER_REVIEW', 'INTERVIEW_SCHEDULED', 'APPROVED', 'REJECTED', 'ENROLLED'];

    // Interview scheduler variables
    public bool $isSchedulingInterview = false;
    public string $selectedAdmissionId = '';
    public string $interview_date = '';
    public string $interview_format = 'ONLINE'; // ONLINE, IN_PERSON
    public string $interview_link = '';

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Admissions Tracker is restricted to staff.');
        }

        $this->loadAdmissions();
    }

    public function loadAdmissions()
    {
        $this->admissions = Admission::with('program', 'documents')->orderBy('updated_at', 'desc')->get();
    }

    public function updateStatus(string $id, string $newStatus)
    {
        $admission = Admission::findOrFail($id);
        $oldStatus = $admission->status;

        if ($oldStatus !== $newStatus) {
            $admission->update([
                'status' => $newStatus,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            AdmissionWorkflowLog::create([
                'admission_id' => $admission->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => auth()->id(),
                'notes' => 'Status transitioned via tracker board.',
            ]);

            // If promoting to ENROLLED, we can automatically convert them to a student!
            if ($newStatus === 'ENROLLED' && !$admission->created_user_id) {
                $this->enrollApplicantAsStudent($admission);
            }
        }

        $this->loadAdmissions();
    }

    private function enrollApplicantAsStudent(Admission $admission)
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId) {
            // Check if user already exists
            $user = \App\Models\User::where('tenant_id', $tenantId)->where('email', $admission->email)->first();

            if (!$user) {
                // Create user login
                $user = \App\Models\User::create([
                    'tenant_id' => $tenantId,
                    'email' => $admission->email,
                    'password_hash' => \Illuminate\Support\Facades\Hash::make('welcome_secure_2026'),
                    'first_name' => explode(' ', $admission->applicant_name)[0],
                    'last_name' => strstr($admission->applicant_name, ' ') ?: 'Student',
                    'is_email_verified' => true,
                    'status' => 'ACTIVE',
                ]);

                // Attach role
                $studentRole = \App\Models\Role::where('tenant_id', $tenantId)->where('name', 'Student')->first();
                if ($studentRole) {
                    $user->roles()->attach($studentRole->id);
                }
            }

            // Create student profile if not exists
            $profile = \App\Models\StudentProfile::where('user_id', $user->id)->first();
            if (!$profile) {
                \App\Models\StudentProfile::create([
                    'user_id' => $user->id,
                    'tenant_id' => $tenantId,
                    'roll_number' => 'ROLL-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'admission_date' => now()->toDateString(),
                    'status' => 'ACTIVE',
                ]);
            }

            // Update admission entry with user link
            $admission->update(['created_user_id' => $user->id]);
        }
    }

    public function openInterviewModal(string $id)
    {
        $this->selectedAdmissionId = $id;
        $this->isSchedulingInterview = true;
    }

    public function scheduleInterview()
    {
        $this->validate([
            'interview_date' => 'required',
            'interview_format' => 'required',
        ]);

        AdmissionInterview::create([
            'admission_id' => $this->selectedAdmissionId,
            'scheduled_at' => $this->interview_date,
            'format' => $this->interview_format,
            'meeting_link' => $this->interview_link ?: null,
            'status' => 'SCHEDULED',
        ]);

        // Transition status
        $this->updateStatus($this->selectedAdmissionId, 'INTERVIEW_SCHEDULED');

        $this->isSchedulingInterview = false;
        $this->reset(['selectedAdmissionId', 'interview_date', 'interview_format', 'interview_link']);
        $this->loadAdmissions();
    }

    public function render()
    {
        return view('livewire.admissions.admission-tracker')
            ->layout('layouts.app');
    }
}
