<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * Super Admin Login — separate secure portal at /superadmin/login
 * Only users with role "Super Admin" can authenticate here.
 */
class SuperAdminLogin extends Component
{
    public string $email    = '';
    public string $password = '';
    public string $error    = '';

    protected array $rules = [
        'email'    => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->error = '';
        $this->validate();

        $throttleKey = 'superadmin|' . Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->error = "Too many attempts. Try again in {$seconds}s.";
            return;
        }

        // Super admins have tenant_id = null (system-wide)
        $user = User::whereNull('tenant_id')
            ->where('email', $this->email)
            ->first();

        if (!$user || !Hash::check($this->password, $user->password_hash)) {
            RateLimiter::hit($throttleKey, 120);
            \App\Models\FailedLogin::create([
                'tenant_id' => null,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'portal' => 'SUPERADMIN',
                'reason' => 'INVALID_CREDENTIALS',
            ]);
            $this->error = 'Invalid credentials. Access restricted to Super Admins only.';
            return;
        }

        if (!$user->hasRole('Super Admin')) {
            RateLimiter::hit($throttleKey, 120);
            \App\Models\FailedLogin::create([
                'tenant_id' => null,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'portal' => 'SUPERADMIN',
                'reason' => 'INSUFFICIENT_ROLE',
            ]);
            $this->error = 'Unauthorized. This portal is for Super Admins only.';
            return;
        }

        if ($user->status !== 'ACTIVE') {
            \App\Models\FailedLogin::create([
                'tenant_id' => null,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'portal' => 'SUPERADMIN',
                'reason' => 'INACTIVE',
            ]);
            $this->error = 'This account has been suspended.';
            return;
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, false);
        $user->update(['last_login_at' => now()]);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.super-admin-login')
            ->layout('layouts.superadmin', ['pageTitle' => 'Super Admin Portal']);
    }
}
