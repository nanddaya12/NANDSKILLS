<div class="space-y-8">
    <!-- Student selector and Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5 gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Parent Engagement Desk</h2>
            <p class="text-slate-400 text-sm mt-1">Monitor your linked child's course registry, attendance records, exam transcripts, and contact course teachers.</p>
        </div>
        
        <div>
            <label class="block text-[10px] uppercase tracking-wider text-slate-400 mb-1 font-bold">Select Student Profile</label>
            <select wire:model="selectedStudentUserId" class="bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 text-sm font-semibold">
                @foreach($linkedStudents as $link)
                    <option value="{{ $link->student_user_id }}">{{ $link->student->first_name }} {{ $link->student->last_name }} ({{ $link->relationship }})</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($selectedStudentProfile)
        <!-- Dashboard Navigation -->
        <div class="flex border-b border-white/10 gap-2 overflow-x-auto">
            <button wire:click="setTab('progress')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'progress' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
                Academic Progress
            </button>
            <button wire:click="setTab('behavior')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'behavior' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
                Behavior Reports & Warnings ({{ count($behaviorReports) + count($warnings) }})
            </button>
            <button wire:click="setTab('messages')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'messages' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
                Teacher Chat
            </button>
        </div>

        @if($activeTab === 'progress')
            <!-- Progress Tab Content -->
            <div class="grid grid-cols-3 gap-6">
                <!-- Left: academic enrollments & standing -->
                <div class="col-span-2 space-y-6">
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/5 space-y-4">
                        <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Academic Enrolments & CGPA Standing</h3>
                        <div class="space-y-4">
                            @forelse($enrollments as $enr)
                                <div class="p-4 bg-slate-950/30 rounded-xl border border-white/5 flex justify-between items-center">
                                    <div>
                                        <div class="font-bold text-white">{{ $enr->program->name ?? 'Degree Program' }}</div>
                                        <div class="text-[11px] text-slate-500 mt-1">Semester {{ $enr->current_semester }} — Session: {{ $enr->academicSession->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-slate-450">CGPA</div>
                                        <div class="text-lg font-black text-blue-450">{{ $enr->cumulative_cgpa ?? '0.00' }}</div>
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[9px] font-bold border border-emerald-500/20 mt-1 inline-block">
                                            {{ $enr->academic_standing }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-xs text-slate-500 py-6 text-center">No active enrollment history.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Exam Grades -->
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/5 space-y-4">
                        <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Recent Exam Grades</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-350">
                                <thead>
                                    <tr class="border-b border-white/5 text-slate-500 uppercase tracking-wider">
                                        <th class="py-2.5 px-3">Exam Module</th>
                                        <th class="py-2.5 px-3">Marks</th>
                                        <th class="py-2.5 px-3">Grade Letter</th>
                                        <th class="py-2.5 px-3">Grade Points</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @forelse($examResults as $res)
                                        <tr>
                                            <td class="py-3 px-3 font-semibold text-white">{{ $res->exam->title ?? 'Exam' }}</td>
                                            <td class="py-3 px-3 font-bold text-blue-450">{{ $res->marks }} / {{ $res->total_marks ?? 100 }}</td>
                                            <td class="py-3 px-3 text-white font-bold">{{ $res->grade_letter ?? 'N/A' }}</td>
                                            <td class="py-3 px-3 text-slate-400">{{ $res->grade_points ?? '0.0' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-6 text-center text-slate-500 italic">No exams recorded yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Recent attendance stats -->
                <div class="p-6 rounded-2xl bg-white/5 border border-white/5 space-y-4 h-fit">
                    <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Recent Attendance Log</h3>
                    <div class="space-y-3">
                        @forelse($attendances->take(10) as $att)
                            <div class="flex justify-between items-center p-3 bg-slate-950/30 rounded-xl border border-white/5 text-xs">
                                <div>
                                    <div class="font-bold text-white">{{ $att->date->format('M d, Y') }}</div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">Session: {{ $att->session ?? 'Regular' }}</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                    {{ $att->status === 'PRESENT' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $att->status === 'ABSENT' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}
                                    {{ $att->status === 'LATE' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                ">
                                    {{ $att->status }}
                                </span>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 py-6 text-center">No attendance logs found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @elseif($activeTab === 'behavior')
            <!-- Behavior and warnings Tab -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Academic Warnings -->
                <div class="p-6 rounded-2xl bg-white/5 border border-white/5 space-y-4">
                    <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Performance & Academic Warnings</h3>
                    <div class="space-y-4">
                        @forelse($warnings as $warn)
                            <div class="p-4 rounded-xl bg-amber-500/5 border border-amber-500/20 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-amber-400 uppercase">{{ str_replace('_', ' ', $warn->warning_type) }}</span>
                                    <span class="text-[10px] text-slate-500">{{ $warn->created_at->format('M d, Y') }}</span>
                                </div>
                                <p class="text-xs text-slate-300 leading-relaxed">{{ $warn->description }}</p>
                                <div class="flex items-center gap-4 text-[10px] text-slate-450 border-t border-white/5 pt-2">
                                    <span>CGPA at Warning: {{ $warn->cgpa_at_warning ?? 'N/A' }}</span>
                                    <span>Attendance: {{ $warn->attendance_pct_at_warning ? $warn->attendance_pct_at_warning . '%' : 'N/A' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 py-8 text-center italic">No academic warnings issued. High performance!</div>
                        @endforelse
                    </div>
                </div>

                <!-- Disciplinary/Behavior incident files -->
                <div class="p-6 rounded-2xl bg-white/5 border border-white/5 space-y-4">
                    <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Disciplinary & Conduct incidents</h3>
                    <div class="space-y-4">
                        @forelse($behaviorReports as $rep)
                            <div class="p-4 rounded-xl bg-rose-500/5 border border-rose-500/20 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-rose-450 uppercase">{{ $rep->incident_type }}</span>
                                    <span class="text-[10px] text-slate-500">{{ $rep->incident_date->format('M d, Y') }}</span>
                                </div>
                                <p class="text-xs text-slate-300 leading-relaxed">{{ $rep->description }}</p>
                                @if($rep->action_taken)
                                    <div class="text-[10px] text-slate-400 mt-2 bg-slate-900/50 p-2 rounded border border-white/5">
                                        <span class="font-bold text-white">Action Taken:</span> {{ $rep->action_taken }}
                                    </div>
                                @endif
                                <div class="text-[9px] text-slate-500 text-right mt-1">Reported by: {{ $rep->reporter->first_name ?? 'Staff' }}</div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 py-8 text-center italic">No disciplinary incidents reported. Excellent behavioral standing!</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @elseif($activeTab === 'messages')
            <!-- Teacher Chat Tab -->
            <div class="grid grid-cols-3 gap-6">
                <!-- Left: Send message -->
                <div class="p-6 rounded-2xl bg-white/5 border border-white/10 h-fit space-y-4">
                    <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Contact Instructor</h3>
                    <form wire:submit.prevent="sendMessage" class="space-y-4">
                        <div>
                            <label class="block text-xs text-slate-350 mb-1">Select Instructor</label>
                            <select wire:model="selectedTeacherId" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500">
                                <option value="">Choose Teacher...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-350 mb-1">Subject</label>
                            <input type="text" wire:model.defer="messageSubject" placeholder="e.g. Query about grade result" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-350 mb-1">Message Body</label>
                            <textarea wire:model.defer="messageBody" rows="4" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs transition-colors">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Right: Messages list -->
                <div class="col-span-2 p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md flex flex-col h-[500px]">
                    <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2 mb-4">Chat Thread</h3>
                    <div class="flex-1 overflow-y-auto space-y-4 pr-2 pb-4">
                        @forelse($messages as $msg)
                            @php
                                $isMe = $msg->sender_user_id === auth()->id();
                            @endphp
                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-md p-3.5 rounded-2xl border text-xs 
                                    {{ $isMe ? 'bg-blue-600 text-white border-blue-500/20 rounded-tr-none' : 'bg-slate-950/40 text-slate-200 border-white/5 rounded-tl-none' }}
                                ">
                                    <div class="font-bold mb-1 {{ $isMe ? 'text-blue-100' : 'text-blue-400' }}">
                                        {{ $isMe ? 'You' : ($msg->sender->first_name . ' ' . $msg->sender->last_name) }}
                                    </div>
                                    @if($msg->subject)
                                        <div class="font-bold underline mb-1">{{ $msg->subject }}</div>
                                    @endif
                                    <p class="leading-relaxed">{{ $msg->body }}</p>
                                    <div class="text-[9px] text-right mt-1.5 opacity-60">{{ $msg->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 py-12 text-center italic">No messages exchanged yet. Send a message to start a conversation.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="p-12 rounded-2xl bg-white/5 border border-white/5 text-center text-slate-500 italic">
            You do not have any linked child/student profiles. Contact Academy Admin to link your parent account.
        </div>
    @endif
</div>
