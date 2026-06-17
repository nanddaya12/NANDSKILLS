<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * Staff/Tenant Admin Login — at /login
 * Only users with roles: Tenant Admin, Trainer, Parent
 * No social OAuth — credential-based only.
 */
class StaffLogin extends Component
{
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;
    public string $error    = '';

    protected array $rules = [
        'email'    => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->error = '';
        $this->validate();

        $throttleKey = 'staff|' . Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->error = "Too many attempts. Try again in {$seconds}s.";
            return;
        }

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $user = User::where('tenant_id', $tenantId)
            ->where('email', $this->email)
            ->first();

        if (!$user || !Hash::check($this->password, $user->password_hash)) {
            RateLimiter::hit($throttleKey, 60);
            \App\Models\FailedLogin::create([
                'tenant_id' => $tenantId,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'portal' => 'STAFF',
                'reason' => 'INVALID_CREDENTIALS',
            ]);
            $this->error = 'Invalid email or password.';
            return;
        }

        // Restrict to staff roles only
        if ($user->hasRole('Student')) {
            \App\Models\FailedLogin::create([
                'tenant_id' => $tenantId,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'portal' => 'STAFF',
                'reason' => 'INSUFFICIENT_ROLE',
            ]);
            $this->error = 'Students must use the Student Portal to sign in.';
            return;
        }

        if ($user->status !== 'ACTIVE') {
            \App\Models\FailedLogin::create([
                'tenant_id' => $tenantId,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'portal' => 'STAFF',
                'reason' => 'INACTIVE',
            ]);
            $this->error = 'Your account is inactive or suspended.';
            return;
        }

        RateLimiter::clear($throttleKey);

        if ($user->mfa_enabled) {
            session(['mfa_auth_user_id' => $user->id, 'mfa_remember' => $this->remember]);
            return redirect()->route('auth.mfa');
        }

        Auth::login($user, $this->remember);
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.staff-login')
            ->layout('layouts.staff', ['pageTitle' => 'Staff Portal']);
    }
}
