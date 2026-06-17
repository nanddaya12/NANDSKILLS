<div class="flex flex-col lg:flex-row gap-8 min-h-[calc(100vh-8rem)]">
    <!-- Left Navigation Menu (Module Selector) -->
    <aside class="w-full lg:w-64 bg-white border border-slate-200 rounded-2xl shadow-sm p-4 shrink-0 space-y-1">
        <div class="px-3 py-2 mb-3 border-b border-slate-100 pb-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Enterprise Modules</h3>
        </div>
        
        <button wire:click="setTab('rbac')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'rbac' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>🔑 Advanced RBAC System</span>
        </button>

        <button wire:click="setTab('campus')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'campus' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>🏫 Multi Campus Management</span>
        </button>

        <button wire:click="setTab('exams')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'exams' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>📝 Examination System</span>
        </button>

        <button wire:click="setTab('automations')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'automations' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>⚡ Workflow Automation</span>
        </button>

        <button wire:click="setTab('forms')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'forms' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>📋 Form Builder</span>
        </button>

        <button wire:click="setTab('gamification')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'gamification' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>🏆 Gamification Engine</span>
        </button>

        <button wire:click="setTab('hr')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'hr' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>💼 HR & Payroll</span>
        </button>

        <button wire:click="setTab('assets')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'assets' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>📦 Inventory & Assets</span>
        </button>

        <button wire:click="setTab('library')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'library' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>📚 Library Management</span>
        </button>

        <button wire:click="setTab('accreditation')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'accreditation' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>🛡️ Compliance & Audits</span>
        </button>

        <button wire:click="setTab('billing')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'billing' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>💳 Subscription & Usages</span>
        </button>

        <button wire:click="setTab('api')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'api' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
            <span>🔑 API Keys & Integration</span>
        </button>
    </aside>

    <!-- Right Workspace Area -->
    <div class="flex-1 space-y-6">
        <!-- Status Messages -->
        @if (session()->has('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center gap-3 shadow-sm">
                <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <!-- TAB CONTENT: RBAC -->
        @if($activeTab === 'rbac')
            <div class="space-y-6">
                <!-- KPI Widgets -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Defined Roles</span>
                        <h4 class="text-3xl font-bold text-slate-900 mt-2">{{ count($roles) }}</h4>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Access Requests</span>
                        <h4 class="text-3xl font-bold text-amber-600 mt-2">{{ count($accessRequests->where('status', 'PENDING')) }} Pending</h4>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Audited Logins</span>
                        <h4 class="text-3xl font-bold text-blue-600 mt-2">{{ count($loginHistories) }} Logs</h4>
                    </div>
                </div>

                <!-- Custom Tenant Roles Builder -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h4 class="font-bold text-slate-900 text-base">Custom Role Builder</h4>
                    <form wire:submit.prevent="createCustomRole" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Role Name</label>
                            <input type="text" wire:model="newRoleName" placeholder="e.g. Course Auditor" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200/50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Role Description</label>
                            <input type="text" wire:model="newRoleDescription" placeholder="Optional details..." class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200/50">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                            Create Role
                        </button>
                    </form>
                </div>

                <!-- Access Request Logs -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-base">Access Approvals Request Board</h3>
                    </div>
                    @if(count($accessRequests) === 0)
                        <div class="text-center py-10 text-slate-400 text-sm">No access requests.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                                        <th class="px-6 py-3">User</th>
                                        <th class="px-6 py-3">Requested Role</th>
                                        <th class="px-6 py-3">Reason</th>
                                        <th class="px-6 py-3 text-center">Status</th>
                                        <th class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($accessRequests as $req)
                                        <tr>
                                            <td class="px-6 py-4 font-semibold text-slate-950">{{ $req->user->first_name }} {{ $req->user->last_name }}</td>
                                            <td class="px-6 py-4">{{ $req->requested_role }}</td>
                                            <td class="px-6 py-4 text-xs text-slate-500">{{ $req->reason }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $req->status === 'APPROVED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                                    {{ $req->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                @if($req->status === 'PENDING')
                                                    <button wire:click="approveAccessRequest('{{ $req->id }}')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm">
                                                        Approve
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: CAMPUS -->
        @if($activeTab === 'campus')
            <div class="space-y-6">
                <!-- Grid of Builders -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- 1. Building Creator -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">🏢 Add Campus Building</h4>
                        <form wire:submit.prevent="createBuilding" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Branch</label>
                                <select wire:model="selectedBranch" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose Branch --</option>
                                    @foreach($branches as $br)
                                        <option value="{{ $br->id }}">{{ $br->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Building Name</label>
                                <input type="text" wire:model="newBuildingName" placeholder="e.g. Science Block" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Description</label>
                                <input type="text" wire:model="newBuildingDesc" placeholder="e.g. Labs & Auditoriums" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Create Building
                            </button>
                        </form>
                    </div>

                    <!-- 2. Department Creator -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">🎓 Add Department</h4>
                        <form wire:submit.prevent="createDepartment" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Branch</label>
                                <select wire:model="selectedBranch" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose Branch --</option>
                                    @foreach($branches as $br)
                                        <option value="{{ $br->id }}">{{ $br->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Department Name</label>
                                <input type="text" wire:model="newDepartmentName" placeholder="e.g. Computer Science" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Description</label>
                                <input type="text" wire:model="newDepartmentDesc" placeholder="e.g. Tech & Programming" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Create Department
                            </button>
                        </form>
                    </div>

                    <!-- 3. Room Creator -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">🏫 Add Room / Space</h4>
                        <form wire:submit.prevent="createRoom" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Building</label>
                                <select wire:model="selectedBuilding" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose Building --</option>
                                    @foreach($buildings as $bld)
                                        <option value="{{ $bld->id }}">{{ $bld->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Room Name</label>
                                <input type="text" wire:model="newRoomName" placeholder="e.g. Room 302" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Capacity</label>
                                <input type="number" wire:model="newRoomCapacity" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Create Room
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Active Rosters Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Buildings List -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Buildings</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($buildings) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($buildings as $bld)
                                <div class="p-4 flex flex-col">
                                    <span class="text-xs font-bold text-slate-800">{{ $bld->name }}</span>
                                    <span class="text-[10px] text-slate-500 mt-1">{{ $bld->description ?: 'No description' }}</span>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No buildings.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Departments List -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Departments</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($departments) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($departments as $dept)
                                <div class="p-4 flex flex-col">
                                    <span class="text-xs font-bold text-slate-800">{{ $dept->name }}</span>
                                    <span class="text-[10px] text-slate-500 mt-1">{{ $dept->description ?: 'No description' }}</span>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No departments.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Rooms List -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Classrooms / Rooms</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($rooms) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($rooms as $rm)
                                <div class="p-4 flex justify-between items-center">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-800">{{ $rm->name }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">{{ $rm->building->name }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100">Cap: {{ $rm->capacity }}</span>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No rooms.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: EXAMS -->
        @if($activeTab === 'exams')
            <div class="space-y-6">
                @if (session()->has('error'))
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium rounded-xl flex items-center gap-3 shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Column 1: Scheduling & Grading Forms -->
                    <div class="space-y-6">
                        <!-- Exam Scheduler -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">📝 Schedule New Exam</h4>
                            <form wire:submit.prevent="createExam" class="space-y-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Exam Session</label>
                                    <select wire:model="selectedExamSession" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                        <option value="">-- Choose Session --</option>
                                        @foreach($examSessions as $sess)
                                            <option value="{{ $sess->id }}">{{ $sess->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Course</label>
                                    <select wire:model="selectedCourse" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                        <option value="">-- Choose Course --</option>
                                        @foreach($courses as $c)
                                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Exam Title</label>
                                    <input type="text" wire:model="newExamTitle" placeholder="e.g. Midterm Programming Exam" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Type</label>
                                    <select wire:model="newExamType" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                        <option value="THEORY">Theory Paper</option>
                                        <option value="PRACTICAL">Practical Lab</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Total Marks</label>
                                    <input type="number" wire:model="newExamMarks" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                    Schedule Exam
                                </button>
                            </form>
                        </div>

                        <!-- Grade Entry Form -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">🎓 Enter Student Grades</h4>
                            <form wire:submit.prevent="submitExamResult" class="space-y-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Exam</label>
                                    <select wire:model="selectedExamForResult" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                        <option value="">-- Choose Exam --</option>
                                        @foreach($exams as $ex)
                                            <option value="{{ $ex->id }}">{{ $ex->title }} ({{ $ex->course->title }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Student</label>
                                    <select wire:model="selectedStudentForResult" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                        <option value="">-- Choose Student --</option>
                                        @foreach($students as $stud)
                                            <option value="{{ $stud->user->id }}">{{ $stud->user->first_name }} {{ $stud->user->last_name }} ({{ $stud->roll_number }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Marks Obtained</label>
                                    <input type="number" step="0.01" wire:model="obtainedMarks" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                </div>
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                    Submit Marks & Grade
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Column 2: Scheduled Exams & Results lists -->
                    <div class="space-y-6">
                        <!-- Exams list -->
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Exams Schedule</h5>
                                <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($exams) }}</span>
                            </div>
                            <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                                @forelse($exams as $ex)
                                    <div class="p-4 flex justify-between items-center">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-800">{{ $ex->title }}</span>
                                            <span class="text-[10px] text-slate-500 mt-0.5">{{ $ex->course->title }} • {{ $ex->examSession->name }}</span>
                                        </div>
                                        <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded">Marks: {{ $ex->total_marks }}</span>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-slate-400 text-xs">No exams scheduled.</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Results List -->
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Recorded Student Grades</h5>
                                <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($examResults) }}</span>
                            </div>
                            <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                                @forelse($examResults as $res)
                                    <div class="p-4 flex justify-between items-center">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-800">{{ $res->user->first_name }} {{ $res->user->last_name }}</span>
                                            <span class="text-[10px] text-slate-500 mt-0.5">{{ $res->exam->title }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded">Score: {{ $res->marks_obtained }}</span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $res->status === 'PASSED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">{{ $res->grade }} (GPA: {{ $res->gpa }})</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-slate-400 text-xs">No results recorded yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: AUTOMATIONS -->
        @if($activeTab === 'automations')
            <div class="space-y-6">
                <!-- Automation builder -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h4 class="font-bold text-slate-900 text-base">Automation Workflow Rule Builder</h4>
                    <form wire:submit.prevent="createWorkflow" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Rule Name</label>
                            <input type="text" wire:model="newWorkflowName" placeholder="e.g. Welcome Student Onboard" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Trigger Event</label>
                            <select wire:model="newWorkflowTrigger" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring">
                                <option value="STUDENT_REGISTERED">Student Registered</option>
                                <option value="FEE_PAID">Tuition Fee Logged PAID</option>
                                <option value="COURSE_COMPLETED">LMS Course Completed</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                            Add Workflow Trigger
                        </button>
                    </form>
                </div>

                <!-- Workflows table -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-base">Active Workflow Automations</h3>
                    </div>
                    @if(count($workflows) === 0)
                        <div class="text-center py-10 text-slate-400 text-sm">No workflows configured.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                                        <th class="px-6 py-3">Workflow</th>
                                        <th class="px-6 py-3">Trigger Event</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($workflows as $wf)
                                        <tr>
                                            <td class="px-6 py-4 font-semibold text-slate-950">{{ $wf->name }}</td>
                                            <td class="px-6 py-4 font-mono text-xs text-blue-600">{{ $wf->trigger_event }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                                    Active
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button wire:click="triggerWorkflow('{{ $wf->id }}')" class="bg-blue-550 hover:bg-blue-100 text-blue-600 text-xs font-bold px-3 py-1.5 rounded-lg border border-blue-200">
                                                    Test Run Trigger
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: FORMS -->
        @if($activeTab === 'forms')
            <div class="space-y-6">
                <!-- Form Creator -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h4 class="font-bold text-slate-900 text-base">Drag & Drop Form Builder</h4>
                    <form wire:submit.prevent="createForm" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Form Title</label>
                            <input type="text" wire:model="newFormTitle" placeholder="e.g. Admission Query form" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Form Description</label>
                            <input type="text" wire:model="newFormDesc" placeholder="Brief outline..." class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                            Create Form
                        </button>
                    </form>
                </div>

                <!-- Forms List -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-base">Public dynamic Forms</h3>
                    </div>
                    @if(count($forms) === 0)
                        <div class="text-center py-10 text-slate-400 text-sm">No forms created.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                                        <th class="px-6 py-3">Form Title</th>
                                        <th class="px-6 py-3">Description</th>
                                        <th class="px-6 py-3">Access</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($forms as $frm)
                                        <tr>
                                            <td class="px-6 py-4 font-semibold text-slate-950">{{ $frm->title }}</td>
                                            <td class="px-6 py-4 text-xs text-slate-500">{{ $frm->description }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                                    PUBLIC / LIVE
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: GAMIFICATION -->
        @if($activeTab === 'gamification')
            <div class="space-y-6">
                <!-- Badges lists -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-slate-900 text-base">Reward Badges Inventory</h3>
                    @if(count($badges) === 0)
                        <div class="text-slate-400 text-sm">No badges loaded yet.</div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach($badges as $bdg)
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-center space-y-2">
                                    <div class="text-2xl">🏆</div>
                                    <h4 class="font-bold text-xs text-slate-900">{{ $bdg->name }}</h4>
                                    <span class="text-[10px] text-blue-600 font-bold">{{ $bdg->xp_required }} XP Required</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Points awarder form -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
                    <h4 class="font-bold text-slate-900 text-base">Award Student XP Points</h4>
                    <form wire:submit.prevent="awardPoints" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Select User</label>
                            <select wire:model="selectedUserForPoints" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring">
                                <option value="">-- Choose student --</option>
                                @foreach($users as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->first_name }} {{ $usr->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">XP Points</label>
                            <input type="number" wire:model="pointsToAward" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Reason</label>
                            <input type="text" wire:model="pointsReason" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                            Award XP
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: HR -->
        @if($activeTab === 'hr')
            <div class="space-y-6">
                <!-- HR Forms Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Leave Request Form -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">📅 File Employee Leave Request</h4>
                        <form wire:submit.prevent="submitLeaveRequest" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Employee</label>
                                <select wire:model="selectedEmployeeForLeave" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose Employee --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->user->first_name }} {{ $emp->user->last_name }} ({{ $emp->designation }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Leave Type</label>
                                <select wire:model="leaveType" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="CASUAL">Casual Leave</option>
                                    <option value="SICK">Sick Leave</option>
                                    <option value="ANNUAL">Annual Leave</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Start Date</label>
                                    <input type="date" wire:model="leaveStartDate" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">End Date</label>
                                    <input type="date" wire:model="leaveEndDate" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Submit Leave Request
                            </button>
                        </form>
                    </div>

                    <!-- Process Payroll Form -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">💳 Process Employee Payroll</h4>
                        <form wire:submit.prevent="runEmployeePayroll" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Employee</label>
                                <select wire:model="selectedEmployeeForPayroll" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose Employee --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->user->first_name }} {{ $emp->user->last_name }} (Base: ${{ number_format($emp->salary, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Payroll Amount</label>
                                <input type="number" step="0.01" wire:model="payrollAmount" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Run & Release Payroll
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Roster lists grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Employees Registry -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Employee Directory</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($employees) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($employees as $emp)
                                <div class="p-4 flex justify-between items-center text-xs">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $emp->user->first_name }} {{ $emp->user->last_name }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">{{ $emp->designation }} • {{ $emp->department->name }}</span>
                                    </div>
                                    <span class="font-bold text-slate-850">${{ number_format($emp->salary, 2) }}</span>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No employees.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Leave Requests Board -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Leave Requests Board</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($leaveRequests) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($leaveRequests as $req)
                                <div class="p-4 flex justify-between items-center text-xs">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $req->employee->user->first_name }} {{ $req->employee->user->last_name }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">{{ $req->type }} • {{ \Carbon\Carbon::parse($req->start_date)->format('M d') }} to {{ \Carbon\Carbon::parse($req->end_date)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full font-bold text-[9px] {{ $req->status === 'APPROVED' ? 'bg-emerald-50 text-emerald-700' : ($req->status === 'REJECTED' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                            {{ $req->status }}
                                        </span>
                                        @if($req->status === 'PENDING')
                                            <button wire:click="approveLeaveRequest('{{ $req->id }}')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-bold px-2 py-1 rounded">Approve</button>
                                            <button wire:click="rejectLeaveRequest('{{ $req->id }}')" class="bg-rose-600 hover:bg-rose-700 text-white text-[9px] font-bold px-2 py-1 rounded">Reject</button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No leave files recorded.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: ASSETS -->
        @if($activeTab === 'assets')
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Register Asset Form -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">📦 Register New Academy Asset</h4>
                        <form wire:submit.prevent="createAsset" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Asset Name</label>
                                <input type="text" wire:model="newAssetName" placeholder="e.g. Smart Projector" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Serial Number</label>
                                <input type="text" wire:model="newAssetSerial" placeholder="e.g. SN-998877" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Register Asset
                            </button>
                        </form>
                    </div>

                    <!-- Assign Asset Form -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">👤 Check Out / Assign Asset</h4>
                        <form wire:submit.prevent="assignAsset" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Asset</label>
                                <select wire:model="selectedAssetForAssign" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose Available Asset --</option>
                                    @foreach($assets->where('status', 'AVAILABLE') as $ast)
                                        <option value="{{ $ast->id }}">{{ $ast->name }} (SN: {{ $ast->serial_number ?: 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Assign To User</label>
                                <select wire:model="selectedUserForAsset" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose User --</option>
                                    @foreach($users as $usr)
                                        <option value="{{ $usr->id }}">{{ $usr->first_name }} {{ $usr->last_name }} ({{ $usr->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Assign Asset
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Assets & Assignments rosters -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Assets List -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Assets Inventory</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($assets) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($assets as $ast)
                                <div class="p-4 flex justify-between items-center text-xs">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $ast->name }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">SN: {{ $ast->serial_number ?? 'N/A' }}</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[9px] {{ $ast->status === 'AVAILABLE' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                        {{ $ast->status }}
                                    </span>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No assets registered.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Assignments list -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Device Checkouts Log</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($assetAssignments) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($assetAssignments as $assign)
                                <div class="p-4 flex justify-between items-center text-xs">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $assign->asset->name }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">Assigned to: {{ $assign->user->first_name }} {{ $assign->user->last_name }}</span>
                                    </div>
                                    <span class="text-[9px] text-slate-400 font-mono">{{ \Carbon\Carbon::parse($assign->assigned_at)->format('M d, h:i A') }}</span>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No devices currently checked out.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: LIBRARY -->
        @if($activeTab === 'library')
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Book Creator -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">📚 Catalog Book in Inventory</h4>
                        <form wire:submit.prevent="createBook" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Book Title</label>
                                <input type="text" wire:model="newBookTitle" placeholder="e.g. Introduction to Algorithms" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Author</label>
                                <input type="text" wire:model="newBookAuthor" placeholder="e.g. Cormen" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">ISBN</label>
                                <input type="text" wire:model="newBookIsbn" placeholder="e.g. 978-0262033848" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Register Book
                            </button>
                        </form>
                    </div>

                    <!-- Book Loaning Form -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">🤝 Loan Book to Student</h4>
                        <form wire:submit.prevent="issueBookLoan" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Book</label>
                                <select wire:model="selectedBookForLoan" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose Book --</option>
                                    @foreach($books as $bk)
                                        @if($bk->available_copies > 0)
                                            <option value="{{ $bk->id }}">{{ $bk->title }} (Avail: {{ $bk->available_copies }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Student</label>
                                <select wire:model="selectedUserForLoan" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose Student/User --</option>
                                    @foreach($users as $usr)
                                        <option value="{{ $usr->id }}">{{ $usr->first_name }} {{ $usr->last_name }} ({{ $usr->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Loan Due Date</label>
                                <input type="date" wire:model="loanDueDate" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Issue Book Loan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Books & Loans rosters -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Books List -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Library Books</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($books) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($books as $bk)
                                <div class="p-4 flex justify-between items-center text-xs">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $bk->title }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">Author: {{ $bk->author }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-blue-50 text-blue-700 rounded border border-blue-100">Copies: {{ $bk->available_copies }}/{{ $bk->total_copies }}</span>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No books in catalog.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Book Loans List -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Active Book Lending Log</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($bookLoans) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($bookLoans as $loan)
                                <div class="p-4 flex justify-between items-center text-xs">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $loan->book->title }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">Borrowed by: {{ $loan->user->first_name }} {{ $loan->user->last_name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($loan->returned_at)
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-100">Returned</span>
                                        @else
                                            <span class="text-[9px] font-bold text-rose-600">Due: {{ \Carbon\Carbon::parse($loan->due_date)->format('M d, Y') }}</span>
                                            <button wire:click="returnBookLoan('{{ $loan->id }}')" class="bg-blue-600 hover:bg-blue-700 text-white text-[9px] font-bold px-2 py-1 rounded shadow-sm">
                                                Return Book
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No active book loans.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: COMPLIANCE -->
        @if($activeTab === 'accreditation')
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Create Accreditation Audits -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">🛡️ New Compliance Auditing Framework</h4>
                        <form wire:submit.prevent="createAccreditation" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Framework Name</label>
                                <input type="text" wire:model="newAccreditationFramework" placeholder="e.g. Higher Education Certification" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Auditing Agency</label>
                                <input type="text" wire:model="newAccreditationAgency" placeholder="e.g. HEC Committee" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Setup Framework
                            </button>
                        </form>
                    </div>

                    <!-- Create Compliance Items Checklist -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">✅ Add Compliance Criteria Item</h4>
                        <form wire:submit.prevent="createComplianceItem" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select Audit Framework</label>
                                <select wire:model="selectedAccreditationForCompliance" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose Framework --</option>
                                    @foreach($accreditations as $ac)
                                        <option value="{{ $ac->id }}">{{ $ac->framework_name }} ({{ $ac->agency }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Criteria Name</label>
                                <input type="text" wire:model="newComplianceItemName" placeholder="e.g. Fire Safety Clearance" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Description</label>
                                <input type="text" wire:model="newComplianceItemDesc" placeholder="e.g. Evacuation plans submitted" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Add Compliance Criteria
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Accreditation audits and compliance roster list -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Audit frameworks list -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Accreditation Audits</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($accreditations) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($accreditations as $ac)
                                <div class="p-4 flex justify-between items-center text-xs">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $ac->framework_name }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">Agency: {{ $ac->agency }}</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[9px] bg-amber-50 text-amber-700 border border-amber-100">
                                        {{ $ac->status }}
                                    </span>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No audits registered.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Compliance Items Checklist -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h5 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Compliance Audit Checklist</h5>
                            <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ count($complianceItems) }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            @forelse($complianceItems as $item)
                                <div class="p-4 flex justify-between items-center text-xs">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $item->name }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">{{ $item->description }} ({{ $item->acreditation->framework_name ?? ($item->accreditation->framework_name ?? 'Audit') }})</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="checkCompliance('{{ $item->id }}')" 
                                                class="px-3 py-1.5 rounded-lg text-[10px] font-bold border transition {{ $item->is_met ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-400 border-slate-200 hover:text-slate-700' }}">
                                            {{ $item->is_met ? '✓ Criteria Met' : '✗ Unmet' }}
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No compliance items defined.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: GAMIFICATION -->
        @if($activeTab === 'gamification')
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Create Badges -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">🏆 Create Reward Badge</h4>
                        <form wire:submit.prevent="createBadge" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Badge Name</label>
                                <input type="text" wire:model="newBadgeName" placeholder="e.g. Master Coder" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Description</label>
                                <input type="text" wire:model="newBadgeDesc" placeholder="e.g. Complete advanced courses" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">XP Required</label>
                                <input type="number" wire:model="newBadgeXp" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Create Reward Badge
                            </button>
                        </form>
                    </div>

                    <!-- Points awarder form -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">🎁 Award Student XP Points</h4>
                        <form wire:submit.prevent="awardPoints" class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Select User</label>
                                <select wire:model="selectedUserForPoints" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                                    <option value="">-- Choose student --</option>
                                    @foreach($users as $usr)
                                        <option value="{{ $usr->id }}">{{ $usr->first_name }} {{ $usr->last_name }} ({{ $usr->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">XP Points</label>
                                <input type="number" wire:model="pointsToAward" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Reason</label>
                                <input type="text" wire:model="pointsReason" class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring">
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Award XP Points
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Badges lists -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Reward Badges Inventory</h3>
                    @if(count($badges) === 0)
                        <div class="text-slate-400 text-sm text-center py-6">No badges loaded yet.</div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach($badges as $bdg)
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-center space-y-2">
                                    <div class="text-2xl">🏆</div>
                                    <h4 class="font-bold text-xs text-slate-900">{{ $bdg->name }}</h4>
                                    <p class="text-[10px] text-slate-500 line-clamp-2">{{ $bdg->description }}</p>
                                    <span class="inline-block text-[9px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full font-bold border border-blue-100">{{ $bdg->xp_required }} XP</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: BILLING -->
        @if($activeTab === 'billing')
            <div class="space-y-6">
                <!-- Subscriptions limitations -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-slate-900 text-base">Active Subscription Plan Limits</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($usages as $usage)
                            @php
                                $percent = $usage->max_limit > 0 ? min(100, round(($usage->current_value / $usage->max_limit) * 100)) : 0;
                            @endphp
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2">
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">{{ $usage->metric }}</span>
                                <h4 class="text-xl font-bold text-slate-900">{{ number_format($usage->current_value) }} / {{ number_format($usage->max_limit) }}</h4>
                                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                    <div class="bg-blue-600 h-full" style="width: {{ $percent }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tenant Invoices roster -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-base">Subscription Invoices</h3>
                    </div>
                    @if(count($invoices) === 0)
                        <div class="text-center py-10 text-slate-400 text-sm">No invoices recorded.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                                        <th class="px-6 py-3">Invoice Number</th>
                                        <th class="px-6 py-3">Amount</th>
                                        <th class="px-6 py-3">Due Date</th>
                                        <th class="px-6 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($invoices as $inv)
                                        <tr>
                                            <td class="px-6 py-4 font-semibold text-slate-950">{{ $inv->invoice_number }}</td>
                                            <td class="px-6 py-4 font-bold text-slate-800">${{ number_format($inv->amount, 2) }}</td>
                                            <td class="px-6 py-4 text-xs text-slate-500">{{ $inv->due_date }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $inv->status === 'PAID' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                                    {{ $inv->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TAB CONTENT: API Keys & Integration -->
        @if($activeTab === 'api')
            <div class="space-y-6">
                <!-- Key Generation Panel -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h4 class="font-bold text-slate-900 text-base">Generate Personal API Token</h4>
                    <p class="text-xs text-slate-500">Issue scoped credentials to interface external tools with the NANDSKILLS API. Standard rate limits apply.</p>

                    @if($generatedToken)
                        <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider block">Token Generated Successfully</span>
                            <p class="text-xs text-slate-600">Copy this token now. For security purposes, it will NOT be shown again.</p>
                            <div class="flex items-center gap-3 mt-2">
                                <code class="bg-slate-950 text-white font-mono px-3 py-2 rounded border border-white/10 select-all font-bold text-sm w-full block truncate">
                                    {{ $generatedToken }}
                                </code>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="generateApiKey" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Token Name / App</label>
                            <input type="text" wire:model.defer="newApiKeyName" required placeholder="e.g. Mobile Sync Service" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200/50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Rate Limit (req/min)</label>
                            <input type="number" wire:model.defer="newApiKeyRateLimit" required min="1" max="10000" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200/50">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                            Generate Token
                        </button>
                    </form>
                </div>

                <!-- API Keys List -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h4 class="font-bold text-slate-900 text-sm">Active API Access Tokens</h4>
                    </div>

                    @if($apiKeys->isEmpty())
                        <div class="p-8 text-center text-slate-400 text-sm">No active API tokens generated yet.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                                        <th class="px-6 py-3">Token Name</th>
                                        <th class="px-6 py-3">Rate Limit</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Issued On</th>
                                        <th class="px-6 py-3 text-right">Revocation</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($apiKeys as $key)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 font-bold text-slate-900">{{ $key->name }}</td>
                                            <td class="px-6 py-4 text-slate-600 font-semibold">{{ $key->rate_limit }} rpm</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    {{ $key->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-slate-500">{{ $key->created_at->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 text-right">
                                                <button wire:click="revokeApiKey('{{ $key->id }}')" class="text-xs text-rose-600 hover:text-rose-500 font-bold">
                                                    Revoke
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- API Logs panel -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h4 class="font-bold text-slate-900 text-sm">Recent Integration Activity Logs</h4>
                    </div>

                    @if($apiLogs->isEmpty())
                        <div class="p-8 text-center text-slate-400 text-sm">No activity logged in this window.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                                        <th class="px-6 py-3">Timestamp</th>
                                        <th class="px-6 py-3">Token</th>
                                        <th class="px-6 py-3">Method</th>
                                        <th class="px-6 py-3">Endpoint</th>
                                        <th class="px-6 py-3">IP Address</th>
                                        <th class="px-6 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($apiLogs as $log)
                                        <tr>
                                            <td class="px-6 py-4 text-xs text-slate-500">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                            <td class="px-6 py-4 font-semibold text-slate-950">{{ $log->apiKey->name ?? 'Deleted Token' }}</td>
                                            <td class="px-6 py-4 font-bold text-slate-800">{{ $log->method }}</td>
                                            <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $log->endpoint }}</td>
                                            <td class="px-6 py-4 text-xs text-slate-500">{{ $log->ip_address }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $log->status_code < 400 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                                    {{ $log->status_code }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
