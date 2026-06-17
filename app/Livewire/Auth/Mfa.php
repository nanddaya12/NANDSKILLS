<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Services\Google2FA;
use Illuminate\Support\Facades\Auth;

class Mfa extends Component
{
    public string $code = '';
    public string $errorMessage = '';

    public function verify()
    {
        $userId = session('mfa_auth_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (!$user) {
            $this->errorMessage = 'User not found. Please log in again.';
            return;
        }

        if (Google2FA::verifyKey($user->mfa_secret, $this->code)) {
            $remember = session('mfa_remember', false);
            Auth::login($user, $remember);
            $user->update(['last_login_at' => now()]);

            session()->forget(['mfa_auth_user_id', 'mfa_remember']);

            return redirect()->intended(route('dashboard'));
        }

        $this->errorMessage = 'Invalid authenticator token code. Please try again.';
    }

    public function render()
    {
        return view('livewire.auth.mfa')
            ->layout('layouts.guest');
    }
}
