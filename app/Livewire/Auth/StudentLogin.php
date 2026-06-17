<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\PhoneOtp;

/**
 * Student Login — at /student/login
 * Supports: Email/Password, Google OAuth, GitHub OAuth, Mobile OTP
 */
class StudentLogin extends Component
{
    // Tabs: 'email' | 'phone'
    public string $activeTab   = 'email';

    // Email/password tab
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;

    // Phone tab
    public string $phone    = '';
    public string $otp      = '';
    public bool   $otpSent  = false;
    public int    $otpTimer = 0;

    // Shared
    public string $error   = '';
    public string $success = '';

    public function loginWithEmail()
    {
        $this->error = '';
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $throttleKey = 'student|' . Str::lower($this->email) . '|' . request()->ip();

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
                'portal' => 'STUDENT',
                'reason' => 'INVALID_CREDENTIALS',
            ]);
            $this->error = 'Invalid email or password.';
            return;
        }

        if (!$user->hasRole('Student')) {
            \App\Models\FailedLogin::create([
                'tenant_id' => $tenantId,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'portal' => 'STUDENT',
                'reason' => 'INSUFFICIENT_ROLE',
            ]);
            $this->error = 'This portal is for students only. Staff should use the Staff Portal.';
            return;
        }

        if ($user->status !== 'ACTIVE') {
            \App\Models\FailedLogin::create([
                'tenant_id' => $tenantId,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'portal' => 'STUDENT',
                'reason' => 'INACTIVE',
            ]);
            $this->error = 'Your account is inactive or suspended.';
            return;
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, $this->remember);
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    public function sendOtp()
    {
        $this->error   = '';
        $this->success = '';

        $this->validate(['phone' => 'required|string|min:7|max:20']);

        $phone = preg_replace('/[^0-9+]/', '', $this->phone);
        $otp   = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache (5 minutes)
        Cache::put("phone_otp:{$phone}", $otp, now()->addMinutes(5));

        // In production: send via SMS gateway here
        // For dev: log it so you can see it
        \Illuminate\Support\Facades\Log::info("[NANDSKILLS OTP] Phone: {$phone} | OTP: {$otp}");

        $this->otpSent  = true;
        $this->otpTimer = 300;
        $this->success  = "OTP sent to {$phone}. Check Laravel logs for dev testing.";
    }

    public function verifyOtp()
    {
        $this->error = '';
        $this->validate([
            'phone' => 'required|string',
            'otp'   => 'required|digits:6',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $this->phone);
        $cached = Cache::get("phone_otp:{$phone}");

        if (!$cached || $cached !== $this->otp) {
            $this->error = 'Invalid or expired OTP. Please try again.';
            return;
        }

        Cache::forget("phone_otp:{$phone}");

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        // Find or auto-create student account by phone
        $user = User::where('tenant_id', $tenantId)
            ->where('phone', $phone)
            ->first();

        if (!$user) {
            // Auto-register student via phone
            $user = User::create([
                'tenant_id'         => $tenantId,
                'email'             => $phone . '@phone.nandskills',
                'password_hash'     => Hash::make(Str::random(32)),
                'first_name'        => 'Student',
                'last_name'         => substr($phone, -4),
                'phone'             => $phone,
                'status'            => 'ACTIVE',
                'is_email_verified' => false,
            ]);

            // Assign Student role
            $studentRole = \App\Models\Role::where('name', 'Student')->first();
            if ($studentRole) {
                $user->roles()->attach($studentRole->id);
            }
        }

        if ($user->status !== 'ACTIVE') {
            $this->error = 'Your account is inactive.';
            return;
        }

        Auth::login($user, true);
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.student-login')
            ->layout('layouts.portal', ['portalTitle' => 'Student Portal', 'portalIcon' => '🎓']);
    }
}
