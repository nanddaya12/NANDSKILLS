<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class SocialAuthController extends Controller
{
    /**
     * Redirect to OAuth provider — students only.
     * GET /auth/social/{provider}
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        // Store the tenant ID in session before redirect
        if (app()->bound('currentTenant')) {
            session(['oauth_tenant_id' => app('currentTenant')->id]);
        }

        // If Socialite is installed, use it; otherwise show config page
        if (class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            return \Laravel\Socialite\Facades\Socialite::driver($provider)->redirect();
        }

        // Fallback: show credentials setup needed page
        return redirect()->route('student.login')
            ->with('oauth_error', "Social login for {$provider} requires OAuth credentials to be configured in .env. Please contact your administrator.");
    }

    /**
     * Handle OAuth provider callback — students only.
     * GET /auth/social/{provider}/callback
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        if (!class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            return redirect()->route('student.login')->with('oauth_error', 'Social login is not configured yet.');
        }

        try {
            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('student.login')
                ->with('oauth_error', 'OAuth authentication failed. Please try again.');
        }

        $tenantId = session('oauth_tenant_id')
            ?? (app()->bound('currentTenant') ? app('currentTenant')->id : null);

        if (!$tenantId) {
            return redirect()->route('student.login')
                ->with('oauth_error', 'Session expired. Please try again.');
        }

        // Find existing user by provider ID
        $user = User::where('tenant_id', $tenantId)
            ->where('social_provider', $provider)
            ->where('social_provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            // Try matching by email
            $user = User::where('tenant_id', $tenantId)
                ->where('email', $socialUser->getEmail())
                ->first();

            if ($user) {
                // Link existing account to social provider
                $user->update([
                    'social_provider'    => $provider,
                    'social_provider_id' => $socialUser->getId(),
                    'avatar_url'         => $socialUser->getAvatar(),
                ]);
            } else {
                // Auto-register as Student
                [$firstName, $lastName] = $this->parseName($socialUser->getName() ?? '');

                $user = User::create([
                    'tenant_id'          => $tenantId,
                    'email'              => $socialUser->getEmail() ?? $provider . '_' . $socialUser->getId() . '@social.nandskills',
                    'password_hash'      => Hash::make(Str::random(40)),
                    'first_name'         => $firstName,
                    'last_name'          => $lastName,
                    'social_provider'    => $provider,
                    'social_provider_id' => $socialUser->getId(),
                    'avatar_url'         => $socialUser->getAvatar(),
                    'is_email_verified'  => true,
                    'status'             => 'ACTIVE',
                ]);

                // Assign Student role
                $studentRole = Role::where('name', 'Student')->first();
                if ($studentRole) {
                    $user->roles()->attach($studentRole->id);
                }
            }
        }

        if (!$user->hasRole('Student')) {
            return redirect()->route('student.login')
                ->with('oauth_error', 'This account is not registered as a student.');
        }

        if ($user->status !== 'ACTIVE') {
            return redirect()->route('student.login')
                ->with('oauth_error', 'Your account is suspended. Please contact support.');
        }

        Auth::login($user, true);
        $user->update(['last_login_at' => now()]);
        session()->forget('oauth_tenant_id');

        return redirect()->route('dashboard');
    }

    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'github', 'facebook'])) {
            abort(404, "Unsupported OAuth provider: {$provider}");
        }
    }

    private function parseName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);
        return [$parts[0] ?? 'Student', $parts[1] ?? ''];
    }
}
