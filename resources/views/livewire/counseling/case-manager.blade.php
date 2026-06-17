<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Student success & Counseling Center</h2>
            <p class="text-slate-400 text-sm mt-1">Manage academic interventions, log counseling sessions, track behavioral action plans, and audit early warnings.</p>
        </div>
        <button wire:click="$toggle('isCreatingCase')" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-sm transition-all shadow-md shadow-blue-600/10">
            Open New Case File
        </button>
    </div>

    <!-- Mode tabs -->
    <div class="flex border-b border-white/10 gap-2 overflow-x-auto">
        <button wire:click="$set('activeTab', 'cases')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'cases' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Intervention Cases ({{ count($cases) }})
        </button>
        <button wire:click="$set('activeTab', 'early_warning')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'early_warning' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            ⚠️ Early Warning radar ({{ count($warningList) }})
        </button>
    </div>

    @if($isCreatingCase)
        <!-- Case creation Form -->
        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md max-w-xl mx-auto space-y-5">
            <h3 class="text-sm font-bold text-white">Create counseling case</h3>
            <form wire:submit.prevent="saveCase" class="space-y-4">
                <div>
                    <label class="block text-xs text-slate-350 mb-1">Student</label>
                    <select wire:model.defer="new_student_id" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500">
                        <option value="">Select Student...</option>
                        @foreach($students as $stud)
                            <option value="{{ $stud->id }}">{{ $stud->first_name }} {{ $stud->last_name }} ({{ $stud->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-350 mb-1">Assigned Counselor</label>
                        <select wire:model.defer="new_counselor_id" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500">
                            <option value="">Select Counselor...</option>
                            @foreach($counselors as $cons)
                                <option value="{{ $cons->id }}">{{ $cons->first_name }} {{ $cons->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-350 mb-1">Case Type</label>
                        <select wire:model.defer="new_case_type" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                            <option value="ACADEMIC">ACADEMIC</option>
                            <option value="PERSONAL">PERSONAL</option>
                            <option value="CAREER">CAREER</option>
                            <option value="FINANCIAL">FINANCIAL</option>
                            <option value="BEHAVIORAL">BEHAVIORAL</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-slate-350 mb-1">Case Priority</label>
                    <select wire:model.defer="new_priority" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                        <option value="LOW">LOW</option>
                        <option value="MEDIUM">MEDIUM</option>
                        <option value="HIGH">HIGH</option>
                        <option value="URGENT">URGENT</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-slate-350 mb-1">Case Summary / Reasons</label>
                    <textarea wire:model.defer="new_summary" rows="3" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs">
                        Create Case File
                    </button>
                    <button type="button" wire:click="$set('isCreatingCase', false)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl text-xs font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($activeTab === 'cases')
        <div class="grid grid-cols-3 gap-6">
            <!-- Left: Active cases registry list -->
            <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Intervention Cases</h3>
                <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                    @forelse($cases as $case)
                        <button wire:click="selectCase('{{ $case->id }}')" class="w-full text-left p-4 rounded-xl border transition-all flex justify-between items-start 
                            {{ $selectedCase && $selectedCase->id === $case->id ? 'bg-blue-600/10 border-blue-500/30' : 'bg-slate-950/30 border-white/5 hover:border-white/10' }}
                        ">
                            <div>
                                <h4 class="text-xs font-bold text-white">{{ $case->student->first_name ?? 'N/A' }} {{ $case->student->last_name ?? '' }}</h4>
                                <span class="text-[9px] font-bold text-blue-400 mt-1 inline-block uppercase bg-blue-500/10 px-2 py-0.5 rounded-full border border-blue-500/20">{{ $case->case_type }}</span>
                                <p class="text-[10px] text-slate-500 mt-1 italic line-clamp-1">"{{ $case->summary }}"</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase
                                    {{ $case->priority === 'URGENT' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-slate-800 text-slate-300 border border-white/10' }}
                                ">
                                    {{ $case->priority }}
                                </span>
                                <div class="text-[9px] text-slate-500 mt-2 font-semibold">{{ $case->status }}</div>
                            </div>
                        </button>
                    @empty
                        <div class="text-xs text-slate-500 py-12 text-center">No counseling cases opened.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right: selected case detail audit & plans -->
            <div class="col-span-2 space-y-6">
                @if($selectedCase)
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md space-y-6">
                        <!-- Case profile details -->
                        <div class="flex justify-between items-start border-b border-white/5 pb-4">
                            <div>
                                <h3 class="text-base font-bold text-white">{{ $selectedCase->student->first_name }} {{ $selectedCase->student->last_name }}</h3>
                                <p class="text-[11px] text-slate-450 mt-1">Counselor: <span class="text-slate-300 font-bold">{{ $selectedCase->counselor->first_name ?? 'Staff' }} {{ $selectedCase->counselor->last_name ?? '' }}</span></p>
                            </div>
                            <div class="flex gap-2">
                                @if($selectedCase->status === 'OPEN')
                                    <button wire:click="updateCaseStatus('RESOLVED')" class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-450 text-[10px] font-bold rounded-lg border border-emerald-500/20 transition-all">Mark Resolved</button>
                                    <button wire:click="updateCaseStatus('CLOSED')" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-750 text-slate-300 text-[10px] font-bold rounded-lg border border-white/5 transition-all">Close Case</button>
                                @endif
                            </div>
                        </div>

                        <!-- Add Session Form -->
                        @if($isAddingSession)
                            <div class="p-5 bg-slate-900 border border-white/10 rounded-xl space-y-3">
                                <h4 class="text-xs font-bold text-white border-b border-white/5 pb-2">Log Meeting Notes</h4>
                                <form wire:submit.prevent="saveSession" class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[10px] text-slate-450 mb-1">Session Date</label>
                                            <input type="date" wire:model.defer="sess_date" required class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-450 mb-1">Duration (Mins)</label>
                                            <input type="number" wire:model.defer="sess_duration" class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-450 mb-1">Meeting Notes (Confidential)</label>
                                        <textarea wire:model.defer="sess_notes" rows="3" required class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-450 mb-1">Next Actions</label>
                                        <input type="text" wire:model.defer="sess_next_steps" placeholder="e.g. Schedule academic tutor" class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                    </div>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold text-xs transition-colors">Save Notes</button>
                                    <button type="button" wire:click="$set('isAddingSession', false)" class="px-3 py-2 bg-slate-800 text-slate-300 rounded-lg text-xs ml-2">Cancel</button>
                                </form>
                            </div>
                        @else
                            <button wire:click="$set('isAddingSession', true)" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-blue-400 text-xs font-bold rounded-xl border border-white/5 transition-all w-full text-center">
                                + Log Session Notes
                            </button>
                        @endif

                        <!-- Display sessions timeline -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Case Logs Timeline</h4>
                            <div class="space-y-3">
                                @forelse($selectedCaseSessions as $sess)
                                    <div class="p-4 bg-slate-950/40 rounded-xl border border-white/5 space-y-2 text-xs">
                                        <div class="flex justify-between items-center text-[10px] text-slate-500">
                                            <span>Date: {{ $sess->session_date->format('M d, Y') }} ({{ $sess->duration_minutes }} Mins)</span>
                                            @if($sess->is_confidential)
                                                <span class="text-rose-400 font-bold bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20">CONFIDENTIAL</span>
                                            @endif
                                        </div>
                                        <p class="text-slate-350 leading-relaxed">{{ $sess->notes }}</p>
                                        @if($sess->next_steps)
                                            <div class="text-[10px] text-blue-300 mt-2"><span class="font-bold">Next Action:</span> {{ $sess->next_steps }}</div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-500 italic py-4">No meeting session logs posted yet.</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Intervention Planner goals -->
                        <div class="space-y-4 border-t border-white/5 pt-6">
                            <div class="flex justify-between items-center">
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Intervention Action Plans</h4>
                                <button wire:click="$set('isAddingPlan', true)" class="text-xs text-blue-450 hover:underline">+ Define Plan Goal</button>
                            </div>

                            @if($isAddingPlan)
                                <div class="p-4 bg-slate-900 border border-white/10 rounded-xl space-y-3">
                                    <form wire:submit.prevent="savePlan" class="space-y-3">
                                        <div>
                                            <label class="block text-[10px] text-slate-450 mb-1">Goal / Outcome Target</label>
                                            <input type="text" wire:model.defer="plan_goal" required placeholder="e.g. Raise CGPA to 2.2" class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-450 mb-1">Target Date</label>
                                            <input type="date" wire:model.defer="plan_target_date" class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-450 mb-1">Action steps (Comma-separated)</label>
                                            <input type="text" wire:model.defer="plan_steps_text" placeholder="e.g. Attend daily tutoring, Complete all backlogs" class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                        </div>
                                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold text-xs">Create Goal Plan</button>
                                        <button type="button" wire:click="$set('isAddingPlan', false)" class="px-3 py-2 bg-slate-800 text-slate-350 rounded-lg text-xs ml-2">Cancel</button>
                                    </form>
                                </div>
                            @endif

                            <div class="space-y-3">
                                @forelse($selectedCasePlans as $plan)
                                    <div class="p-4 bg-slate-950/40 rounded-xl border border-white/5 flex justify-between items-center text-xs">
                                        <div>
                                            <div class="font-bold text-white">{{ $plan->goal }}</div>
                                            @if($plan->target_date)
                                                <div class="text-[9px] text-slate-500 mt-1">Target Date: {{ $plan->target_date->format('M d, Y') }}</div>
                                            @endif
                                            @if(!empty($plan->action_steps))
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    @foreach($plan->action_steps as $step)
                                                        <span class="px-2 py-0.5 rounded bg-white/5 text-slate-400 text-[9px] border border-white/5">✓ {{ $step }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            @if($plan->status === 'ACTIVE')
                                                <button wire:click="updatePlanStatus('{{ $plan->id }}', 'ACHIEVED')" class="px-2 py-1 bg-emerald-650 hover:bg-emerald-600 text-white text-[10px] rounded">Achieved</button>
                                                <button wire:click="updatePlanStatus('{{ $plan->id }}', 'ABANDONED')" class="px-2 py-1 text-rose-450 text-[10px]">Abandon</button>
                                            @else
                                                <span class="text-[10px] font-bold text-slate-450 uppercase">{{ $plan->status }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-500 italic py-4">No intervention action plans defined.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-12 rounded-2xl bg-white/5 border border-white/5 text-center text-slate-500 italic">
                        Select a counseling case from the registry to view logs and intervention plans.
                    </div>
                @endif
            </div>
        </div>
    @elseif($activeTab === 'early_warning')
        <!-- Early Warning Radar List -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-3 mb-4">Risk Warnings Registry</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                            <th class="py-3 px-4">Student</th>
                            <th class="py-3 px-4">Detected Risk</th>
                            <th class="py-3 px-4">Active metrics</th>
                            <th class="py-3 px-4">Risk Severity</th>
                            <th class="py-3 px-4 text-right">Intervention Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($warningList as $warn)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 font-bold text-white">{{ $warn['name'] }}</td>
                                <td class="py-4 px-4 text-xs font-semibold text-slate-350">{{ $warn['reason'] }}</td>
                                <td class="py-4 px-4 text-xs text-blue-400 font-bold">{{ $warn['metric'] }}</td>
                                <td class="py-4 px-4 text-xs">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold border 
                                        {{ $warn['severity'] === 'URGENT' ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20' }}
                                    ">
                                        {{ $warn['severity'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <button wire:click="openCreate; $set('new_student_id', '{{ $warn['student_id'] }}'); $set('isCreatingCase', true)" class="text-xs bg-slate-800 hover:bg-slate-700 text-blue-400 px-3 py-1.5 rounded-lg font-semibold">
                                        Open Case File
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-500">No students are currently flagged on the early warning radar. Institutional standings are excellent!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
