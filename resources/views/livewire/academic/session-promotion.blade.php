<div class="space-y-8">
    <!-- Header -->
    <div class="bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <h2 class="text-xl font-bold text-white">Batch Student Promotion</h2>
        <p class="text-slate-400 text-sm mt-1">Promote students from one semester or session to another. Review CGPA and academic standings before batch promotion.</p>
    </div>

    <!-- Filters and promotion settings -->
    <div class="grid grid-cols-3 gap-6">
        <!-- Step 1: Select Source -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-2">1. Select Source Class</h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Program</label>
                    <select wire:model="selectedProgramId" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                        <option value="">Select Program</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->name }} ({{ $prog->code }})</option>
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

                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Current Semester</label>
                    <input type="number" wire:model="selectedSemester" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                </div>

                <button wire:click="loadStudents" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs transition-colors mt-2">
                    Load Student List
                </button>
            </div>
        </div>

        <!-- Step 2: Target Class -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-2">2. Select Target Class</h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Target Session</label>
                    <select wire:model="targetSessionId" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                        <option value="">Select Target Session</option>
                        @foreach($sessions as $sess)
                            <option value="{{ $sess->id }}">{{ $sess->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Target Semester</label>
                    <input type="number" wire:model="targetSemester" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                </div>

                <div class="pt-4">
                    <button onclick="confirm('Promote selected students?') || event.stopImmediatePropagation()" wire:click="promoteStudents" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs transition-colors">
                        Execute Promotion
                    </button>
                </div>

                @if($promotionStatusMessage)
                    <div class="p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold text-center">
                        {{ $promotionStatusMessage }}
                    </div>
                @endif
                @error('promotion')
                    <div class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold text-center">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Selection Quick actions -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/10 pb-2">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <button wire:click="selectAll" class="py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl text-xs font-semibold">
                    Select All
                </button>
                <button wire:click="deselectAll" class="py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl text-xs font-semibold">
                    Deselect All
                </button>
            </div>
            <div class="p-4 rounded-xl bg-slate-950/40 border border-white/5 text-xs text-slate-400 space-y-2">
                <div>Selected: <span class="font-bold text-white">{{ count($selectedStudentIds) }}</span> students</div>
                <div>Loaded: <span class="font-bold text-white">{{ count($students) }}</span> students</div>
            </div>
        </div>
    </div>

    <!-- Student List Table -->
    <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead>
                    <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                        <th class="py-3 px-4 w-12">Select</th>
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4">Credits Earned</th>
                        <th class="py-3 px-4">CGPA</th>
                        <th class="py-3 px-4">Standing</th>
                        <th class="py-3 px-4">Enrollment Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($students as $profile)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-4 px-4">
                                <input type="checkbox" wire:model="selectedStudentIds" value="{{ $profile->id }}" class="rounded bg-slate-900 border-white/10 text-blue-600">
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-white">{{ $profile->student->first_name ?? 'N/A' }} {{ $profile->student->last_name ?? '' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $profile->student->email ?? '' }}</div>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold">{{ $profile->credits_earned }} Hrs</td>
                            <td class="py-4 px-4 font-bold text-blue-400">{{ $profile->cumulative_cgpa ?? '0.00' }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 text-[10px] font-bold border border-white/10">
                                    {{ $profile->academic_standing }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">
                                    {{ $profile->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No students loaded. Select program and session to load.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
