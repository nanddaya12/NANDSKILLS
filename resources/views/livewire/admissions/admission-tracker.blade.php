<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Admissions Lifecycle board</h2>
            <p class="text-slate-400 text-sm mt-1">Review student admission applications, schedule interviews, grade test results, rank merit lists, and auto-enroll students.</p>
        </div>
    </div>

    <!-- Scheduling Modal -->
    @if($isSchedulingInterview)
        <div class="p-6 rounded-2xl bg-slate-900 border border-white/10 max-w-md mx-auto space-y-4 shadow-xl">
            <h3 class="text-sm font-bold text-white">Schedule Admission Interview</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-slate-350 mb-1">Interview Date & Time</label>
                    <input type="datetime-local" wire:model.defer="interview_date" required class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-2 px-3 text-xs">
                </div>
                <div>
                    <label class="block text-xs text-slate-350 mb-1">Format</label>
                    <select wire:model.defer="interview_format" class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-2 px-3 text-xs">
                        <option value="ONLINE">ONLINE</option>
                        <option value="IN_PERSON">IN_PERSON</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-350 mb-1">Meeting Link / Room (Optional)</label>
                    <input type="text" wire:model.defer="interview_link" class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-2 px-3 text-xs" placeholder="e.g. Google Meet link or Room 102">
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button wire:click="scheduleInterview" class="flex-1 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs transition-colors">
                    Schedule & Move Status
                </button>
                <button wire:click="$set('isSchedulingInterview', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl text-xs font-semibold">
                    Cancel
                </button>
            </div>
        </div>
    @endif

    <!-- Kanban board columns -->
    <div class="grid grid-cols-6 gap-4 overflow-x-auto pb-4">
        @foreach($statuses as $status)
            @php
                $statusAdmissions = $admissions->where('status', $status);
            @endphp
            <div class="bg-white/5 rounded-2xl border border-white/5 p-4 flex flex-col min-w-[240px] space-y-4">
                <div class="flex justify-between items-center border-b border-white/5 pb-2">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">{{ str_replace('_', ' ', $status) }}</span>
                    <span class="px-2 py-0.5 rounded bg-white/10 text-white font-bold text-[10px]">{{ count($statusAdmissions) }}</span>
                </div>

                <div class="space-y-3 flex-1 overflow-y-auto min-h-[300px]">
                    @forelse($statusAdmissions as $adm)
                        <div class="p-4 rounded-xl bg-slate-950/40 border border-white/5 space-y-3 hover:border-blue-500/30 transition-colors">
                            <div>
                                <h4 class="text-xs font-bold text-white">{{ $adm->applicant_name }}</h4>
                                <p class="text-[10px] text-slate-450 mt-0.5">{{ $adm->email }}</p>
                                <p class="text-[10px] text-blue-400 mt-1 font-semibold">{{ $adm->program->name ?? 'Unassigned' }}</p>
                                <p class="text-[9px] text-slate-500 mt-0.5">Grade: {{ $adm->previous_grade }}%</p>
                            </div>

                            @if($adm->documents->isNotEmpty())
                                <div class="space-y-1">
                                    <div class="text-[9px] font-bold text-slate-400">ATTACHMENTS:</div>
                                    @foreach($adm->documents as $doc)
                                        <a href="/storage/{{ $doc->file_path }}" target="_blank" class="block text-[9px] text-blue-300 hover:underline">📄 {{ $doc->document_type }}</a>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex flex-col gap-1 border-t border-white/5 pt-2">
                                <!-- Status action loops -->
                                @if($status === 'SUBMITTED')
                                    <button wire:click="updateStatus('{{ $adm->id }}', 'UNDER_REVIEW')" class="w-full py-1.5 bg-slate-800 hover:bg-slate-700 text-blue-300 text-[10px] font-bold rounded-lg transition-colors">Move to Under Review</button>
                                @elseif($status === 'UNDER_REVIEW')
                                    <button wire:click="openInterviewModal('{{ $adm->id }}')" class="w-full py-1.5 bg-slate-850 hover:bg-slate-800 text-amber-300 text-[10px] font-bold rounded-lg transition-colors">Schedule Interview</button>
                                    <button wire:click="updateStatus('{{ $adm->id }}', 'APPROVED')" class="w-full py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 text-[10px] font-bold rounded-lg transition-colors mt-1">Direct Approve</button>
                                @elseif($status === 'INTERVIEW_SCHEDULED')
                                    <button wire:click="updateStatus('{{ $adm->id }}', 'APPROVED')" class="w-full py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 text-[10px] font-bold rounded-lg transition-colors">Approve Application</button>
                                @elseif($status === 'APPROVED')
                                    <button wire:click="updateStatus('{{ $adm->id }}', 'ENROLLED')" class="w-full py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold rounded-lg transition-colors shadow-sm shadow-blue-500/10">Register & Enroll</button>
                                @endif

                                @if($status !== 'REJECTED' && $status !== 'ENROLLED')
                                    <button onclick="confirm('Reject this applicant?') || event.stopImmediatePropagation()" wire:click="updateStatus('{{ $adm->id }}', 'REJECTED')" class="w-full py-1 text-rose-400 text-[9px] hover:underline text-left mt-1 pl-1">Reject Applicant</button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-[10px] text-slate-500 italic py-6 text-center">Empty stage.</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
