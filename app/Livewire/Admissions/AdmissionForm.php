<?php

namespace App\Livewire\Admissions;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\AdmissionForm as FormModel;
use App\Models\Admission;
use App\Models\AdmissionDocument;
use Illuminate\Support\Str;

class AdmissionForm extends Component
{
    use WithFileUploads;

    public $activeForms = [];
    public string $selectedFormId = '';
    public $formConfig = null;

    // Static fields
    public string $applicant_name = '';
    public string $email = '';
    public string $phone = '';
    public string $date_of_birth = '';
    public string $gender = 'MALE';
    public string $nationality = '';
    public string $previous_qualification = '';
    public float $previous_grade = 0.0;

    // Dynamic field responses
    public array $dynamicResponses = [];

    // File attachments
    public $transcriptFile;
    public $idCardFile;

    public string $trackingNumber = '';
    public bool $isSubmitted = false;

    public function mount()
    {
        $this->activeForms = FormModel::with('program')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('application_close_date')
                  ->orWhere('application_close_date', '>=', now()->toDateString());
            })->get();
    }

    public function updatedSelectedFormId($value)
    {
        $this->formConfig = FormModel::find($value);
        $this->dynamicResponses = [];
        if ($this->formConfig && !empty($this->formConfig->fields_config)) {
            foreach ($this->formConfig->fields_config as $field) {
                $this->dynamicResponses[$field['name']] = '';
            }
        }
    }

    public function submitApplication()
    {
        $this->validate([
            'selectedFormId' => 'required|uuid',
            'applicant_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:30',
            'date_of_birth' => 'required|date',
            'nationality' => 'required|string|max:50',
            'previous_qualification' => 'required|string',
            'previous_grade' => 'required|numeric|min:0',
            'transcriptFile' => 'required|file|mimes:pdf,jpg,png|max:5120',
            'idCardFile' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        $appNo = 'ADM-' . now()->format('Y') . '-' . strtoupper(Str::random(6));

        // Create Admission Record
        $admission = Admission::create([
            'tenant_id' => $tenantId,
            'admission_form_id' => $this->selectedFormId,
            'program_id' => $this->formConfig->program_id,
            'applicant_name' => $this->applicant_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'nationality' => $this->nationality,
            'previous_qualification' => $this->previous_qualification,
            'previous_grade' => $this->previous_grade,
            'form_responses' => $this->dynamicResponses,
            'status' => 'SUBMITTED',
            'application_number' => $appNo,
            'submitted_at' => now(),
        ]);

        // Upload Transcript Document
        if ($this->transcriptFile) {
            $transcriptPath = $this->transcriptFile->store('admissions/transcripts', 'local');
            AdmissionDocument::create([
                'admission_id' => $admission->id,
                'document_type' => 'TRANSCRIPT',
                'file_path' => $transcriptPath,
                'original_filename' => $this->transcriptFile->getClientOriginalName(),
                'mime_type' => $this->transcriptFile->getMimeType(),
                'file_size' => $this->transcriptFile->getSize(),
            ]);
        }

        // Upload ID Card Document
        if ($this->idCardFile) {
            $idCardPath = $this->idCardFile->store('admissions/id_cards', 'local');
            AdmissionDocument::create([
                'admission_id' => $admission->id,
                'document_type' => 'ID_CARD',
                'file_path' => $idCardPath,
                'original_filename' => $this->idCardFile->getClientOriginalName(),
                'mime_type' => $this->idCardFile->getMimeType(),
                'file_size' => $this->idCardFile->getSize(),
            ]);
        }

        $this->trackingNumber = $appNo;
        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.admissions.admission-form')
            ->layout('layouts.app');
    }
}
