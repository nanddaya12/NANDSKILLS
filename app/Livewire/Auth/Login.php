<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $errorMessage = '';

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->errorMessage = "Too many login attempts. Please try again in {$seconds} seconds.";
            return;
        }

        // Check user existence within the current tenant context
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if (!$tenantId) {
            $this->errorMessage = "You must access the login portal via a tenant subdomain or domain.";
            return;
        }

        $user = User::where('tenant_id', $tenantId)
            ->where('email', $this->email)
            ->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($this->password, $user->password_hash)) {
            RateLimiter::hit($throttleKey, 60);
            $this->errorMessage = 'Invalid email or password credentials.';
            return;
        }

        if ($user->status !== 'ACTIVE') {
            $this->errorMessage = 'Your account is currently inactive or suspended.';
            return;
        }

        RateLimiter::clear($throttleKey);

        if ($user->mfa_enabled) {
            // Store details in session and redirect to MFA verification screen
            session(['mfa_auth_user_id' => $user->id, 'mfa_remember' => $this->remember]);
            return redirect()->route('auth.mfa');
        }

        // Login user
        Auth::login($user, $this->remember);
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest');
    }
}
