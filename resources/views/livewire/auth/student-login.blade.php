<div x-data="{ tab: @entangle('activeTab') }">
    <h2 class="text-xl font-bold text-slate-800 mb-1">Student Sign In</h2>
    <p class="text-sm text-slate-500 mb-5">Access your learning dashboard.</p>

    {{-- OAuth Error --}}
    @if (session('oauth_error'))
        <div class="mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm">
            ⚠️ {{ session('oauth_error') }}
        </div>
    @endif

    {{-- Success --}}
    @if ($success)
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">✓ {{ $success }}</div>
    @endif

    {{-- Error --}}
    @if ($error)
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center gap-2">
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ $error }}
        </div>
    @endif

    {{-- ── SOCIAL AUTH BUTTONS ─────────────────────────── --}}
    <div class="grid grid-cols-3 gap-2.5 mb-5">
        <a href="{{ route('auth.social', 'google') }}"
           class="flex flex-col items-center gap-1.5 py-3 border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-xl transition text-xs font-semibold text-slate-600 hover:text-blue-700 group">
            <svg class="h-5 w-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google
        </a>
        <a href="{{ route('auth.social', 'github') }}"
           class="flex flex-col items-center gap-1.5 py-3 border border-slate-200 hover:border-slate-600 hover:bg-slate-50 rounded-xl transition text-xs font-semibold text-slate-600 hover:text-slate-800 group">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.2 11.38.6.11.82-.26.82-.58 0-.28-.01-1.04-.02-2.04-3.34.72-4.04-1.61-4.04-1.61-.55-1.39-1.34-1.76-1.34-1.76-1.09-.75.08-.73.08-.73 1.2.08 1.84 1.24 1.84 1.24 1.07 1.83 2.81 1.3 3.49 1 .11-.78.42-1.3.76-1.6-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.12-.3-.54-1.52.12-3.18 0 0 1.01-.32 3.3 1.23a11.5 11.5 0 013.01-.4c1.02 0 2.05.14 3.01.4 2.28-1.55 3.29-1.23 3.29-1.23.66 1.66.25 2.88.12 3.18.77.84 1.24 1.91 1.24 3.22 0 4.61-2.81 5.63-5.48 5.92.43.37.81 1.1.81 2.22 0 1.6-.01 2.9-.01 3.29 0 .32.21.7.82.58C20.56 21.8 24 17.3 24 12c0-6.63-5.37-12-12-12z"/>
            </svg>
            GitHub
        </a>
        <a href="{{ route('auth.social', 'facebook') }}"
           class="flex flex-col items-center gap-1.5 py-3 border border-slate-200 hover:border-blue-500 hover:bg-blue-50 rounded-xl transition text-xs font-semibold text-slate-600 hover:text-blue-700 group">
            <svg class="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.03 4.388 11.03 10.125 11.927v-8.437H7.078v-3.49h3.047V9.413c0-3.025 1.792-4.697 4.533-4.697 1.313 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.885v2.266h3.328l-.532 3.49h-2.796v8.437C19.612 23.103 24 18.103 24 12.073z"/>
            </svg>
            Facebook
        </a>
    </div>

    <div class="relative flex items-center gap-3 mb-5">
        <div class="flex-1 h-px bg-slate-200"></div>
        <span class="text-xs text-slate-400 font-medium">or sign in with</span>
        <div class="flex-1 h-px bg-slate-200"></div>
    </div>

    {{-- ── TAB SWITCHER ─────────────────────────────────── --}}
    <div class="flex bg-slate-100 rounded-xl p-1 mb-5">
        <button type="button"
                @click="tab = 'email'; $wire.set('activeTab', 'email')"
                :class="tab === 'email' ? 'bg-white shadow text-blue-700' : 'text-slate-500 hover:text-slate-700'"
                class="flex-1 py-2 rounded-lg text-sm font-semibold transition">
            📧 Email
        </button>
        <button type="button"
                @click="tab = 'phone'; $wire.set('activeTab', 'phone')"
                :class="tab === 'phone' ? 'bg-white shadow text-blue-700' : 'text-slate-500 hover:text-slate-700'"
                class="flex-1 py-2 rounded-lg text-sm font-semibold transition">
            📱 Mobile OTP
        </button>
    </div>

    {{-- ── EMAIL / PASSWORD TAB ─────────────────────────── --}}
    <div x-show="tab === 'email'" x-transition>
        <form wire:submit.prevent="loginWithEmail" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Email Address</label>
                <input type="email" wire:model.defer="email" autocomplete="email"
                       class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="you@email.com">
                @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <div class="flex justify-between mb-1.5">
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Password</label>
                    <a href="{{ route('auth.forgot-password') }}" class="text-xs text-blue-600 hover:underline">Forgot?</a>
                </div>
                <div x-data="{ show: false }" class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model.defer="password"
                           class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="••••••••">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model="remember" id="student-remember" class="rounded border-slate-300 text-blue-600">
                <label for="student-remember" class="text-sm text-slate-600">Remember me</label>
            </div>

            <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                <span wire:loading.remove>🎓 Sign In</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Signing in…
                </span>
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-slate-500">
            New student?
            <a href="{{ route('student.register') }}" class="text-blue-600 font-semibold hover:underline">Create account →</a>
        </p>
    </div>

    {{-- ── MOBILE OTP TAB ───────────────────────────────── --}}
    <div x-show="tab === 'phone'" x-transition>
        @if (!$otpSent)
            <form wire:submit.prevent="sendOtp" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Mobile Number</label>
                    <div class="flex gap-2">
                        <span class="inline-flex items-center px-3 bg-slate-100 border border-slate-300 border-r-0 rounded-l-xl text-sm text-slate-600">+</span>
                        <input type="tel" wire:model.defer="phone"
                               class="flex-1 bg-white border border-slate-300 text-slate-800 rounded-r-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="923001234567">
                    </div>
                    @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    <p class="text-xs text-slate-400 mt-1">Enter full number with country code, e.g. 92300...</p>
                </div>

                <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                    <span wire:loading.remove>📱 Send OTP Code</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Sending…
                    </span>
                </button>
            </form>
        @else
            <form wire:submit.prevent="verifyOtp" class="space-y-4">
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800">
                    📱 OTP sent to <strong>{{ $phone }}</strong>. Enter the 6-digit code below.
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">6-Digit OTP Code</label>
                    <input type="text" wire:model.defer="otp" maxlength="6" inputmode="numeric"
                           class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-3 px-4 text-2xl font-bold text-center tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="000000">
                    @error('otp') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                    <span wire:loading.remove>✅ Verify & Sign In</span>
                    <span wire:loading>Verifying…</span>
                </button>

                <button type="button" wire:click="$set('otpSent', false)"
                        class="w-full py-2 text-slate-500 hover:text-blue-600 text-sm font-medium transition">
                    ← Change number
                </button>
            </form>
        @endif
    </div>

    <div class="mt-5 pt-5 border-t border-slate-100 text-center text-sm text-slate-500">
        Are you a staff member?
        <a href="{{ url('/login') }}" class="text-blue-600 font-semibold hover:underline">Staff Portal →</a>
    </div>
</div>
