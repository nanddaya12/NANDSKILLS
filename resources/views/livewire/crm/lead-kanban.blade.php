<div class="space-y-8 h-full flex flex-col">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5 shrink-0">
        <div>
            <h2 class="text-xl font-bold text-white">Sales Pipeline</h2>
            <p class="text-slate-400 text-sm mt-1">Track prospective students, schedule outreach calls, and monitor enrollment conversion channels.</p>
        </div>
        <button wire:click="$toggle('isAddingLead')" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-sm transition-all shadow-md shadow-blue-600/10">
            {{ $isAddingLead ? 'View Kanban Board' : 'Add New Prospect' }}
        </button>
    </div>

    @if($isAddingLead)
        <!-- Add Lead Form -->
        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md max-w-2xl mx-auto space-y-6">
            <h3 class="text-lg font-bold text-white">Add New Pipeline Lead</h3>

            <form wire:submit.prevent="addLead" class="space-y-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">First Name</label>
                        <input type="text" wire:model.defer="first_name" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('first_name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Last Name</label>
                        <input type="text" wire:model.defer="last_name" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('last_name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Email Address</label>
                    <input type="email" wire:model.defer="email" required
                           class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                    @error('email') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Phone</label>
                        <input type="text" wire:model.defer="phone"
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('phone') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Estimated Value ($)</label>
                        <input type="number" step="0.01" wire:model.defer="value" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('value') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Lead Source</label>
                    <select wire:model.defer="source" required
                            class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        <option value="Organic">Organic Search</option>
                        <option value="Paid">Paid Campaign</option>
                        <option value="Referral">Referral Code</option>
                        <option value="Social">Social Media</option>
                    </select>
                    @error('source') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors">
                        Add to Pipeline
                    </button>
                    <button type="button" wire:click="$set('isAddingLead', false)" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Kanban Grid -->
        <div class="flex-1 flex gap-6 overflow-x-auto pb-4 items-start select-none">
            @foreach($stages as $stage)
                <div class="w-80 shrink-0 bg-slate-950/40 border border-white/5 rounded-2xl flex flex-col max-h-full overflow-hidden">
                    <!-- Column Header -->
                    <div class="p-4 border-b border-white/5 flex items-center justify-between shrink-0 bg-slate-950/60" style="border-top: 3px solid {{ $stage->color }}">
                        <div>
                            <span class="font-bold text-xs text-white uppercase tracking-wider">{{ $stage->name }}</span>
                            <span class="text-[10px] text-slate-400 font-bold block mt-0.5">${{ number_format($stage->deals->sum('amount'), 2) }} Total</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-[10px] text-slate-300 font-bold">
                            {{ $stage->deals->count() }}
                        </span>
                    </div>

                    <!-- Column Cards -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 max-h-[500px]">
                        @forelse($stage->deals as $deal)
                            <div class="p-4 rounded-xl bg-white/5 border border-white/5 space-y-3 hover:border-white/10 transition-all">
                                <div class="flex justify-between items-start gap-3">
                                    <div class="space-y-0.5">
                                        <h4 class="text-xs font-bold text-white leading-normal">{{ $deal->lead->full_name }}</h4>
                                        <p class="text-[10px] text-slate-400 leading-normal">{{ $deal->lead->email }}</p>
                                    </div>
                                    <span class="text-xs font-extrabold text-blue-400 shrink-0">${{ number_format($deal->amount, 2) }}</span>
                                </div>

                                <div class="flex justify-between items-center text-[10px] text-slate-500 pt-2 border-t border-white/5">
                                    <span>Src: {{ $deal->lead->source }}</span>
                                    <div class="flex gap-2">
                                        <!-- Quick Move Actions -->
                                        @foreach($stages as $targetStage)
                                            @if($targetStage->id !== $stage->id)
                                                <button wire:click="moveDeal('{{ $deal->id }}', '{{ $targetStage->id }}')"
                                                        title="Move to {{ $targetStage->name }}"
                                                        class="px-1.5 py-0.5 rounded bg-slate-800 text-[8px] font-bold text-slate-300 hover:bg-blue-600 hover:text-white transition-all">
                                                    → {{ substr($targetStage->name, 0, 3) }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-[11px] text-slate-600">No opportunities in this stage.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
