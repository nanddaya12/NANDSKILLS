<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-black text-slate-800 tracking-tight">Academic Registry Verification</h2>
        <p class="text-slate-500 text-xs mt-1">Validate student credentials and completion certs issued by the academy.</p>
    </div>

    <!-- Lookup Form -->
    <form wire:submit.prevent="verify" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Certificate Reference Code</label>
            <div class="flex gap-2">
                <input type="text" wire:model.defer="code" placeholder="CERT-2026-XXXX" required
                       class="flex-1 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm font-bold uppercase">
                <button type="submit" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-colors">
                    Verify
                </button>
            </div>
            @error('code') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
    </form>

    <!-- Results Output -->
    @if($searched)
        @if($verified && $certificate)
            <!-- Verified Success Card -->
            <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-4 text-slate-800 animate-pulse">
                <div class="flex items-center gap-3 text-emerald-600 font-extrabold text-sm">
                    <span class="text-xl">🛡</span>
                    <span>CREDENTIAL VALIDATED</span>
                </div>
                
                <div class="space-y-3 text-xs">
                    <div class="border-b border-emerald-200/50 pb-2">
                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Candidate Graduate</span>
                        <span class="text-sm font-extrabold text-slate-900">{{ $certificate->user->first_name }} {{ $certificate->user->last_name }}</span>
                    </div>

                    <div class="border-b border-emerald-200/50 pb-2">
                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Course Accomplished</span>
                        <span class="text-sm font-extrabold text-slate-900">{{ $certificate->course->title ?? 'Academy Curriculum Program' }}</span>
                    </div>

                    <div class="border-b border-emerald-200/50 pb-2">
                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Issuance Date</span>
                        <span class="text-slate-800 font-bold">{{ \Carbon\Carbon::parse($certificate->issue_date)->format('M d, Y') }}</span>
                    </div>

                    <div>
                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Verification Status</span>
                        <span class="text-emerald-600 font-extrabold">AUTHENTIC & ACTIVE</span>
                    </div>
                </div>
            </div>
        @else
            <!-- Verification Failed -->
            <div class="p-6 rounded-2xl bg-rose-50 border border-rose-200 text-center space-y-3">
                <div class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-rose-100 text-rose-500 text-lg">
                    ⚠
                </div>
                <h4 class="text-sm font-bold text-rose-700">Invalid Reference Code</h4>
                <p class="text-xs text-slate-500 leading-normal">The requested certificate credentials could not be located in our registry database. Verify character spelling and try again.</p>
            </div>
        @endif
    @endif
</div>
