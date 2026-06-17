<div class="space-y-8">
    <!-- Print Stylesheet -->
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            /* Hide sidebar, headers, control buttons */
            aside, nav, header, .no-print, button, select, .breadcrumbs {
                display: none !important;
            }
            main, .main-content, .print-container {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .print-border {
                border: 2px double #1e293b !important;
                padding: 30px !important;
                margin: 20px !important;
                background: white !important;
            }
            .print-text-dark {
                color: #000000 !important;
            }
            .print-bg-light {
                background-color: #f1f5f9 !important;
            }
        }
    </style>

    <!-- Header Panel (no-print) -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5 no-print">
        <div>
            <h2 class="text-xl font-bold text-white">Transcript & Grade Engine</h2>
            <p class="text-slate-400 text-sm mt-1">Compile official, semester-wise, and degree fulfillment transcripts. Generates academic stand standings, computes CGPA, and prints formal documents.</p>
        </div>
        <div class="flex gap-3">
            @if($studentProfile)
                <button wire:click="saveTranscriptRecord" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-750 font-semibold text-xs text-slate-350 transition-all border border-white/5">
                    💾 Log Compilation
                </button>
                <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-xs text-white transition-all shadow-md shadow-blue-600/10">
                    🖨️ Print Transcript
                </button>
            @endif
        </div>
    </div>

    <!-- Selection Dashboard (no-print) -->
    <div class="grid grid-cols-4 gap-6 no-print">
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4 col-span-1">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider pb-2 border-b border-white/5">Select Student</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-450 mb-1">Student Directory</label>
                    <select wire:model="selectedStudentId" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                        <option value="">Select Student...</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->first_name }} {{ $st->last_name }} ({{ $st->email }})</option>
                        @endforeach
                    </select>
                </div>
                
                <button wire:click="loadTranscript" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs transition-colors">
                    Generate Transcript
                </button>
            </div>

            <!-- Saved logs -->
            @if($savedTranscripts)
                <div class="pt-4 border-t border-white/5">
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase mb-2">Saved compilations</h4>
                    <div class="space-y-2 max-h-[150px] overflow-y-auto">
                        @foreach($savedTranscripts as $t)
                            <div class="p-2 bg-slate-900/60 rounded-lg text-[10px] text-slate-400 flex justify-between">
                                <span>CGPA: <strong>{{ number_format($t->cgpa, 2) }}</strong></span>
                                <span>{{ $t->compiled_at }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Transcript Display Panel -->
        <div class="col-span-3 bg-white/5 border border-white/10 rounded-2xl p-6">
            @if($studentProfile)
                <!-- View Tabs -->
                <div class="flex border-b border-white/5 mb-6 gap-2">
                    <button wire:click="$set('viewType', 'official')" class="px-4 py-2 text-xs font-bold transition-all border-b-2 {{ $viewType === 'official' ? 'border-blue-500 text-white' : 'border-transparent text-slate-450' }}">Official Transcript</button>
                    <button wire:click="$set('viewType', 'semester')" class="px-4 py-2 text-xs font-bold transition-all border-b-2 {{ $viewType === 'semester' ? 'border-blue-500 text-white' : 'border-transparent text-slate-450' }}">Semester Transcript</button>
                    <button wire:click="$set('viewType', 'degree')" class="px-4 py-2 text-xs font-bold transition-all border-b-2 {{ $viewType === 'degree' ? 'border-blue-500 text-white' : 'border-transparent text-slate-450' }}">Degree Audit</button>
                </div>

                <!-- Livewire feedback messages -->
                @if(session()->has('success'))
                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs rounded-xl mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- RENDER FORMAL TRANSCRIPT SHEET -->
                <div class="bg-slate-950 p-8 rounded-xl border border-white/5 text-slate-300 print-border print-text-dark">
                    <!-- Heading -->
                    <div class="text-center space-y-2 pb-6 border-b border-white/10 print-border-b">
                        <h1 class="text-2xl font-black text-white print-text-dark uppercase tracking-widest">{{ optional(app('currentTenant'))->name ?? 'NANDSKILLS UNIVERSITY' }}</h1>
                        <p class="text-xs uppercase font-bold text-slate-400 tracking-wider">Office of the Registrar — Official Academic Transcript</p>
                    </div>

                    <!-- Student Metadata grid -->
                    <div class="grid grid-cols-2 gap-4 py-6 text-xs border-b border-white/5">
                        <div class="space-y-1">
                            <div>Student Name: <strong class="text-white print-text-dark">{{ $studentProfile->user->first_name }} {{ $studentProfile->user->last_name }}</strong></div>
                            <div>Roll Number: <strong class="text-white print-text-dark">{{ $studentProfile->roll_number }}</strong></div>
                            <div>Admission Date: <strong>{{ $studentProfile->admission_date ? $studentProfile->admission_date->format('M d, Y') : 'N/A' }}</strong></div>
                        </div>
                        <div class="space-y-1">
                            <div>Academic Program: <strong class="text-white print-text-dark">{{ $studentProfile->program->name ?? 'BS Computer Science' }}</strong></div>
                            <div>Department: <strong>Computer Science</strong></div>
                            <div>Degree Status: <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 uppercase">{{ $studentProfile->status }}</span></div>
                        </div>
                    </div>

                    <!-- Grade matrix based on selection view -->
                    <div class="py-6">
                        @if($viewType === 'official' || $viewType === 'semester')
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-white/10 font-bold text-slate-400 uppercase tracking-wider text-[10px]">
                                        <th class="py-2.5">Code</th>
                                        <th class="py-2.5">Course Subject</th>
                                        <th class="py-2.5 text-center">Semester</th>
                                        <th class="py-2.5 text-center">Score</th>
                                        <th class="py-2.5 text-center">Grade</th>
                                        <th class="py-2.5 text-right">Points</th>
                                        <th class="py-2.5 text-right">Credits</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @forelse($coursesData as $course)
                                        @if($viewType === 'official' || $course['semester_no'] == $selectedSemesterNo)
                                            <tr class="hover:bg-white/5 transition-colors">
                                                <td class="py-3 font-mono">{{ $course['course_code'] }}</td>
                                                <td class="py-3 font-bold text-white print-text-dark">{{ $course['course_title'] }}</td>
                                                <td class="py-3 text-center">{{ $course['semester_no'] }}</td>
                                                <td class="py-3 text-center">{{ $course['overall_score'] }}%</td>
                                                <td class="py-3 text-center font-bold text-blue-400 print-text-dark">{{ $course['grade'] }}</td>
                                                <td class="py-3 text-right">{{ number_format($course['points'], 2) }}</td>
                                                <td class="py-3 text-right">{{ number_format($course['credits'], 1) }}</td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-8 text-center text-slate-500 italic">No course grade records found for this student.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        @elseif($viewType === 'degree')
                            <!-- Degree Audit Summary -->
                            <div class="space-y-6">
                                <h3 class="text-sm font-bold text-white print-text-dark">Degree Completion Progress</h3>
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span>Credits Earned: {{ $totalCredits }} / {{ $studentProfile->program->credit_hours_required ?? 130 }}</span>
                                        <span>{{ round(($totalCredits / ($studentProfile->program->credit_hours_required ?? 130)) * 100) }}% Complete</span>
                                    </div>
                                    <div class="w-full bg-slate-900 h-2.5 rounded-full overflow-hidden border border-white/5">
                                        <div class="bg-blue-600 h-full" style="width: {{ min(100, ($totalCredits / ($studentProfile->program->credit_hours_required ?? 130)) * 100) }}%"></div>
                                    </div>
                                </div>
                                <div class="p-4 bg-white/5 rounded-xl border border-white/5 space-y-2 text-xs">
                                    <div>Program Code: <strong>{{ $studentProfile->program->code ?? 'N/A' }}</strong></div>
                                    <div>Duration Years: <strong>{{ $studentProfile->program->duration_years ?? 4 }} Years</strong></div>
                                    <div>Degree Requirements Met: <strong>{{ $totalCredits >= ($studentProfile->program->credit_hours_required ?? 130) ? 'YES' : 'NO (Requirements Pending)' }}</strong></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- GPA / Credit Summary Footer -->
                    @if($viewType !== 'degree')
                        <div class="grid grid-cols-3 gap-6 p-4 rounded-xl bg-slate-900 border border-white/5 print-bg-light text-center text-xs">
                            <div>
                                <span class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Total Credits Earned</span>
                                <strong class="text-white print-text-dark text-lg">{{ number_format($totalCredits, 1) }}</strong>
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Cumulative CGPA</span>
                                <strong class="text-white print-text-dark text-lg">{{ number_format($this->cgpa, 2) }}</strong>
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Academic Standing</span>
                                <span class="block px-2.5 py-0.5 rounded font-bold uppercase tracking-wider text-[11px] mt-1 w-fit mx-auto bg-blue-500/10 text-blue-450 border border-blue-500/20">
                                    {{ $academicStanding }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Validation QR / Verification Stamp & Signature Blocks -->
                    <div class="grid grid-cols-2 gap-8 pt-12 text-xs">
                        <div class="space-y-4">
                            <div class="h-20 w-20 bg-slate-900/60 flex items-center justify-center border border-white/10 rounded-lg print-border text-center text-[8px] leading-tight text-slate-500">
                                SECURE VERIFICATION CODE
                            </div>
                            <div class="text-[10px] text-slate-500">
                                Compiled Date: {{ now()->toFormattedDateString() }}<br>
                                Verification Ref: {{ hash('sha256', $studentProfile->roll_number . now()->toDateString()) }}
                            </div>
                        </div>
                        <div class="text-right flex flex-col justify-end space-y-1">
                            <div class="border-b border-white/20 w-48 ml-auto h-12"></div>
                            <div class="font-bold text-white print-text-dark uppercase tracking-wider">Controller of Examinations</div>
                            <div class="text-[10px] text-slate-500">Registrar Seal Authority</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center text-slate-500 text-xs italic py-24">
                    Select a student from the directory sidebar to generate and display the transcript sheet.
                </div>
            @endif
        </div>
    </div>
</div>
