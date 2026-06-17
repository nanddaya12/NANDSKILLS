<?php

namespace App\Livewire\Sis;

use Livewire\Component;
use App\Models\Branch;
use App\Models\StudentProfile;
use App\Models\Attendance;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AttendanceManager extends Component
{
    public $branches = [];
    public string $selectedBranch = '';
    public string $selectedDate = '';
    
    // QR / Code Check-in fields
    public string $activeCheckInCode = '';
    public bool $hasActiveCode = false;

    // Listen for updates to reload
    protected $listeners = ['refreshAttendance' => 'loadAttendance'];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Unauthorized action. Attendance management is restricted.');
        }

        $this->selectedDate = date('Y-m-d');
        $this->loadBranches();
        
        if (!empty($this->branches)) {
            $this->selectedBranch = $this->branches[0]->id;
        }

        // Check if there's an existing active check-in code in cache for the current user and branch
        $this->checkExistingCode();
    }

    public function loadBranches()
    {
        $this->branches = Branch::where('status', 'ACTIVE')->get();
    }

    public function checkExistingCode()
    {
        if ($this->selectedBranch) {
            $cacheKey = 'active_checkin_by_trainer_' . auth()->id() . '_' . $this->selectedBranch;
            $code = Cache::get($cacheKey);
            if ($code && Cache::has('active_checkin_code_' . $code)) {
                $this->activeCheckInCode = $code;
                $this->hasActiveCode = true;
            } else {
                $this->activeCheckInCode = '';
                $this->hasActiveCode = false;
            }
        }
    }

    public function updatedSelectedBranch()
    {
        $this->checkExistingCode();
    }

    public function updatedSelectedDate()
    {
        $this->checkExistingCode();
    }

    public function generateCheckInCode()
    {
        if (!$this->selectedBranch) {
            session()->flash('error', 'Please select a branch first.');
            return;
        }

        $code = strtoupper(Str::random(6));
        $cacheKey = 'active_checkin_code_' . $code;
        $trainerCacheKey = 'active_checkin_by_trainer_' . auth()->id() . '_' . $this->selectedBranch;

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $data = [
            'tenant_id' => $tenantId,
            'branch_id' => $this->selectedBranch,
            'date' => $this->selectedDate,
            'marked_by_id' => auth()->id(),
        ];

        // Store code in Cache for 30 minutes (1800 seconds)
        Cache::put($cacheKey, $data, 1800);
        Cache::put($trainerCacheKey, $code, 1800);

        $this->activeCheckInCode = $code;
        $this->hasActiveCode = true;

        session()->flash('status', 'Check-in code generated successfully! Direct students to check in with code: ' . $code);
    }

    public function clearCheckInCode()
    {
        if ($this->activeCheckInCode) {
            Cache::forget('active_checkin_code_' . $this->activeCheckInCode);
            Cache::forget('active_checkin_by_trainer_' . auth()->id() . '_' . $this->selectedBranch);
        }
        
        $this->activeCheckInCode = '';
        $this->hasActiveCode = false;
        session()->flash('status', 'Active check-in code deactivated.');
    }

    public function markAttendance($studentUserId, $status)
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        Attendance::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'branch_id' => $this->selectedBranch,
                'date' => $this->selectedDate,
                'user_id' => $studentUserId,
            ],
            [
                'status' => $status,
                'marked_by_id' => auth()->id(),
                'ip_address' => request()->ip(),
            ]
        );

        session()->flash('status', 'Attendance updated successfully.');
    }

    public function markAll($status)
    {
        if (!$this->selectedBranch) {
            session()->flash('error', 'Select a branch before performing bulk actions.');
            return;
        }

        $students = StudentProfile::all();
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        foreach ($students as $student) {
            Attendance::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'branch_id' => $this->selectedBranch,
                    'date' => $this->selectedDate,
                    'user_id' => $student->user_id,
                ],
                [
                    'status' => $status,
                    'marked_by_id' => auth()->id(),
                    'ip_address' => request()->ip(),
                ]
            );
        }

        session()->flash('status', 'All students marked as ' . $status);
    }

    public function render()
    {
        $studentsList = [];
        $attendanceMap = [];

        if ($this->selectedBranch) {
            // Fetch all student profiles in tenant
            $studentsList = StudentProfile::with('user')->get();

            // Fetch existing attendance records for the branch and date
            $records = Attendance::where('branch_id', $this->selectedBranch)
                ->where('date', $this->selectedDate)
                ->get();
                
            $attendanceMap = $records->keyBy('user_id')->toArray();
        }

        return view('livewire.sis.attendance-manager', [
            'students' => $studentsList,
            'attendanceRecords' => $attendanceMap
        ])->layout('layouts.app');
    }
}
