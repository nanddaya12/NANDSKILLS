<div class="min-h-screen flex items-center justify-center bg-slate-950 relative overflow-hidden font-sans">
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[500px] h-[500px] rounded-full bg-violet-600/10 blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-md p-8 rounded-2xl backdrop-blur-md bg-white/5 border border-white/10 shadow-2xl relative z-10 mx-4">
        <div class="text-center mb-8">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-tr from-blue-600 to-violet-600 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-500/20">
                <span class="text-white text-xl font-bold">MFA</span>
            </div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Two-Factor Authentication</h2>
            <p class="text-slate-400 text-sm mt-1">Please enter the 6-digit verification code from your authenticator app.</p>
        </div>

        @if ($errorMessage)
            <div class="mb-5 p-4 rounded-xl bg-rose-500/15 border border-rose-500/20 text-rose-300 text-sm flex items-center gap-3">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        <form wire:submit.prevent="verify" class="space-y-6">
            <div>
                <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Verification Code</label>
                <input type="text" id="code" wire:model.defer="code" required maxlength="6" autofocus
                       class="w-full bg-white/5 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-center tracking-[1em] text-lg font-bold transition-all placeholder-slate-700"
                       placeholder="000000">
                @error('code') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-3 bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-500 hover:to-violet-500 text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-blue-600/10 focus:outline-none disabled:opacity-50">
                <span wire:loading.remove>Verify & Log In</span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Verifying...
                </span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-white/5 text-center">
            <p class="text-xs text-slate-500">Lost access? <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Cancel & Sign In again</a></p>
        </div>
    </div>
</div>
