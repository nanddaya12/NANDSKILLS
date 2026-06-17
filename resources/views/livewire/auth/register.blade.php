<div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden font-sans py-12">
    <!-- Ambient background glows -->
    <div class="absolute top-[-20%] right-[-10%] w-[500px] h-[500px] rounded-full bg-blue-500/5 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-500/5 blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-xl p-8 rounded-2xl bg-white border border-slate-200 shadow-xl shadow-slate-200/50 relative z-10 mx-4">
        <div class="text-center mb-8">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-600/20">
                <span class="text-white text-xl font-bold font-display">NS</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Register Your Enterprise Academy</h2>
            <p class="text-slate-500 text-sm mt-1">Get started with NANDSKILLS SaaS Training Management System</p>
        </div>

        @if ($errorMessage)
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-center gap-3">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        <form wire:submit.prevent="register" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="tenantName" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Academy Name</label>
                    <input type="text" id="tenantName" wire:model.defer="tenantName" required
                           class="w-full bg-slate-55 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm transition-all"
                           placeholder="Nandskills Academy">
                    @error('tenantName') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="subdomain" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Desired Subdomain</label>
                    <div class="relative flex">
                        <input type="text" id="subdomain" wire:model.defer="subdomain" required
                               class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-l-xl py-3 px-4 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm transition-all placeholder-slate-400"
                               placeholder="my-academy">
                        <span class="inline-flex items-center px-3 rounded-r-xl border border-l-0 border-slate-200 bg-slate-100 text-slate-500 text-sm">
                            .localhost
                        </span>
                    </div>
                    @error('subdomain') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="border-t border-slate-100 pt-5">
                <h3 class="text-sm font-bold text-slate-700 mb-4">Admin Account Credentials</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="firstName" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">First Name</label>
                        <input type="text" id="firstName" wire:model.defer="firstName" required
                               class="w-full bg-slate-55 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm transition-all"
                               placeholder="John">
                        @error('firstName') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="lastName" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Last Name</label>
                        <input type="text" id="lastName" wire:model.defer="lastName" required
                               class="w-full bg-slate-55 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm transition-all"
                               placeholder="Doe">
                        @error('lastName') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Admin Email</label>
                    <input type="email" id="email" wire:model.defer="email" required
                           class="w-full bg-slate-55 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm transition-all"
                           placeholder="admin@academy.com">
                    @error('email') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Password</label>
                    <input type="password" id="password" wire:model.defer="password" required
                           class="w-full bg-slate-55 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm transition-all"
                           placeholder="••••••••">
                    @error('password') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" wire:loading.attr="disabled"
                    class="w-full mt-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-blue-600/20 focus:outline-none disabled:opacity-50">
                <span wire:loading.remove>Provision My Academy</span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Provisioning Systems...
                </span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">Already registered? <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Sign In Instead</a></p>
        </div>
    </div>
</div>
