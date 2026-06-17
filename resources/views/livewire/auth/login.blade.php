<div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden font-sans">
    <!-- Ambient background glows -->
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-blue-500/5 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-500/5 blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-md p-8 rounded-2xl bg-white border border-slate-200 shadow-xl shadow-slate-200/50 relative z-10 mx-4">
        <!-- Logo and branding header -->
        <div class="text-center mb-8">
            @if(isset($currentTenant) && $currentTenant->logo_url)
                <img src="{{ $currentTenant->logo_url }}" alt="{{ $currentTenant->name }}" class="h-12 mx-auto mb-3">
            @else
                <div class="h-12 w-12 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-600/20">
                    <span class="text-white text-xl font-bold font-display">NS</span>
                </div>
            @endif
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                {{ $currentTenant->name ?? 'NANDSKILLS' }}
            </h2>
            <p class="text-slate-500 text-sm mt-1">Education & Training Management Portal</p>
        </div>

        @if (session()->has('status'))
            <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-3">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errorMessage)
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-center gap-3">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        <form wire:submit.prevent="login" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                        </svg>
                    </span>
                    <input type="email" id="email" wire:model.defer="email" required
                           class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 pl-11 pr-4 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm transition-all placeholder-slate-400"
                           placeholder="name@nandskills.com">
                </div>
                @error('email') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Password</label>
                    <a href="{{ route('auth.forgot-password') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-500 transition-colors">Forgot password?</a>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <input type="password" id="password" wire:model.defer="password" required
                           class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 pl-11 pr-4 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm transition-all placeholder-slate-400"
                           placeholder="••••••••">
                </div>
                @error('password') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center text-xs text-slate-600 cursor-pointer select-none">
                    <input type="checkbox" wire:model.defer="remember" class="rounded border-slate-300 bg-slate-50 text-blue-600 focus:ring-0 focus:ring-offset-0 mr-2 h-4 w-4 transition-all">
                    Remember me
                </label>
            </div>

            <button type="submit" wire:loading.attr="disabled"
                    class="w-full mt-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-blue-600/20 focus:outline-none disabled:opacity-50">
                <span wire:loading.remove>Sign In</span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Signing In...
                </span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">Need to launch your own training academy? <a href="{{ route('auth.register') }}" class="text-blue-600 font-bold hover:underline">Register Tenant</a></p>
        </div>
    </div>
</div>
