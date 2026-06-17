<div class="max-w-md mx-auto py-12">
    <!-- Check-In Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <!-- Banner Branding -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8 text-center text-white relative">
            <h2 class="text-xl font-bold">Classroom Check-In</h2>
            <p class="text-xs text-blue-100/90 mt-1.5 font-medium">Log your attendance instantly using the active code provided by your trainer.</p>
            
            <div class="absolute right-3 top-3 opacity-10">
                <svg class="h-16 w-16" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                </svg>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Session feedback messages -->
            @if (session()->has('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center gap-3">
                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if (session()->has('info'))
                <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 text-sm font-medium rounded-xl flex items-center gap-3">
                    <svg class="h-5 w-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('info') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium rounded-xl flex items-center gap-3">
                    <svg class="h-5 w-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Form -->
            <form wire:submit.prevent="submitCheckIn" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 text-center">Enter 6-Character Verification Code</label>
                    <input 
                        type="text" 
                        wire:model="code" 
                        maxlength="6" 
                        placeholder="e.g. AB12CD" 
                        class="w-full text-center tracking-widest text-2xl font-bold uppercase rounded-xl border-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-200/50 py-3"
                    >
                    @error('code') 
                        <span class="text-xs text-red-500 mt-1 block text-center">{{ $message }}</span> 
                    @enderror
                </div>

                <button 
                    type="submit" 
                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition-all shadow-sm"
                >
                    Submit Code
                </button>
            </form>

            <div class="border-t border-slate-100 pt-4 text-center">
                <span class="text-xs text-slate-400">Having trouble? Please verify the code on the board or ask your trainer to mark you manually.</span>
            </div>
        </div>
    </div>
</div>
