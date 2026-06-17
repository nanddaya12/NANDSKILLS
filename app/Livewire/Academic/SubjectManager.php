<?php

namespace App\Livewire\Academic;

use Livewire\Component;
use App\Models\Subject;
use App\Models\Department;

class SubjectManager extends Component
{
    public $subjects = [];
    public $departments = [];

    public bool $isCreating = false;
    public string $subject_id = '';
    public string $department_id = '';
    public string $name = '';
    public string $code = '';
    public float $credit_hours = 3.0;
    public string $type = 'THEORY'; // THEORY, LAB, SEMINAR, PROJECT, INTERNSHIP
    public bool $is_elective = false;
    public string $description = '';

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Subject inventory is restricted.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->subjects = Subject::with('department')->get();
        // Fallback or load departments
        $this->departments = Department::all();
    }

    public function openCreate()
    {
        $this->resetInputFields();
        $this->isCreating = true;
    }

    public function resetInputFields()
    {
        $this->subject_id = '';
        $this->department_id = '';
        $this->name = '';
        $this->code = '';
        $this->credit_hours = 3.0;
        $this->type = 'THEORY';
        $this->is_elective = false;
        $this->description = '';
    }

    public function saveSubject()
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:30',
            'credit_hours' => 'required|numeric|min:0.5|max:10',
            'type' => 'required|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        Subject::updateOrCreate(
            ['id' => $this->subject_id ?: null],
            [
                'tenant_id' => $tenantId,
                'department_id' => $this->department_id ?: null,
                'name' => $this->name,
                'code' => $this->code,
                'credit_hours' => $this->credit_hours,
                'type' => $this->type,
                'is_elective' => $this->is_elective,
                'description' => $this->description,
                'status' => 'ACTIVE',
            ]
        );

        $this->isCreating = false;
        $this->loadData();
    }

    public function editSubject(string $id)
    {
        $subj = Subject::findOrFail($id);
        $this->subject_id = $subj->id;
        $this->department_id = $subj->department_id ?: '';
        $this->name = $subj->name;
        $this->code = $subj->code;
        $this->credit_hours = $subj->credit_hours;
        $this->type = $subj->type;
        $this->is_elective = (bool)$subj->is_elective;
        $this->description = $subj->description ?: '';

        $this->isCreating = true;
    }

    public function deleteSubject(string $id)
    {
        Subject::destroy($id);
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.academic.subject-manager')
            ->layout('layouts.app');
    }
}
