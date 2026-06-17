<?php

namespace App\Livewire\Lms;

use Livewire\Component;
use App\Models\VirtualClassroom as VirtualClassroomModel;
use App\Models\Course;
use Illuminate\Support\Str;

class VirtualClassroom extends Component
{
    public $meetings = [];
    public $courses = [];
    
    // Scheduling form fields
    public string $selectedCourse = '';
    public string $topic = '';
    public string $description = '';
    public string $scheduledAt = '';
    public int $duration = 60;
    public bool $isScheduling = false;

    // Active Meeting State
    public ?VirtualClassroomModel $activeMeeting = null;

    protected array $rules = [
        'selectedCourse' => 'required|exists:courses,id',
        'topic' => 'required|string|max:200',
        'description' => 'nullable|string',
        'scheduledAt' => 'required|date',
        'duration' => 'required|integer|min:5|max:480',
    ];

    public function mount()
    {
        $this->loadMeetings();
    }

    public function loadMeetings()
    {
        $user = auth()->user();
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($tenantId) {
            // Auto-complete stale meetings scheduled in the past that are still LIVE
            VirtualClassroomModel::where('tenant_id', $tenantId)
                ->where('status', 'LIVE')
                ->where('scheduled_at', '<', now()->subHours(12))
                ->update(['status' => 'COMPLETED']);
        }

        if ($user->hasRole('Trainer')) {
            $this->meetings = VirtualClassroomModel::with('course', 'trainer')
                ->where('trainer_id', $user->id)
                ->orderBy('scheduled_at', 'desc')
                ->get();
        } elseif ($user->hasRole('Student')) {
            $enrolledCourseIds = \App\Models\Enrollment::where('user_id', $user->id)
                ->where('status', 'ACTIVE')
                ->pluck('course_id');

            $this->meetings = VirtualClassroomModel::with('course', 'trainer')
                ->whereIn('course_id', $enrolledCourseIds)
                ->orderBy('scheduled_at', 'desc')
                ->get();
        } else {
            $this->meetings = VirtualClassroomModel::with('course', 'trainer')
                ->orderBy('scheduled_at', 'desc')
                ->get();
        }

        $this->courses = Course::all();
    }

    public function scheduleMeeting()
    {
        $this->validate();

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($tenantId) {
            $meeting = VirtualClassroomModel::create([
                'tenant_id' => $tenantId,
                'course_id' => $this->selectedCourse,
                'trainer_id' => auth()->id(),
                'topic' => $this->topic,
                'description' => $this->description,
                'scheduled_at' => $this->scheduledAt,
                'duration_minutes' => $this->duration,
                'meeting_id' => 'nandskills_meet_' . Str::random(12),
                'status' => 'UPCOMING',
            ]);

            // Auto create announcement for course students
            \App\Models\Announcement::create([
                'tenant_id' => $tenantId,
                'branch_id' => \App\Models\Branch::first()->id ?? null,
                'title' => '🎥 New Virtual Class: ' . $this->topic,
                'content' => 'A live virtual session has been scheduled for "' . $meeting->course->title . '" on ' . date('M d, Y h:i A', strtotime($this->scheduledAt)) . '.',
                'target_roles' => ['Student'],
                'created_by_id' => auth()->id(),
            ]);
        }

        $this->resetInputFields();
        $this->loadMeetings();
        $this->isScheduling = false;
        session()->flash('status', 'Virtual classroom scheduled successfully!');
    }

    public function startMeeting(string $meetingId)
    {
        $meeting = VirtualClassroomModel::find($meetingId);
        if ($meeting) {
            $meeting->update(['status' => 'LIVE']);
        }
        $this->loadMeetings();
        $this->redirectRoute('meeting.session', ['meetingId' => $meetingId]);
    }

    public function joinMeeting(string $meetingId)
    {
        $this->redirectRoute('meeting.session', ['meetingId' => $meetingId]);
    }

    public function leaveMeeting()
    {
        $this->activeMeeting = null;
        $this->loadMeetings();
    }

    public function completeMeeting(string $meetingId)
    {
        $meeting = VirtualClassroomModel::find($meetingId);
        if ($meeting) {
            $meeting->update(['status' => 'COMPLETED']);
            if ($this->activeMeeting && $this->activeMeeting->id === $meetingId) {
                $this->activeMeeting = null;
            }
        }
        $this->loadMeetings();
    }

    private function resetInputFields()
    {
        $this->selectedCourse = '';
        $this->topic = '';
        $this->description = '';
        $this->scheduledAt = '';
        $this->duration = 60;
    }

    public function render()
    {
        return view('livewire.lms.virtual-classroom')
            ->layout('layouts.app');
    }
}
