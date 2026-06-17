<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Attendance Manager</h2>
            <p class="text-sm text-slate-500 mt-1">Log student attendance, view rosters, and generate self-service QR check-in codes.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Branch</label>
                <select wire:model.live="selectedBranch" class="rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200/50">
                    <option value="">-- Choose Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Date</label>
                <input type="date" wire:model.live="selectedDate" class="rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200/50">
            </div>
        </div>
    </div>

    <!-- Alert / Feedback Messages -->
    @if (session()->has('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center gap-3">
            <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('status') }}
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

    <!-- QR / Check-in Code Section -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-1">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Self-Service Student Check-In
                </h3>
                <p class="text-sm text-slate-500">Generate a temporary validation code or scan point. Students can check in on their devices via <code class="bg-slate-100 text-blue-600 px-1 rounded">/attendance/checkin</code>.</p>
            </div>

            <div class="shrink-0 w-full md:w-auto">
                @if($hasActiveCode)
                    <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4 bg-blue-50 border border-blue-100 p-4 rounded-xl">
                        <div class="text-center md:text-left">
                            <span class="text-[10px] font-bold text-blue-500 uppercase tracking-wider block">Active Check-In Code</span>
                            <span class="text-2xl font-black text-blue-700 tracking-widest">{{ $activeCheckInCode }}</span>
                        </div>
                        <button wire:click="clearCheckInCode" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
                            Deactivate Code
                        </button>
                    </div>
                @else
                    <button wire:click="generateCheckInCode" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Generate Check-In Code
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Student Attendance Roster -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Classroom Roster</h3>
                <p class="text-xs text-slate-500 mt-0.5">Showing students currently enrolled in the selected branch.</p>
            </div>
            
            @if($selectedBranch)
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400 font-medium mr-2">Bulk actions:</span>
                    <button wire:click="markAll('PRESENT')" class="px-3 py-1.5 bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 border border-slate-200 text-xs font-semibold rounded-xl transition-all">
                        Mark All Present
                    </button>
                    <button wire:click="markAll('ABSENT')" class="px-3 py-1.5 bg-slate-100 hover:bg-red-50 text-slate-700 hover:text-red-600 border border-slate-200 text-xs font-semibold rounded-xl transition-all">
                        Mark All Absent
                    </button>
                </div>
            @endif
        </div>

        @if(!$selectedBranch)
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-slate-50 text-slate-400 border border-slate-100 mb-3">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-slate-800 text-sm">No branch selected</h4>
                <p class="text-xs text-slate-500 mt-1">Please select an active branch in the header to display the student roster.</p>
            </div>
        @elseif(count($students) === 0)
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-slate-50 text-slate-400 border border-slate-100 mb-3">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-slate-800 text-sm">No students registered</h4>
                <p class="text-xs text-slate-500 mt-1">There are no student profiles enrolled in this branch yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Student Name</th>
                            <th class="px-6 py-4">Roll Number</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Set Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $student)
                            @php
                                $record = $attendanceRecords[$student->user_id] ?? null;
                                $status = $record['status'] ?? 'UNMARKED';
                                $markedBy = isset($record['marked_by_id']) ? 'Set manually' : '';
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-all text-sm">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                            {{ substr($student->user->first_name ?? 'S', 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-900 block">{{ $student->user->first_name }} {{ $student->user->last_name }}</span>
                                            <span class="text-xs text-slate-400 block">{{ $student->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-600 text-xs">
                                    {{ $student->roll_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($status === 'PRESENT')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Present
                                        </span>
                                    @elseif($status === 'ABSENT')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Absent
                                        </span>
                                    @elseif($status === 'LATE')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Late
                                        </span>
                                    @elseif($status === 'EXCUSED')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                            Excused
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Unmarked
                                        </span>
                                    @endif
                                    @if($record && isset($record['ip_address']))
                                        <span class="text-[10px] text-slate-400 block mt-1" title="IP: {{ $record['ip_address'] }}">
                                            Via Check-in
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button wire:click="markAttendance('{{ $student->user_id }}', 'PRESENT')" 
                                            class="h-8 px-2.5 rounded-lg border text-xs font-bold transition-all shadow-sm {{ $status === 'PRESENT' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' }}">
                                            P
                                        </button>
                                        <button wire:click="markAttendance('{{ $student->user_id }}', 'ABSENT')" 
                                            class="h-8 px-2.5 rounded-lg border text-xs font-bold transition-all shadow-sm {{ $status === 'ABSENT' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' }}">
                                            A
                                        </button>
                                        <button wire:click="markAttendance('{{ $student->user_id }}', 'LATE')" 
                                            class="h-8 px-2.5 rounded-lg border text-xs font-bold transition-all shadow-sm {{ $status === 'LATE' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' }}">
                                            L
                                        </button>
                                        <button wire:click="markAttendance('{{ $student->user_id }}', 'EXCUSED')" 
                                            class="h-8 px-2.5 rounded-lg border text-xs font-bold transition-all shadow-sm {{ $status === 'EXCUSED' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' }}">
                                            E
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
