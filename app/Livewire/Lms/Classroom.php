<?php

namespace App\Livewire\Lms;

use Livewire\Component;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonNote;

class Classroom extends Component
{
    public $courses = [];
    public ?Course $activeCourse = null;
    public ?Lesson $activeLesson = null;
    public string $newNoteContent = '';
    public string $activeTab = 'lesson'; // lesson, notes, resources
    public string $view = 'classroom';   // classroom | catalog
    public $availableCourses = [];

    public function mount()
    {
        $this->loadEnrollments();
        if (count($this->courses) === 0) {
            $this->view = 'catalog';
            $this->loadCatalog();
        }
    }

    public function loadCatalog()
    {
        $enrolledIds = collect($this->courses)->pluck('id')->toArray();
        $this->availableCourses = Course::where('status', 'PUBLISHED')
            ->whereNotIn('id', $enrolledIds)
            ->get();
    }

    public function setView(string $view)
    {
        $this->view = $view;
        if ($view === 'catalog') {
            $this->loadCatalog();
        }
    }

    public function enrollInCourse(string $courseId)
    {
        $user = auth()->user();
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        \App\Models\Enrollment::create([
            'tenant_id' => $tenantId,
            'user_id' => $user->id,
            'course_id' => $courseId,
            'progress_percent' => 0,
            'status' => 'ACTIVE',
            'enrolled_at' => now(),
        ]);

        session()->flash('status', 'Enrolled in course successfully!');
        $this->loadEnrollments();
        $this->view = 'classroom';
    }

    public function loadEnrollments()
    {
        $user = auth()->user();
        $this->courses = Course::whereHas('enrollments', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('chapters.lessons')->get();

        if ($this->courses->isNotEmpty()) {
            $this->selectCourse($this->courses->first()->id);
        }
    }

    public function selectCourse(string $courseId)
    {
        $this->activeCourse = Course::with('chapters.lessons')->find($courseId);
        if ($this->activeCourse && $this->activeCourse->chapters->isNotEmpty()) {
            $firstChapter = $this->activeCourse->chapters->first();
            if ($firstChapter->lessons->isNotEmpty()) {
                $this->selectLesson($firstChapter->lessons->first()->id);
            }
        }
    }

    public function selectLesson(string $lessonId)
    {
        $this->activeLesson = Lesson::find($lessonId);
        $this->newNoteContent = '';
    }

    public function saveNote()
    {
        if (empty($this->newNoteContent)) {
            return;
        }

        LessonNote::create([
            'lesson_id' => $this->activeLesson->id,
            'user_id' => auth()->id(),
            'content' => $this->newNoteContent,
        ]);

        $this->newNoteContent = '';
        $this->activeLesson->load('notes');
    }

    public function completeLesson()
    {
        $user = auth()->user();
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $this->activeCourse->id)
            ->first();

        if ($enrollment) {
            // Calculate progress increment
            $totalLessons = Lesson::whereHas('chapter', function($q) {
                $q->where('course_id', $this->activeCourse->id);
            })->count();

            if ($totalLessons > 0) {
                $currentProgress = $enrollment->progress_percent;
                $increment = floor(100 / $totalLessons);
                $newProgress = min(100, $currentProgress + $increment);
                $enrollment->update([
                    'progress_percent' => $newProgress,
                    'status' => $newProgress === 100 ? 'COMPLETED' : 'ACTIVE',
                    'completed_at' => $newProgress === 100 ? now() : null,
                ]);
            }
        }

        // Auto advance to next lesson in chapter
        $currentChapterLessons = $this->activeLesson->chapter->lessons;
        $currentIndex = $currentChapterLessons->pluck('id')->search($this->activeLesson->id);

        if ($currentIndex !== false && isset($currentChapterLessons[$currentIndex + 1])) {
            $this->selectLesson($currentChapterLessons[$currentIndex + 1]->id);
        }
    }

    public function render()
    {
        return view('livewire.lms.classroom')
            ->layout('layouts.app');
    }
}
