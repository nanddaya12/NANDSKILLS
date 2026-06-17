<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

/**
 * Student self-registration — at /student/register
 * Creates a student account under the current tenant.
 */
class StudentRegister extends Component
{
    public string $firstName = '';
    public string $lastName  = '';
    public string $email     = '';
    public string $phone     = '';
    public string $password  = '';
    public string $passwordConfirm = '';
    public bool   $agreeTerms = false;
    public string $error     = '';
    public string $success   = '';

    protected function rules(): array
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        return [
            'firstName'       => 'required|string|max:50',
            'lastName'        => 'required|string|max:50',
            'email'           => [
                'required',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')->where('tenant_id', $tenantId)
            ],
            'phone'           => 'nullable|string|max:20',
            'password'        => 'required|min:8',
            'passwordConfirm' => 'required|same:password',
            'agreeTerms'      => 'accepted',
        ];
    }

    protected array $messages = [
        'agreeTerms.accepted'      => 'You must agree to the terms and conditions.',
        'passwordConfirm.same'     => 'Passwords do not match.',
        'password.min'             => 'Password must be at least 8 characters.',
    ];

    public function register()
    {
        $this->error   = '';
        $this->success = '';
        $this->validate();

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if (!$tenantId) {
            $this->error = 'Unable to determine your institution. Please access this page via your institution\'s portal.';
            return;
        }

        // Check if email already exists in this tenant
        $exists = User::where('tenant_id', $tenantId)
            ->where('email', $this->email)
            ->exists();

        if ($exists) {
            $this->error = 'An account with this email already exists. Please sign in instead.';
            return;
        }

        // Create student user
        $user = User::create([
            'tenant_id'         => $tenantId,
            'email'             => $this->email,
            'password_hash'     => Hash::make($this->password),
            'first_name'        => $this->firstName,
            'last_name'         => $this->lastName,
            'phone'             => $this->phone ?: null,
            'is_email_verified' => false,
            'status'            => 'ACTIVE',
        ]);

        // Assign Student role
        $studentRole = Role::where('tenant_id', $tenantId)
            ->where('name', 'Student')
            ->first();

        if ($studentRole) {
            $user->roles()->attach($studentRole->id);
        }

        // Create student profile so the student shows up in directories and is enabled for SIS features
        $rollNumber = 'NS-' . date('Y') . '-' . str_pad(rand(100, 9999), 4, '0', STR_PAD_LEFT);
        while (\App\Models\StudentProfile::where('tenant_id', $tenantId)->where('roll_number', $rollNumber)->exists()) {
            $rollNumber = 'NS-' . date('Y') . '-' . str_pad(rand(100, 9999), 4, '0', STR_PAD_LEFT);
        }

        \App\Models\StudentProfile::create([
            'user_id' => $user->id,
            'tenant_id' => $tenantId,
            'roll_number' => $rollNumber,
            'admission_date' => now(),
            'status' => 'ACTIVE',
        ]);

        // Log in immediately
        Auth::login($user, true);
        $user->update(['last_login_at' => now()]);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.student-register')
            ->layout('layouts.portal', [
                'portalTitle' => 'Student Registration',
                'portalIcon'  => '🎓',
            ]);
    }
}
