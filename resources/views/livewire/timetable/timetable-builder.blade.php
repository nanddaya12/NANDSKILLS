<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Interactive Timetable Scheduler</h2>
            <p class="text-slate-400 text-sm mt-1">Design course timetables, assign classrooms, map instructors, print schedules, and automatically detect resource overlaps.</p>
        </div>
    </div>

    <!-- Filters and Builder tools -->
    <div class="grid grid-cols-3 gap-6">
        <!-- Cohort select -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Select Cohort</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-350 mb-1">Academic Program</label>
                    <select wire:model="selectedProgramId" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                        <option value="">Select Program</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-350 mb-1">Session</label>
                    <select wire:model="selectedSessionId" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                        <option value="">Select Session</option>
                        @foreach($sessions as $sess)
                            <option value="{{ $sess->id }}">{{ $sess->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-350 mb-1">Semester No</label>
                    <input type="number" wire:model="selectedSemester" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                </div>

                <div class="pt-2 flex gap-2">
                    <button wire:click="loadTimetable" class="flex-1 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs transition-colors">
                        Load Timetable
                    </button>
                    @if($activeTimetable)
                        <button onclick="window.print()" class="px-3 py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 rounded-xl text-xs font-semibold">
                            🖨️ Print
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add Class Entry Form -->
        @if($activeTimetable)
            <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Add Schedule Entry</h3>
                <form wire:submit.prevent="saveEntry" class="space-y-3">
                    <div>
                        <label class="block text-[10px] uppercase text-slate-400 mb-1">Subject</label>
                        <select wire:model.defer="new_subject_id" required class="w-full bg-slate-900 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->id }}">{{ $subj->name }} ({{ $subj->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase text-slate-400 mb-1">Time Period/Slot</label>
                            <select wire:model.defer="new_slot_id" required class="w-full bg-slate-900 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                <option value="">Select Slot</option>
                                @foreach($timetableSlots as $slot)
                                    <option value="{{ $slot->id }}">{{ $slot->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase text-slate-400 mb-1">Day of Week</label>
                            <select wire:model.defer="new_day_of_week" class="w-full bg-slate-900 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                                <option value="7">Sunday</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase text-slate-400 mb-1">Classroom/Room</label>
                            <select wire:model.defer="new_room_id" required class="w-full bg-slate-900 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                <option value="">Select Room</option>
                                @foreach($rooms as $rm)
                                    <option value="{{ $rm->id }}">{{ $rm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase text-slate-400 mb-1">Instructor</label>
                            <select wire:model.defer="new_teacher_id" required class="w-full bg-slate-900 border border-white/10 text-white rounded-lg py-1.5 px-2.5 text-xs">
                                <option value="">Select Trainer</option>
                                @foreach($teachers as $tch)
                                    <option value="{{ $tch->id }}">{{ $tch->first_name }} {{ $tch->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs transition-colors mt-2">
                        Add Entry
                    </button>
                </form>
            </div>
        @else
            <div class="p-6 rounded-2xl bg-white/5 border border-white/5 text-center text-slate-500 text-xs italic flex items-center justify-center">
                Select a program, session and semester to configure timetable entries.
            </div>
        @endif

        <!-- Conflict warnings panel -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-3">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Conflict Detector Logs</h3>
            <div class="space-y-2 max-h-[160px] overflow-y-auto">
                @forelse($conflictWarnings as $warning)
                    <div class="p-2.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-medium leading-relaxed">
                        ⚠️ {{ $warning }}
                    </div>
                @empty
                    <div class="text-[11px] text-slate-500 italic py-8 text-center">No conflicts or schedule overlaps detected. Clean run!</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Main grid view timetable -->
    @if($activeTimetable)
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md space-y-4">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <h3 class="text-sm font-bold text-white">Class Schedule Matrix</h3>
                
                <!-- View Mode Buttons -->
                <div class="flex gap-1.5 bg-slate-950/60 p-1 rounded-xl border border-white/5">
                    <button wire:click="$set('viewMode', 'daily')" class="px-3.5 py-1.5 text-[10px] font-bold rounded-lg transition-all {{ $viewMode === 'daily' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white' }}">
                        Daily
                    </button>
                    <button wire:click="$set('viewMode', 'weekly')" class="px-3.5 py-1.5 text-[10px] font-bold rounded-lg transition-all {{ $viewMode === 'weekly' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white' }}">
                        Weekly
                    </button>
                    <button wire:click="$set('viewMode', 'monthly')" class="px-3.5 py-1.5 text-[10px] font-bold rounded-lg transition-all {{ $viewMode === 'monthly' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white' }}">
                        Monthly List
                    </button>
                </div>
            </div>

            <!-- Daily View Day Toggles -->
            @if($viewMode === 'daily')
                <div class="flex gap-2 justify-center pb-2 border-b border-white/5">
                    @for($d=1; $d<=7; $d++)
                        <button wire:click="$set('selectedDay', {{ $d }})" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $selectedDay === $d ? 'bg-slate-800 text-white border border-blue-500/40' : 'text-slate-400 hover:text-white' }}">
                            {{ $this->getDayName($d) }}
                        </button>
                    @endfor
                </div>
            @endif

            <div class="overflow-x-auto">
                @if($viewMode === 'weekly')
                    <table class="w-full border-collapse text-left text-xs text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-slate-400">
                                <th class="py-3 px-3 font-semibold uppercase tracking-wider w-24">Time Slot</th>
                                @for($d=1; $d<=7; $d++)
                                    <th class="py-3 px-3 font-semibold uppercase tracking-wider">{{ $this->getDayName($d) }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($timetableSlots as $slot)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-3 font-bold text-white border-r border-white/5">
                                        {{ $slot->label }}
                                        @if($slot->is_break)
                                            <span class="block text-[9px] text-amber-400 font-bold bg-amber-500/10 px-1 py-0.5 rounded w-fit mt-1">BREAK</span>
                                        @endif
                                    </td>
                                    @for($d=1; $d<=7; $d++)
                                        @php
                                            $cellEntries = $entries->where('slot_id', $slot->id)->where('day_of_week', $d);
                                        @endphp
                                        <td class="py-4 px-3 align-top border-r border-white/5 last:border-0">
                                            @foreach($cellEntries as $entry)
                                                <div class="p-2 rounded-lg bg-slate-950/40 border border-white/5 space-y-1 mb-2 last:mb-0 relative group">
                                                    <div class="font-bold text-white leading-tight">{{ $entry->subject->name ?? 'Subject' }}</div>
                                                    <div class="text-[9px] text-slate-400">Room: {{ $entry->room->name ?? 'N/A' }}</div>
                                                    <div class="text-[9px] text-slate-500">T: {{ $entry->teacher->first_name ?? '' }} {{ $entry->teacher->last_name ?? '' }}</div>
                                                    
                                                    <button wire:click="deleteEntry('{{ $entry->id }}')" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 text-rose-450 hover:text-rose-350 text-[10px] bg-slate-900/80 px-1 rounded transition-opacity">✕</button>
                                                </div>
                                            @endforeach
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($viewMode === 'daily')
                    <table class="w-full border-collapse text-left text-xs text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-slate-400 font-bold uppercase text-[10px]">
                                <th class="py-3 px-3 w-40">Period Time Slot</th>
                                <th class="py-3 px-3">Subject Class Schedule ({{ $this->getDayName($selectedDay) }})</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($timetableSlots as $slot)
                                @php
                                    $cellEntries = $entries->where('slot_id', $slot->id)->where('day_of_week', $selectedDay);
                                @endphp
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-3 font-bold text-white border-r border-white/5">
                                        {{ $slot->label }}
                                        @if($slot->is_break)
                                            <span class="block text-[9px] text-amber-400 font-bold bg-amber-500/10 px-1 py-0.5 rounded w-fit mt-1">BREAK</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-3">
                                        <div class="grid grid-cols-3 gap-4">
                                            @forelse($cellEntries as $entry)
                                                <div class="p-3 rounded-xl bg-slate-950/40 border border-white/5 space-y-1.5 relative group font-sans">
                                                    <div class="font-bold text-white text-xs">{{ $entry->subject->name ?? 'Subject' }}</div>
                                                    <div class="text-[10px] text-slate-400">Classroom: {{ $entry->room->name ?? 'N/A' }}</div>
                                                    <div class="text-[10px] text-slate-500">Instructor: {{ $entry->teacher->first_name ?? '' }} {{ $entry->teacher->last_name ?? '' }}</div>
                                                    <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-blue-500/10 text-blue-400 uppercase mt-1 inline-block">{{ $entry->entry_type }}</span>
                                                    
                                                    <button wire:click="deleteEntry('{{ $entry->id }}')" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 text-rose-450 hover:text-rose-350 text-[10px] bg-slate-900/80 px-1 rounded transition-opacity">✕</button>
                                                </div>
                                            @empty
                                                <span class="text-slate-500 italic text-xs">No classes scheduled.</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($viewMode === 'monthly')
                    <!-- Monthly planner list view -->
                    <div class="space-y-4 py-2">
                        <div class="flex justify-between items-center text-xs text-slate-400 border-b border-white/5 pb-2">
                            <span>Monthly Recurring Planner Summary</span>
                            <span>{{ count($entries) }} Scheduled Weekly Events</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            @for($d=1; $d<=7; $d++)
                                @php $dayEntries = $entries->where('day_of_week', $d); @endphp
                                @if($dayEntries->isNotEmpty())
                                    <div class="p-4 bg-slate-950/40 border border-white/5 rounded-xl space-y-3">
                                        <h4 class="text-xs font-bold text-white border-b border-white/5 pb-1 flex justify-between">
                                            <span>{{ $this->getDayName($d) }}s</span>
                                            <span class="text-[10px] text-blue-400 font-normal font-sans">Recurring weekly</span>
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach($dayEntries as $entry)
                                                <div class="flex justify-between items-start text-[11px] py-1 border-b border-white/5 last:border-0">
                                                    <div>
                                                        <strong class="text-slate-200">{{ $entry->subject->name }}</strong>
                                                        <div class="text-[10px] text-slate-500">{{ $entry->slot->label }} • Room {{ $entry->room->name ?? 'N/A' }}</div>
                                                    </div>
                                                    <span class="text-[10px] text-slate-400">{{ $entry->teacher->first_name }} {{ $entry->teacher->last_name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
