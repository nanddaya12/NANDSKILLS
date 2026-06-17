<div>
    <h2 class="text-xl font-bold text-slate-800 mb-1">Welcome Back</h2>
    <p class="text-sm text-slate-500 mb-6">Sign in to your staff dashboard.</p>

    @if (session('status'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">✓ {{ session('status') }}</div>
    @endif

    @if ($error)
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center gap-2">
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ $error }}
        </div>
    @endif

    <form wire:submit.prevent="login" class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Email Address</label>
            <input type="email" wire:model.defer="email" autocomplete="email"
                   class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                   placeholder="you@institution.com">
            @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Password</label>
                <a href="{{ route('auth.forgot-password') }}" class="text-xs text-blue-600 hover:underline">Forgot password?</a>
            </div>
            <div x-data="{ show: false }" class="relative">
                <input :type="show ? 'text' : 'password'" wire:model.defer="password" autocomplete="current-password"
                       class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="••••••••">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model="remember" id="remember-staff"
                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <label for="remember-staff" class="text-sm text-slate-600 select-none cursor-pointer">Remember me for 30 days</label>
        </div>

        <button type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2 mt-1">
            <span wire:loading.remove>Sign In to Dashboard</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Signing in…
            </span>
        </button>
    </form>

    {{-- Student portal link — only if student portal is active on this tenant --}}
    <div class="mt-5 pt-4 border-t border-slate-100 text-center text-sm text-slate-400">
        Are you a student? <a href="{{ route('student.login') }}" class="text-blue-600 font-medium hover:underline">Student Portal →</a>
    </div>
    {{-- No link back to public website from staff portal --}}
</div>
