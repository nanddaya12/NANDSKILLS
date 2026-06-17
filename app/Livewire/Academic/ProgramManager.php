<?php

namespace App\Livewire\Academic;

use Livewire\Component;
use App\Models\Program;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\ProgramSubject;

class ProgramManager extends Component
{
    public $programs = [];
    public $faculties = [];
    public $subjects = [];

    // Creating/editing program
    public bool $isCreating = false;
    public string $program_id = '';
    public string $faculty_id = '';
    public string $name = '';
    public string $code = '';
    public string $degree_type = 'BACHELOR'; // CERTIFICATE, DIPLOMA, BACHELOR, MASTER, PHD
    public int $duration_years = 4;
    public int $total_semesters = 8;
    public float $credit_hours_required = 130;
    public float $min_cgpa_required = 2.00;
    public string $description = '';

    // Curriculum builder
    public bool $isCurriculumOpen = false;
    public $selectedProgram = null;
    public $mappedSubjects = [];
    public string $new_subject_id = '';
    public int $new_semester_no = 1;
    public bool $new_is_elective = false;
    public bool $new_is_prerequisite_required = false;
    public string $new_prerequisite_subject_id = '';

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Programs and Curriculum management is restricted.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->programs = Program::with('faculty')->get();
        $this->faculties = Faculty::where('status', 'ACTIVE')->get();
        $this->subjects = Subject::where('status', 'ACTIVE')->get();
    }

    public function openCreate()
    {
        $this->resetInputFields();
        $this->isCreating = true;
        $this->isCurriculumOpen = false;
    }

    public function resetInputFields()
    {
        $this->program_id = '';
        $this->faculty_id = '';
        $this->name = '';
        $this->code = '';
        $this->degree_type = 'BACHELOR';
        $this->duration_years = 4;
        $this->total_semesters = 8;
        $this->credit_hours_required = 130;
        $this->min_cgpa_required = 2.00;
        $this->description = '';
    }

    public function saveProgram()
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:30',
            'degree_type' => 'required|string',
            'duration_years' => 'required|integer|min:1',
            'total_semesters' => 'required|integer|min:1',
            'credit_hours_required' => 'required|numeric|min:1',
            'min_cgpa_required' => 'required|numeric|min:0|max:4.0',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        Program::updateOrCreate(
            ['id' => $this->program_id ?: null],
            [
                'tenant_id' => $tenantId,
                'faculty_id' => $this->faculty_id ?: null,
                'name' => $this->name,
                'code' => $this->code,
                'degree_type' => $this->degree_type,
                'duration_years' => $this->duration_years,
                'total_semesters' => $this->total_semesters,
                'credit_hours_required' => $this->credit_hours_required,
                'min_cgpa_required' => $this->min_cgpa_required,
                'description' => $this->description,
                'status' => 'ACTIVE',
            ]
        );

        $this->isCreating = false;
        $this->loadData();
    }

    public function editProgram(string $id)
    {
        $prog = Program::findOrFail($id);
        $this->program_id = $prog->id;
        $this->faculty_id = $prog->faculty_id ?: '';
        $this->name = $prog->name;
        $this->code = $prog->code;
        $this->degree_type = $prog->degree_type;
        $this->duration_years = $prog->duration_years;
        $this->total_semesters = $prog->total_semesters;
        $this->credit_hours_required = $prog->credit_hours_required;
        $this->min_cgpa_required = $prog->min_cgpa_required;
        $this->description = $prog->description ?: '';

        $this->isCreating = true;
        $this->isCurriculumOpen = false;
    }

    public function deleteProgram(string $id)
    {
        Program::destroy($id);
        $this->loadData();
    }

    // Curriculum building logic
    public function manageCurriculum(string $id)
    {
        $this->selectedProgram = Program::findOrFail($id);
        $this->isCurriculumOpen = true;
        $this->isCreating = false;
        $this->loadCurriculum();
    }

    public function loadCurriculum()
    {
        if ($this->selectedProgram) {
            $this->mappedSubjects = ProgramSubject::with('subject', 'prerequisiteSubject')
                ->where('program_id', $this->selectedProgram->id)
                ->orderBy('semester_no')
                ->get();
        }
    }

    public function addSubjectToCurriculum()
    {
        $this->validate([
            'new_subject_id' => 'required|uuid',
            'new_semester_no' => 'required|integer|min:1|max:' . ($this->selectedProgram->total_semesters ?? 10),
        ]);

        // Check if already mapped
        $exists = ProgramSubject::where('program_id', $this->selectedProgram->id)
            ->where('subject_id', $this->new_subject_id)
            ->where('semester_no', $this->new_semester_no)
            ->exists();

        if (!$exists) {
            ProgramSubject::create([
                'program_id' => $this->selectedProgram->id,
                'subject_id' => $this->new_subject_id,
                'semester_no' => $this->new_semester_no,
                'is_elective' => $this->new_is_elective,
                'is_prerequisite_required' => $this->new_is_prerequisite_required,
                'prerequisite_subject_id' => $this->new_is_prerequisite_required && $this->new_prerequisite_subject_id ? $this->new_prerequisite_subject_id : null,
            ]);
        }

        // Reset inputs
        $this->new_subject_id = '';
        $this->new_semester_no = 1;
        $this->new_is_elective = false;
        $this->new_is_prerequisite_required = false;
        $this->new_prerequisite_subject_id = '';

        $this->loadCurriculum();
    }

    public function removeSubjectFromCurriculum(string $id)
    {
        ProgramSubject::destroy($id);
        $this->loadCurriculum();
    }

    public function render()
    {
        return view('livewire.academic.program-manager')
            ->layout('layouts.app');
    }
}
