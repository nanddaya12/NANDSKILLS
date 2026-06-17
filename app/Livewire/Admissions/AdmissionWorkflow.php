<?php

namespace App\Livewire\Admissions;

use Livewire\Component;
use App\Models\AdmissionForm;
use App\Models\Program;
use App\Models\AdmissionWorkflowLog;

class AdmissionWorkflow extends Component
{
    public $forms = [];
    public $programs = [];
    public $logs = [];

    public bool $isCreating = false;
    public string $form_id = '';
    public string $program_id = '';
    public string $title = '';
    public string $description = '';
    public string $open_date = '';
    public string $close_date = '';
    public float $application_fee = 0.00;

    // Field configuration builder
    public array $fields = [];
    public string $new_field_name = '';
    public string $new_field_label = '';
    public string $new_field_type = 'text'; // text, textarea, select, number

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Workflow config is restricted.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->forms = AdmissionForm::with('program')->get();
        $this->programs = Program::where('status', 'ACTIVE')->get();
        $this->logs = AdmissionWorkflowLog::with('admission', 'changedByUser')->orderBy('created_at', 'desc')->take(30)->get();
    }

    public function openCreate()
    {
        $this->resetInputFields();
        $this->isCreating = true;
    }

    private function resetInputFields()
    {
        $this->form_id = '';
        $this->program_id = '';
        $this->title = '';
        $this->description = '';
        $this->open_date = '';
        $this->close_date = '';
        $this->application_fee = 0.00;
        $this->fields = [];
        $this->new_field_name = '';
        $this->new_field_label = '';
        $this->new_field_type = 'text';
    }

    public function addField()
    {
        $this->validate([
            'new_field_name' => 'required|alpha_dash|max:50',
            'new_field_label' => 'required|string|max:100',
        ]);

        $this->fields[] = [
            'name' => $this->new_field_name,
            'label' => $this->new_field_label,
            'type' => $this->new_field_type,
        ];

        $this->new_field_name = '';
        $this->new_field_label = '';
        $this->new_field_type = 'text';
    }

    public function removeField(int $index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function saveForm()
    {
        $this->validate([
            'program_id' => 'required|uuid',
            'title' => 'required|string|max:150',
            'open_date' => 'required|date',
            'close_date' => 'required|date|after:open_date',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        AdmissionForm::updateOrCreate(
            ['id' => $this->form_id ?: null],
            [
                'tenant_id' => $tenantId,
                'program_id' => $this->program_id,
                'title' => $this->title,
                'description' => $this->description,
                'fields_config' => $this->fields,
                'is_active' => true,
                'application_open_date' => $this->open_date,
                'application_close_date' => $this->close_date,
                'application_fee' => $this->application_fee,
            ]
        );

        $this->isCreating = false;
        $this->loadData();
    }

    public function editForm(string $id)
    {
        $f = AdmissionForm::findOrFail($id);
        $this->form_id = $f->id;
        $this->program_id = $f->program_id;
        $this->title = $f->title;
        $this->description = $f->description ?: '';
        $this->open_date = $f->application_open_date ? $f->application_open_date->toDateString() : '';
        $this->close_date = $f->application_close_date ? $f->application_close_date->toDateString() : '';
        $this->application_fee = $f->application_fee;
        $this->fields = $f->fields_config ?: [];

        $this->isCreating = true;
    }

    public function toggleFormStatus(string $id)
    {
        $f = AdmissionForm::findOrFail($id);
        $f->update(['is_active' => !$f->is_active]);
        $this->loadData();
    }

    public function deleteForm(string $id)
    {
        AdmissionForm::destroy($id);
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admissions.admission-workflow')
            ->layout('layouts.app');
    }
}
