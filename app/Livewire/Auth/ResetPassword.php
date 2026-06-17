<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $errorMessage = '';
    public string $statusMessage = '';

    protected array $rules = [
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ];

    public function mount()
    {
        $this->token = request()->query('token', '');
        $this->email = request()->query('email', '');
    }

    public function resetPassword()
    {
        $this->validate();
        $this->errorMessage = '';
        $this->statusMessage = '';

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if (!$tenantId) {
            $this->errorMessage = "You must access this portal via a tenant subdomain or domain.";
            return;
        }

        $tokenRecord = DB::table('password_reset_tokens')
            ->where('tenant_id', $tenantId)
            ->where('email', $this->email)
            ->first();

        if (!$tokenRecord || !Hash::check($this->token, $tokenRecord->token)) {
            $this->errorMessage = 'This password reset link is invalid or has expired.';
            return;
        }

        // Check if token is older than 60 minutes
        $createdAt = new \DateTime($tokenRecord->created_at);
        $diff = $createdAt->diff(new \DateTime());
        $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

        if ($minutes > 60) {
            $this->errorMessage = 'This password reset link has expired.';
            return;
        }

        $user = User::where('tenant_id', $tenantId)
            ->where('email', $this->email)
            ->first();

        if (!$user) {
            $this->errorMessage = 'Unable to find a user with this email address.';
            return;
        }

        // Update password
        $user->update([
            'password_hash' => Hash::make($this->password),
        ]);

        // Delete the token
        DB::table('password_reset_tokens')
            ->where('tenant_id', $tenantId)
            ->where('email', $this->email)
            ->delete();

        session()->flash('status', 'Your password has been successfully reset! You can now log in.');
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.reset-password')
            ->layout('layouts.guest');
    }
}
