<div>
    <h2 class="text-lg font-bold text-white mb-1">Sign In</h2>
    <p class="text-sm text-slate-400 mb-6">Super Administrator access only.</p>

    @if ($error)
        <div class="mb-4 p-3 bg-red-900/40 border border-red-700/60 text-red-300 rounded-xl text-sm flex items-center gap-2">
            <svg class="h-4 w-4 shrink-0 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ $error }}
        </div>
    @endif

    <!-- Audit notice -->
    <div class="mb-5 flex items-start gap-2 p-3 bg-indigo-950/60 border border-indigo-800/40 rounded-xl text-xs text-slate-400">
        <svg class="h-4 w-4 text-indigo-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        All authentication attempts are logged with IP address, timestamp, and user agent.
    </div>

    <form wire:submit.prevent="login" class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email Address</label>
            <input type="email" wire:model.defer="email" autocomplete="off"
                   class="admin-input w-full rounded-xl py-2.5 px-4 text-sm border focus:outline-none transition"
                   placeholder="admin@nandskills.com">
            @error('email') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Password</label>
            <div x-data="{ show: false }" class="relative">
                <input :type="show ? 'text' : 'password'" wire:model.defer="password" autocomplete="off"
                       class="admin-input w-full rounded-xl py-2.5 px-4 pr-10 text-sm border focus:outline-none transition"
                       placeholder="••••••••">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3 mt-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-indigo-900/50 flex items-center justify-center gap-2 border border-indigo-500/50">
            <span wire:loading.remove class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Authenticate
            </span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Verifying identity…
            </span>
        </button>
    </form>
</div>
