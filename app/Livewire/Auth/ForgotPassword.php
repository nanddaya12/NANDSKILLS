<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ForgotPassword extends Component
{
    public string $email = '';
    public string $statusMessage = '';
    public string $errorMessage = '';
    public string $simulationLink = '';

    protected array $rules = [
        'email' => 'required|email',
    ];

    public function sendResetLink()
    {
        $this->validate();
        $this->errorMessage = '';
        $this->statusMessage = '';
        $this->simulationLink = '';

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if (!$tenantId) {
            $this->errorMessage = "You must access this portal via a tenant subdomain or domain.";
            return;
        }

        $user = User::where('tenant_id', $tenantId)
            ->where('email', $this->email)
            ->first();

        if (!$user) {
            // Avoid enumerating accounts: act as if it succeeded
            $this->statusMessage = 'If the email matches an active account, a password reset link has been dispatched.';
            return;
        }

        // Generate token
        $token = Str::random(60);

        // Delete existing tokens for this user in this tenant
        DB::table('password_reset_tokens')
            ->where('tenant_id', $tenantId)
            ->where('email', $this->email)
            ->delete();

        // Save new token
        DB::table('password_reset_tokens')->insert([
            'id' => Str::uuid(),
            'tenant_id' => $tenantId,
            'email' => $this->email,
            'token' => bcrypt($token),
            'created_at' => now(),
        ]);

        $resetUrl = route('auth.reset-password', ['token' => $token, 'email' => $this->email]);

        // Log the mail
        \Illuminate\Support\Facades\Log::info("Password Reset Link sent to user {$this->email}: {$resetUrl}");

        $this->statusMessage = 'A password reset link has been dispatched successfully.';
        
        // Show simulation link in development mode
        if (config('app.env') === 'local') {
            $this->simulationLink = $resetUrl;
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('layouts.guest');
    }
}
