<div>
    <h2 class="text-xl font-bold text-slate-800 mb-1">Create Student Account</h2>
    <p class="text-sm text-slate-500 mb-5">Join {{ $currentTenant->name ?? 'the academy' }} as a student.</p>

    @if ($error)
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-start gap-2">
            <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ $error }}
        </div>
    @endif

    {{-- Social signup (same as login — just redirects to OAuth which auto-registers) --}}
    <div class="grid grid-cols-3 gap-2.5 mb-5">
        <a href="{{ route('auth.social', 'google') }}"
           class="flex flex-col items-center gap-1.5 py-3 border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-xl transition text-xs font-semibold text-slate-600 hover:text-blue-700">
            <svg class="h-5 w-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google
        </a>
        <a href="{{ route('auth.social', 'github') }}"
           class="flex flex-col items-center gap-1.5 py-3 border border-slate-200 hover:border-slate-600 hover:bg-slate-50 rounded-xl transition text-xs font-semibold text-slate-600 hover:text-slate-800">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.2 11.38.6.11.82-.26.82-.58 0-.28-.01-1.04-.02-2.04-3.34.72-4.04-1.61-4.04-1.61-.55-1.39-1.34-1.76-1.34-1.76-1.09-.75.08-.73.08-.73 1.2.08 1.84 1.24 1.84 1.24 1.07 1.83 2.81 1.3 3.49 1 .11-.78.42-1.3.76-1.6-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.12-.3-.54-1.52.12-3.18 0 0 1.01-.32 3.3 1.23a11.5 11.5 0 013.01-.4c1.02 0 2.05.14 3.01.4 2.28-1.55 3.29-1.23 3.29-1.23.66 1.66.25 2.88.12 3.18.77.84 1.24 1.91 1.24 3.22 0 4.61-2.81 5.63-5.48 5.92.43.37.81 1.1.81 2.22 0 1.6-.01 2.9-.01 3.29 0 .32.21.7.82.58C20.56 21.8 24 17.3 24 12c0-6.63-5.37-12-12-12z"/>
            </svg>
            GitHub
        </a>
        <a href="{{ route('auth.social', 'facebook') }}"
           class="flex flex-col items-center gap-1.5 py-3 border border-slate-200 hover:border-blue-500 hover:bg-blue-50 rounded-xl transition text-xs font-semibold text-slate-600 hover:text-blue-700">
            <svg class="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.03 4.388 11.03 10.125 11.927v-8.437H7.078v-3.49h3.047V9.413c0-3.025 1.792-4.697 4.533-4.697 1.313 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.885v2.266h3.328l-.532 3.49h-2.796v8.437C19.612 23.103 24 18.103 24 12.073z"/>
            </svg>
            Facebook
        </a>
    </div>

    <div class="relative flex items-center gap-3 mb-5">
        <div class="flex-1 h-px bg-slate-200"></div>
        <span class="text-xs text-slate-400 font-medium">or register with email</span>
        <div class="flex-1 h-px bg-slate-200"></div>
    </div>

    <form wire:submit.prevent="register" class="space-y-4">
        {{-- Name row --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">First Name</label>
                <input type="text" wire:model.defer="firstName" autocomplete="given-name"
                       class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="John">
                @error('firstName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Last Name</label>
                <input type="text" wire:model.defer="lastName" autocomplete="family-name"
                       class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="Doe">
                @error('lastName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Email Address</label>
            <input type="email" wire:model.defer="email" autocomplete="email"
                   class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                   placeholder="you@email.com">
            @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Phone (optional) --}}
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                Phone <span class="text-slate-400 font-normal normal-case">(optional)</span>
            </label>
            <input type="tel" wire:model.defer="phone" autocomplete="tel"
                   class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                   placeholder="+92 300 1234567">
            @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Password</label>
            <div x-data="{ show: false }" class="relative">
                <input :type="show ? 'text' : 'password'" wire:model.defer="password" autocomplete="new-password"
                       class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="Min. 8 characters">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Confirm Password</label>
            <input type="password" wire:model.defer="passwordConfirm" autocomplete="new-password"
                   class="w-full bg-white border border-slate-300 text-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                   placeholder="Repeat your password">
            @error('passwordConfirm') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Terms checkbox --}}
        <div class="flex items-start gap-2.5">
            <input type="checkbox" wire:model="agreeTerms" id="terms"
                   class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
            <label for="terms" class="text-xs text-slate-500 cursor-pointer leading-relaxed">
                I agree to the <a href="#" class="text-blue-600 hover:underline font-medium">Terms of Service</a>
                and <a href="#" class="text-blue-600 hover:underline font-medium">Privacy Policy</a>
            </label>
        </div>
        @error('agreeTerms') <span class="text-xs text-red-500 block -mt-2">{{ $message }}</span> @enderror

        <button type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2 mt-1">
            <span wire:loading.remove>🎓 Create Student Account</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Creating account…
            </span>
        </button>
    </form>

    <div class="mt-5 pt-4 border-t border-slate-100 text-center text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('student.login') }}" class="text-blue-600 font-semibold hover:underline">Sign In →</a>
    </div>
</div>
