<?php

namespace App\Livewire\Sis;

use Livewire\Component;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentRegistry extends Component
{
    public $students = [];
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $roll_number = '';
    public string $admission_date = '';
    public bool $isCreating = false;

    public function rules(): array
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => [
                'required',
                'email',
                \Illuminate\Validation\Rule::unique('users', 'email')->where('tenant_id', $tenantId)
            ],
            'roll_number' => 'required|string|max:30',
            'admission_date' => 'required|date',
        ];
    }

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Unauthorized action. SIS Student Registry is restricted.');
        }
        $this->loadStudents();
    }

    public function loadStudents()
    {
        $this->students = StudentProfile::with('user', 'parents.user')->get();
    }

    public function createStudent()
    {
        $this->validate();

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($tenantId) {
            // Create user login record
            $user = User::create([
                'tenant_id' => $tenantId,
                'email' => $this->email,
                'password_hash' => Hash::make('student_secure_pass_2026'),
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'is_email_verified' => true,
                'status' => 'ACTIVE',
            ]);

            // Assign Student role (specific to this tenant)
            $studentRole = \App\Models\Role::where('tenant_id', $tenantId)->where('name', 'Student')->first();
            if ($studentRole) {
                $user->roles()->attach($studentRole->id);
            }

            // Create profile
            StudentProfile::create([
                'user_id' => $user->id,
                'tenant_id' => $tenantId,
                'roll_number' => $this->roll_number,
                'admission_date' => $this->admission_date,
                'status' => 'ACTIVE',
            ]);
        }

        $this->resetInputFields();
        $this->loadStudents();
        $this->isCreating = false;
    }

    private function resetInputFields()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->roll_number = '';
        $this->admission_date = '';
    }

    public function render()
    {
        return view('livewire.sis.student-registry')
            ->layout('layouts.app');
    }
}
