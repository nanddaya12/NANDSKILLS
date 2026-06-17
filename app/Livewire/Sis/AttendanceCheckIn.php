<?php

namespace App\Livewire\Sis;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Cache;

class AttendanceCheckIn extends Component
{
    public string $code = '';
    
    protected array $rules = [
        'code' => 'required|string|size:6',
    ];

    public function submitCheckIn()
    {
        $this->validate();

        $user = auth()->user();

        // 1. Authorization check
        if (!$user->hasRole('Student')) {
            session()->flash('error', 'Only student accounts can perform self-service check-in.');
            return;
        }

        // 2. Fetch code from cache
        $cacheKey = 'active_checkin_code_' . strtoupper($this->code);
        $meetingData = Cache::get($cacheKey);

        if (!$meetingData) {
            session()->flash('error', 'The check-in code is invalid, incorrect, or has expired.');
            return;
        }

        // 3. Match tenant (safety check)
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId && $meetingData['tenant_id'] !== $tenantId) {
            session()->flash('error', 'The check-in code belongs to a different academy instance.');
            return;
        }

        // 4. Verify student profile
        $studentProfile = StudentProfile::where('user_id', $user->id)->first();
        if (!$studentProfile) {
            session()->flash('error', 'Student registry profile not found for this account.');
            return;
        }

        // 5. Check duplicate check-ins
        $existing = Attendance::where('user_id', $user->id)
            ->where('date', $meetingData['date'])
            ->where('branch_id', $meetingData['branch_id'])
            ->first();

        if ($existing && $existing->status === 'PRESENT') {
            session()->flash('info', 'You have already checked in for today!');
            $this->code = '';
            return;
        }

        // 6. Log attendance
        Attendance::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'branch_id' => $meetingData['branch_id'],
                'date' => $meetingData['date'],
                'user_id' => $user->id,
            ],
            [
                'status' => 'PRESENT',
                'marked_by_id' => $meetingData['marked_by_id'],
                'qr_code' => strtoupper($this->code),
                'ip_address' => request()->ip(),
            ]
        );

        $this->code = '';
        session()->flash('status', 'Check-in successful! Your attendance has been logged.');
    }

    public function render()
    {
        return view('livewire.sis.attendance-check-in')
            ->layout('layouts.app');
    }
}
