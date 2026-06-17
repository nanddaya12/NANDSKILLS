<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Admissions Intake Builder & Logs</h2>
            <p class="text-slate-400 text-sm mt-1">Configure open intakes, configure dynamic fields, set application fees, and audit applicant status history log.</p>
        </div>
        <button wire:click="openCreate" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-sm transition-all shadow-md shadow-blue-600/10">
            Create Intake Form
        </button>
    </div>

    @if($isCreating)
        <!-- Intake form configuration form -->
        <div class="grid grid-cols-3 gap-6">
            <!-- Form metadata -->
            <div class="col-span-2 p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md space-y-4">
                <h3 class="text-base font-bold text-white border-b border-white/5 pb-2">Form Information</h3>
                <form wire:submit.prevent="saveForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Form / Intake Title</label>
                        <input type="text" wire:model.defer="title" required placeholder="e.g. BSCS Fall 2026 Admissions" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Target Academic Program</label>
                            <select wire:model.defer="program_id" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                <option value="">Select Program</option>
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Application Fee ($)</label>
                            <input type="number" step="0.01" wire:model.defer="application_fee" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Intake Start Date</label>
                            <input type="date" wire:model.defer="open_date" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Intake Close Date</label>
                            <input type="date" wire:model.defer="close_date" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Intake Guidelines & Info</label>
                        <textarea wire:model.defer="description" rows="3" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm"></textarea>
                    </div>

                    <div class="pt-4 flex gap-4">
                        <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors">
                            Save Intake Form
                        </button>
                        <button type="button" wire:click="$set('isCreating', false)" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Dynamic field configurations -->
            <div class="p-6 rounded-2xl bg-white/5 border border-white/10 h-fit space-y-4">
                <h4 class="text-sm font-bold text-white border-b border-white/5 pb-2">Custom Input Fields</h4>
                
                <!-- Field Adder -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-[10px] uppercase tracking-wider text-slate-400 mb-1">Field Key (Unique)</label>
                        <input type="text" wire:model.defer="new_field_name" placeholder="e.g. father_name" class="w-full bg-slate-900 border border-white/10 text-white rounded-lg py-2 px-3 text-xs focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-wider text-slate-400 mb-1">Field Label / Text</label>
                        <input type="text" wire:model.defer="new_field_label" placeholder="e.g. Father's Full Name" class="w-full bg-slate-900 border border-white/10 text-white rounded-lg py-2 px-3 text-xs focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-wider text-slate-400 mb-1">Field Input Type</label>
                        <select wire:model.defer="new_field_type" class="w-full bg-slate-900 border border-white/10 text-white rounded-lg py-2 px-3 text-xs">
                            <option value="text">TEXT</option>
                            <option value="textarea">TEXTAREA</option>
                            <option value="select">SELECT</option>
                            <option value="number">NUMBER</option>
                        </select>
                    </div>
                    <button type="button" wire:click="addField" class="w-full py-2 bg-slate-850 hover:bg-slate-800 text-blue-400 rounded-lg text-xs font-bold transition-all">
                        + Append Field
                    </button>
                </div>

                <!-- Fields List -->
                <div class="space-y-2 border-t border-white/5 pt-3">
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">Configured Fields</span>
                    @forelse($fields as $index => $field)
                        <div class="flex justify-between items-center bg-slate-950/40 p-2.5 rounded-lg border border-white/5">
                            <div>
                                <span class="text-xs font-bold text-white">{{ $field['label'] }}</span>
                                <span class="text-[9px] text-slate-500 ml-2">[{{ $field['type'] }}]</span>
                            </div>
                            <button wire:click="removeField({{ $index }})" class="text-rose-450 hover:text-rose-350 text-xs">✕</button>
                        </div>
                    @empty
                        <div class="text-[10px] text-slate-500 italic py-2 pl-1">No custom fields added yet. Only default fields will be loaded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <!-- Intakes & Logs Lists -->
        <div class="grid grid-cols-3 gap-6">
            <!-- Left: Intake forms -->
            <div class="col-span-2 p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Active Admissions Intake Forms</h3>
                <div class="space-y-4">
                    @forelse($forms as $f)
                        <div class="p-5 bg-slate-950/30 rounded-xl border border-white/5 hover:border-white/10 transition-colors flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-bold text-white">{{ $f->title }}</h4>
                                <p class="text-slate-400 text-xs mt-1">{{ $f->program->name ?? '' }}</p>
                                <div class="flex items-center gap-4 text-[10px] text-slate-500 mt-2 font-semibold">
                                    <span>Intake Close: {{ $f->application_close_date ? $f->application_close_date->format('M d, Y') : 'Open' }}</span>
                                    <span>Fee: ${{ $f->application_fee }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="toggleFormStatus('{{ $f->id }}')" class="text-xs bg-slate-900 hover:bg-slate-850 px-3 py-1.5 rounded-lg text-slate-300 font-semibold">
                                    {{ $f->is_active ? 'Disable' : 'Enable' }}
                                </button>
                                <button wire:click="editForm('{{ $f->id }}')" class="text-xs text-blue-400 hover:text-blue-350 px-2 py-1.5">Edit</button>
                                <button onclick="confirm('Delete this form?') || event.stopImmediatePropagation()" wire:click="deleteForm('{{ $f->id }}')" class="text-xs text-rose-400 hover:text-rose-350 px-2 py-1.5">Delete</button>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-500 py-12 text-center">No admission intake templates configured.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Workflow logs audit -->
            <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Admissions Change Log</h3>
                <div class="space-y-4 overflow-y-auto max-h-[400px] pr-2">
                    @forelse($logs as $log)
                        <div class="p-3 bg-slate-950/50 rounded-xl border border-white/5 space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-white">{{ $log->admission->applicant_name ?? 'Applicant' }}</span>
                                <span class="text-[9px] text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-[10px]">
                                <span class="text-slate-450">Transition:</span>
                                <span class="text-amber-400 font-bold bg-amber-500/10 px-1.5 py-0.5 rounded text-[8px] border border-amber-500/15">{{ $log->from_status ?? 'DRAFT' }}</span>
                                <span class="text-slate-400 mx-1">→</span>
                                <span class="text-emerald-400 font-bold bg-emerald-500/10 px-1.5 py-0.5 rounded text-[8px] border border-emerald-500/15">{{ $log->to_status }}</span>
                            </div>
                            @if($log->notes)
                                <p class="text-[9px] text-slate-500 italic mt-1">"{{ $log->notes }}"</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-slate-500 py-12 text-center">No log activity audited.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
