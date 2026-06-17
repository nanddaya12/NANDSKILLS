<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Academic Configuration Desk</h2>
            <p class="text-slate-400 text-sm mt-1">Configure academic years, sessions, departments/faculties, grading rules, cgpa standings, and academic events.</p>
        </div>
        <button wire:click="openForm" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-sm transition-all shadow-md shadow-blue-600/10">
            Add New Record
        </button>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex border-b border-white/10 gap-2 overflow-x-auto">
        <button wire:click="setTab('years')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'years' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Academic Years
        </button>
        <button wire:click="setTab('sessions')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'sessions' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Academic Sessions
        </button>
        <button wire:click="setTab('faculties')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'faculties' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Faculties
        </button>
        <button wire:click="setTab('grading')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'grading' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Grading Scale
        </button>
        <button wire:click="setTab('cgpa')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'cgpa' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            CGPA Standings
        </button>
        <button wire:click="setTab('calendar')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 {{ $activeTab === 'calendar' ? 'border-blue-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }}">
            Calendar Events
        </button>
    </div>

    <!-- Form Section -->
    @if($isFormOpen)
        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md max-w-2xl mx-auto space-y-6">
            <div class="flex justify-between items-center border-b border-white/10 pb-4">
                <h3 class="text-lg font-bold text-white">Create New {{ ucfirst(rtrim($activeTab, 's')) }}</h3>
                <button wire:click="$set('isFormOpen', false)" class="text-slate-450 hover:text-white">✕</button>
            </div>

            <!-- Form Content based on tab -->
            @if($activeTab === 'years')
                <form wire:submit.prevent="saveYear" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Year Name</label>
                        <input type="text" wire:model.defer="year_name" required placeholder="e.g. 2026-2027" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Start Date</label>
                            <input type="date" wire:model.defer="year_start_date" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">End Date</label>
                            <input type="date" wire:model.defer="year_end_date" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Description</label>
                        <textarea wire:model.defer="year_description" rows="3" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model.defer="year_is_current" id="year_is_current" class="rounded bg-slate-900 border-white/10 text-blue-600">
                        <label for="year_is_current" class="text-sm text-slate-300">Set as Current Academic Year</label>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors mt-4">Save Academic Year</button>
                </form>
            @elseif($activeTab === 'sessions')
                <form wire:submit.prevent="saveSession" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Academic Year</label>
                        <select wire:model.defer="selectedSessionYearId" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            <option value="">Select Year</option>
                            @foreach($years as $yr)
                                <option value="{{ $yr->id }}">{{ $yr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Session/Semester Name</label>
                        <input type="text" wire:model.defer="session_name" required placeholder="e.g. Fall 2026" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Session Type</label>
                            <select wire:model.defer="session_type" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                <option value="SEMESTER">SEMESTER</option>
                                <option value="TERM">TERM</option>
                                <option value="QUARTER">QUARTER</option>
                                <option value="TRIMESTER">TRIMESTER</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Initial Status</label>
                            <select wire:model.defer="session_status" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                <option value="UPCOMING">UPCOMING</option>
                                <option value="ACTIVE">ACTIVE</option>
                                <option value="COMPLETED">COMPLETED</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Start Date</label>
                            <input type="date" wire:model.defer="session_start_date" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">End Date</label>
                            <input type="date" wire:model.defer="session_end_date" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model.defer="session_is_current" id="session_is_current" class="rounded bg-slate-900 border-white/10 text-blue-600">
                        <label for="session_is_current" class="text-sm text-slate-300">Set as Current Session</label>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors mt-4">Save Session</button>
                </form>
            @elseif($activeTab === 'faculties')
                <form wire:submit.prevent="saveFaculty" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Faculty/College Name</label>
                        <input type="text" wire:model.defer="faculty_name" required placeholder="e.g. Faculty of Computer Science" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Faculty Code</label>
                        <input type="text" wire:model.defer="faculty_code" required placeholder="e.g. FCS" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Dean / Department Head</label>
                        <select wire:model.defer="faculty_head_user_id" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            <option value="">Select Dean</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->first_name }} {{ $staff->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Description</label>
                        <textarea wire:model.defer="faculty_description" rows="3" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm"></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors mt-4">Save Faculty</button>
                </form>
            @elseif($activeTab === 'grading')
                <form wire:submit.prevent="saveGradingRule" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Grade Letter</label>
                            <input type="text" wire:model.defer="grade_letter" required placeholder="e.g. A+" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Grade Points (GPA)</label>
                            <input type="number" step="0.01" wire:model.defer="grade_points" required placeholder="e.g. 4.00" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Min Marks (%)</label>
                            <input type="number" step="0.01" wire:model.defer="grade_min_score" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Max Marks (%)</label>
                            <input type="number" step="0.01" wire:model.defer="grade_max_score" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Status</label>
                        <select wire:model.defer="grade_status" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            <option value="PASS">PASS</option>
                            <option value="FAIL">FAIL</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors mt-4">Save Grading Rule</button>
                </form>
            @elseif($activeTab === 'cgpa')
                <form wire:submit.prevent="saveCgpaRule" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Standing Name</label>
                        <input type="text" wire:model.defer="cgpa_standing_name" required placeholder="e.g. Dean's List, Good Standing" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Min CGPA</label>
                            <input type="number" step="0.01" wire:model.defer="cgpa_min" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Max CGPA</label>
                            <input type="number" step="0.01" wire:model.defer="cgpa_max" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Status Tag</label>
                        <select wire:model.defer="cgpa_status_tag" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            <option value="EXCELLENT">EXCELLENT</option>
                            <option value="GOOD">GOOD</option>
                            <option value="WARNING">WARNING</option>
                            <option value="PROBATION">PROBATION</option>
                            <option value="DISMISSED">DISMISSED</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Description</label>
                        <textarea wire:model.defer="cgpa_description" rows="3" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm"></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors mt-4">Save Standing Rule</button>
                </form>
            @elseif($activeTab === 'calendar')
                <form wire:submit.prevent="saveCalendarEvent" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Academic Year</label>
                            <select wire:model.defer="event_year_id" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                <option value="">Select Year</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr->id }}">{{ $yr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Academic Session (Optional)</label>
                            <select wire:model.defer="event_session_id" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                <option value="">Select Session</option>
                                @foreach($sessions as $sess)
                                    <option value="{{ $sess->id }}">{{ $sess->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Event Title</label>
                        <input type="text" wire:model.defer="event_title" required placeholder="e.g. Midterm Exams" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Event Date</label>
                            <input type="date" wire:model.defer="event_date" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">End Date (Optional)</label>
                            <input type="date" wire:model.defer="event_end_date" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Event Type</label>
                            <select wire:model.defer="event_type" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                                <option value="GENERAL">GENERAL</option>
                                <option value="HOLIDAY">HOLIDAY</option>
                                <option value="EXAM">EXAM</option>
                                <option value="REGISTRATION">REGISTRATION</option>
                                <option value="ORIENTATION">ORIENTATION</option>
                                <option value="BREAK">BREAK</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Holiday?</label>
                            <div class="flex items-center h-12">
                                <input type="checkbox" wire:model.defer="event_is_holiday" id="event_is_holiday" class="rounded bg-slate-900 border-white/10 text-blue-600">
                                <label for="event_is_holiday" class="ml-2 text-sm text-slate-300">Yes</label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Color Tag</label>
                            <input type="color" wire:model.defer="event_color" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl h-12 p-1 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Description</label>
                        <textarea wire:model.defer="event_description" rows="3" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm"></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors mt-4">Save Calendar Event</button>
                </form>
            @endif
        </div>
    @else
        <!-- Grid tables / lists -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
            @if($activeTab === 'years')
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                                <th class="py-3 px-4">Academic Year</th>
                                <th class="py-3 px-4">Duration</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($years as $yr)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-4 font-bold text-white flex items-center gap-2">
                                        {{ $yr->name }}
                                        @if($yr->is_current)
                                            <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">CURRENT</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-xs">{{ $yr->start_date->format('M d, Y') }} - {{ $yr->end_date->format('M d, Y') }}</td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">{{ $yr->status }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-right space-x-2">
                                        @if(!$yr->is_current)
                                            <button wire:click="makeCurrentYear('{{ $yr->id }}')" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-1.5 rounded-lg">Make Current</button>
                                        @endif
                                        <button onclick="confirm('Delete this year?') || event.stopImmediatePropagation()" wire:click="deleteItem('AcademicYear', '{{ $yr->id }}')" class="text-xs text-rose-400 hover:text-rose-350">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-slate-500">No academic years configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif($activeTab === 'sessions')
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                                <th class="py-3 px-4">Session Name</th>
                                <th class="py-3 px-4">Academic Year</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">Duration</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($sessions as $sess)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-4 font-bold text-white flex items-center gap-2">
                                        {{ $sess->name }}
                                        @if($sess->is_current)
                                            <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">CURRENT</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-slate-400">{{ $sess->academicYear->name }}</td>
                                    <td class="py-4 px-4 text-xs font-semibold">{{ $sess->type }}</td>
                                    <td class="py-4 px-4 text-xs">{{ $sess->start_date->format('M d, Y') }} - {{ $sess->end_date->format('M d, Y') }}</td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-bold border border-amber-500/20">{{ $sess->status }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-right space-x-2">
                                        @if(!$sess->is_current)
                                            <button wire:click="makeCurrentSession('{{ $sess->id }}')" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-1.5 rounded-lg">Make Current</button>
                                        @endif
                                        <button onclick="confirm('Delete this session?') || event.stopImmediatePropagation()" wire:click="deleteItem('AcademicSession', '{{ $sess->id }}')" class="text-xs text-rose-400 hover:text-rose-350">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500">No sessions configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif($activeTab === 'faculties')
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                                <th class="py-3 px-4">Faculty Code</th>
                                <th class="py-3 px-4">Faculty Name</th>
                                <th class="py-3 px-4">Dean/Head</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($faculties as $fac)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-4 font-bold text-blue-450">{{ $fac->code }}</td>
                                    <td class="py-4 px-4 font-semibold text-white">{{ $fac->name }}</td>
                                    <td class="py-4 px-4 text-xs text-slate-400">{{ $fac->head ? $fac->head->first_name . ' ' . $fac->head->last_name : 'N/A' }}</td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">{{ $fac->status }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button onclick="confirm('Delete this faculty?') || event.stopImmediatePropagation()" wire:click="deleteItem('Faculty', '{{ $fac->id }}')" class="text-xs text-rose-400 hover:text-rose-350">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-500">No faculties configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif($activeTab === 'grading')
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                                <th class="py-3 px-4">Grade Letter</th>
                                <th class="py-3 px-4">Grade Points</th>
                                <th class="py-3 px-4">Min score (%)</th>
                                <th class="py-3 px-4">Max score (%)</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($gradingRules as $rule)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-4 font-bold text-white text-base">{{ $rule->grade_letter }}</td>
                                    <td class="py-4 px-4 font-semibold text-blue-400">{{ $rule->grade_points }}</td>
                                    <td class="py-4 px-4 text-xs">{{ $rule->min_score }}%</td>
                                    <td class="py-4 px-4 text-xs">{{ $rule->max_score }}%</td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 rounded-full {{ $rule->status === 'PASS' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20' }} text-[10px] font-bold border">
                                            {{ $rule->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button onclick="confirm('Delete this rule?') || event.stopImmediatePropagation()" wire:click="deleteItem('GradingRule', '{{ $rule->id }}')" class="text-xs text-rose-400 hover:text-rose-350">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500">No grading rules configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif($activeTab === 'cgpa')
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                                <th class="py-3 px-4">Standing Name</th>
                                <th class="py-3 px-4">Min CGPA</th>
                                <th class="py-3 px-4">Max CGPA</th>
                                <th class="py-3 px-4">Status Tag</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($cgpaRules as $rule)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-4 font-bold text-white">{{ $rule->standing_name }}</td>
                                    <td class="py-4 px-4 text-slate-400">{{ $rule->min_cgpa }}</td>
                                    <td class="py-4 px-4 text-slate-400">{{ $rule->max_cgpa }}</td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 rounded-full bg-slate-900 border border-white/10 text-slate-300 text-[10px] font-bold">
                                            {{ $rule->status_tag }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button onclick="confirm('Delete this standing rule?') || event.stopImmediatePropagation()" wire:click="deleteItem('CgpaRule', '{{ $rule->id }}')" class="text-xs text-rose-400 hover:text-rose-350">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-500">No CGPA standings configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif($activeTab === 'calendar')
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                                <th class="py-3 px-4">Event Date</th>
                                <th class="py-3 px-4">Event Type</th>
                                <th class="py-3 px-4">Title</th>
                                <th class="py-3 px-4">Holiday?</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($calendarEvents as $event)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-4 font-semibold text-white text-xs">
                                        {{ $event->event_date->format('M d, Y') }}
                                        @if($event->event_end_date)
                                            - {{ $event->event_end_date->format('M d, Y') }}
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-xs font-bold" style="color: {{ $event->color }}">{{ $event->event_type }}</td>
                                    <td class="py-4 px-4 text-slate-300">
                                        <div class="font-semibold">{{ $event->title }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $event->description }}</div>
                                    </td>
                                    <td class="py-4 px-4 text-xs">
                                        @if($event->is_holiday)
                                            <span class="text-emerald-400 font-semibold">Yes</span>
                                        @else
                                            <span class="text-slate-500">No</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button onclick="confirm('Delete this event?') || event.stopImmediatePropagation()" wire:click="deleteItem('AcademicCalendar', '{{ $event->id }}')" class="text-xs text-rose-400 hover:text-rose-350">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-500">No calendar events configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
