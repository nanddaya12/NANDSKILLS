<?php

namespace App\Livewire\Lms;

use Livewire\Component;
use App\Models\Course;
use Illuminate\Support\Str;

class CourseAdmin extends Component
{
    public $courses = [];
    public string $title = '';
    public string $description = '';
    public string $cover_image_url = '';
    public float $price = 0.00;
    public string $language = 'English';
    public bool $isCreating = false;
    public ?string $editCourseId = null;

    protected array $rules = [
        'title' => 'required|string|max:150',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'language' => 'required|string',
        'cover_image_url' => 'nullable|url',
    ];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Unauthorized action. Course administration is restricted.');
        }
        $this->loadCourses();
    }

    public function loadCourses()
    {
        $this->courses = Course::withCount('chapters')->get();
    }

    public function createCourse()
    {
        $this->validate();

        Course::create([
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'description' => $this->description,
            'price' => $this->price,
            'language' => $this->language,
            'cover_image_url' => $this->cover_image_url ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500',
            'status' => 'DRAFT',
        ]);

        $this->resetInputFields();
        $this->loadCourses();
        $this->isCreating = false;
    }

    public function editCourse(string $id)
    {
        $course = Course::find($id);
        if ($course) {
            $this->editCourseId = $course->id;
            $this->title = $course->title;
            $this->description = $course->description ?? '';
            $this->price = $course->price;
            $this->language = $course->language;
            $this->cover_image_url = $course->cover_image_url ?? '';
            $this->isCreating = true;
        }
    }

    public function updateCourse()
    {
        $this->validate();

        $course = Course::find($this->editCourseId);
        if ($course) {
            $course->update([
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'description' => $this->description,
                'price' => $this->price,
                'language' => $this->language,
                'cover_image_url' => $this->cover_image_url ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500',
            ]);
        }

        $this->resetInputFields();
        $this->loadCourses();
        $this->isCreating = false;
    }

    public function deleteCourse(string $id)
    {
        $course = Course::find($id);
        if ($course) {
            $course->delete();
        }
        $this->loadCourses();
    }

    private function resetInputFields()
    {
        $this->title = '';
        $this->description = '';
        $this->price = 0.00;
        $this->language = 'English';
        $this->cover_image_url = '';
        $this->editCourseId = null;
    }

    public function render()
    {
        return view('livewire.lms.course-admin')
            ->layout('layouts.app');
    }
}
