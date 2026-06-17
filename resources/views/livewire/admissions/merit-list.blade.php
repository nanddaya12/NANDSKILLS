<div class="space-y-8">
    <!-- Header -->
    <div class="bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <h2 class="text-xl font-bold text-white">Merit List Rankings & Offers</h2>
        <p class="text-slate-400 text-sm mt-1">Compute merit score rankings for candidate lists and release admissions offers in batch.</p>
    </div>

    <!-- Filters & ranking buttons -->
    <div class="grid grid-cols-3 gap-6">
        <!-- Setup Cohort selection -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Select Cohort</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Program</label>
                    <select wire:model="selectedProgramId" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                        <option value="">Select Program</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Session</label>
                    <select wire:model="selectedSessionId" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                        <option value="">Select Session</option>
                        @foreach($sessions as $sess)
                            <option value="{{ $sess->id }}">{{ $sess->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2 flex gap-2">
                    <button wire:click="loadMeritList" class="flex-1 py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl font-semibold text-xs transition-colors">
                        View List
                    </button>
                    <button wire:click="generateMeritList" class="flex-1 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs transition-colors shadow-md shadow-blue-500/10">
                        Compute Rankings
                    </button>
                </div>

                @error('merit')
                    <div class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-450 text-[10px] text-center font-bold">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Bulk offer generator -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Release Admission Offers</h3>
            <p class="text-[11px] text-slate-450">Release admission letters and update states to approved for top ranked candidates.</p>
            
            <div class="grid grid-cols-3 gap-2">
                <button wire:click="bulkOffer(10)" class="py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl text-xs font-semibold">Top 10</button>
                <button wire:click="bulkOffer(25)" class="py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl text-xs font-semibold">Top 25</button>
                <button wire:click="bulkOffer(50)" class="py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl text-xs font-semibold">Top 50</button>
            </div>

            @if($statusMessage)
                <div class="p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[11px] font-semibold text-center mt-2">
                    {{ $statusMessage }}
                </div>
            @endif
        </div>

        <!-- Stats Overview -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-3">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Cohort Summary</h3>
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="p-3 rounded-xl bg-slate-950/40 border border-white/5">
                    <div class="text-lg font-black text-white">{{ count($meritItems) }}</div>
                    <div class="text-[9px] text-slate-500 uppercase tracking-wider font-semibold">Ranked</div>
                </div>
                <div class="p-3 rounded-xl bg-slate-950/40 border border-white/5">
                    <div class="text-lg font-black text-emerald-450">{{ count($meritItems->where('status', 'ACCEPTED')) }}</div>
                    <div class="text-[9px] text-slate-500 uppercase tracking-wider font-semibold">Accepted</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Merit list table -->
    <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead>
                    <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                        <th class="py-3 px-4 w-16">Rank</th>
                        <th class="py-3 px-4">Applicant</th>
                        <th class="py-3 px-4">Merit Score</th>
                        <th class="py-3 px-4">Intake Form</th>
                        <th class="py-3 px-4">Offer Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($meritItems as $item)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-4 px-4 font-black text-lg text-slate-200">#{{ $item->rank }}</td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-white">{{ $item->admission->applicant_name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">{{ $item->admission->email ?? '' }}</div>
                            </td>
                            <td class="py-4 px-4 text-sm font-bold text-blue-450">{{ $item->merit_score }}%</td>
                            <td class="py-4 px-4 text-xs text-slate-400">{{ $item->admission->admissionForm->title ?? 'N/A' }}</td>
                            <td class="py-4 px-4 text-xs">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold border 
                                    {{ $item->status === 'OFFERED' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : '' }}
                                    {{ $item->status === 'ACCEPTED' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : '' }}
                                    {{ $item->status === 'DECLINED' ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : '' }}
                                    {{ $item->status === 'LISTED' ? 'bg-slate-800 text-slate-350 border-white/10' : '' }}
                                ">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right space-x-1">
                                @if($item->status === 'LISTED')
                                    <button wire:click="updateItemStatus('{{ $item->id }}', 'OFFERED')" class="text-xs bg-slate-850 hover:bg-slate-800 text-amber-450 px-2 py-1 rounded">Issue Offer</button>
                                @endif
                                @if($item->status === 'OFFERED')
                                    <button wire:click="updateItemStatus('{{ $item->id }}', 'ACCEPTED')" class="text-xs bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-450 px-2 py-1 rounded">Accept</button>
                                    <button wire:click="updateItemStatus('{{ $item->id }}', 'DECLINED')" class="text-xs text-rose-450 hover:text-rose-350 px-2 py-1">Decline</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No ranked applicants in this merit cohort. Select criteria to compute rankings.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
