<?php

namespace App\Livewire\Lms;

use Livewire\Component;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class LearningPathManager extends Component
{
    public $courses = [];
    public $completedCourseIds = [];
    
    // Admin setup fields
    public ?string $selectedCourseId = null;
    public ?string $prerequisiteCourseId = null;
    
    public bool $isAdmin = false;

    public function mount()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $this->isAdmin = $user->hasRole(['Super Admin', 'Tenant Admin']);
        $this->loadData();
    }

    public function loadData()
    {
        $tenant = app('currentTenant');
        if (!$tenant) return;

        // Load all courses with their prerequisites
        $this->courses = Course::with('prerequisites')->get();

        // Load logged in student's completed courses
        $this->completedCourseIds = Enrollment::where('user_id', auth()->id())
            ->where('status', 'COMPLETED')
            ->pluck('course_id')
            ->toArray();
    }

    public function addPrerequisite()
    {
        if (!$this->isAdmin) return;

        $this->validate([
            'selectedCourseId' => 'required|exists:courses,id',
            'prerequisiteCourseId' => 'required|exists:courses,id|different:selectedCourseId',
        ]);

        $course = Course::find($this->selectedCourseId);
        
        // Add prerequisite using pivot table relation attach
        if ($course) {
            // Check if already exists to prevent duplication
            if (!$course->prerequisites()->where('prerequisite_id', $this->prerequisiteCourseId)->exists()) {
                $course->prerequisites()->attach($this->prerequisiteCourseId);
                session()->flash('success', 'Prerequisite course linked successfully.');
            } else {
                session()->flash('error', 'Prerequisite already linked to this course.');
            }
        }

        $this->prerequisiteCourseId = null;
        $this->loadData();
    }

    public function removePrerequisite($courseId, $prereqId)
    {
        if (!$this->isAdmin) return;

        $course = Course::find($courseId);
        if ($course) {
            $course->prerequisites()->detach($prereqId);
            session()->flash('success', 'Prerequisite course removed successfully.');
        }

        $this->loadData();
    }

    /**
     * Helper to verify if a course is locked for the current student.
     */
    public function isCourseLocked($course)
    {
        // Admins see everything unlocked
        if ($this->isAdmin) {
            return false;
        }

        // Check if student is already enrolled or completed
        $isCompleted = in_array($course->id, $this->completedCourseIds);
        if ($isCompleted) {
            return false;
        }

        // Check prerequisites
        foreach ($course->prerequisites as $prereq) {
            if (!in_array($prereq->id, $this->completedCourseIds)) {
                return true; // Missing completed prerequisite
            }
        }

        return false;
    }

    public function render()
    {
        return view('livewire.lms.learning-path-manager')
            ->layout('layouts.app');
    }
}
