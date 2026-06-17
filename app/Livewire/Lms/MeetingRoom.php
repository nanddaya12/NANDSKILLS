<?php

namespace App\Livewire\Lms;

use Livewire\Component;
use App\Models\VirtualClassroom as VirtualClassroomModel;
use Illuminate\Support\Facades\Auth;

class MeetingRoom extends Component
{
    public string $meetingId;
    public ?VirtualClassroomModel $meeting = null;
    public bool $isHost = false;

    public function mount(string $meetingId)
    {
        // Fetch meeting by primary key ID or room meeting_id string
        $this->meeting = VirtualClassroomModel::where('id', $meetingId)
            ->orWhere('meeting_id', $meetingId)
            ->firstOrFail();

        $user = Auth::user();

        // Security check: must belong to the active tenant
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId && $this->meeting->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized access to this classroom.');
        }

        // Security check: student must be enrolled in this course to join
        if ($user->hasRole('Student')) {
            $isEnrolled = \App\Models\Enrollment::where('user_id', $user->id)
                ->where('course_id', $this->meeting->course_id)
                ->where('status', 'ACTIVE')
                ->exists();

            if (!$isEnrolled) {
                abort(403, 'Access denied. You are not enrolled in this course.');
            }
        }

        // Determine if user is host
        $this->isHost = ($user->id === $this->meeting->trainer_id) || $user->hasRole(['Tenant Admin', 'Super Admin']);

        // Auto-start upcoming meeting if host enters
        if ($this->meeting->status === 'UPCOMING' && $this->isHost) {
            $this->meeting->update(['status' => 'LIVE']);
        }

        // Prevent joining completed meetings
        if ($this->meeting->status === 'COMPLETED') {
            session()->flash('status', 'This classroom session has already ended.');
            return redirect()->route('meetings.index');
        }

        // Auto-mark attendance for students who join the live class
        if ($user->hasRole('Student') && $this->meeting->status === 'LIVE') {
            \App\Models\Attendance::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'date' => now()->toDateString(),
                    'user_id' => $user->id,
                ],
                [
                    'branch_id' => \App\Models\Branch::first()->id ?? null,
                    'status' => 'PRESENT',
                    'marked_by_id' => $this->meeting->trainer_id,
                    'ip_address' => request()->ip(),
                ]
            );
        }
    }

    public function leaveMeeting()
    {
        return redirect()->route('meetings.index');
    }

    public function endMeeting()
    {
        if ($this->isHost && $this->meeting) {
            $this->meeting->update(['status' => 'COMPLETED']);
        }
        return redirect()->route('meetings.index');
    }

    public function render()
    {
        return view('livewire.lms.meeting-room')
            ->layout('layouts.meeting');
    }
}
