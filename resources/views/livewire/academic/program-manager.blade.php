<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Academic Programs & Curriculums</h2>
            <p class="text-slate-400 text-sm mt-1">Manage degree plans, course pathways, core courses, electives, credit hour requirements, and department frameworks.</p>
        </div>
        <button wire:click="openCreate" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-sm transition-all shadow-md shadow-blue-600/10">
            Create New Program
        </button>
    </div>

    @if($isCreating)
        <!-- Program Form -->
        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md max-w-2xl mx-auto space-y-6">
            <h3 class="text-lg font-bold text-white">{{ $program_id ? 'Modify' : 'Create' }} Program File</h3>

            <form wire:submit.prevent="saveProgram" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Program Name</label>
                        <input type="text" wire:model.defer="name" required placeholder="e.g. Bachelor of Computer Science" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        @error('name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Program Code</label>
                        <input type="text" wire:model.defer="code" required placeholder="e.g. BSCS" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        @error('code') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Faculty Department</label>
                        <select wire:model.defer="faculty_id" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            <option value="">Select Faculty</option>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Degree Type</label>
                        <select wire:model.defer="degree_type" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            <option value="CERTIFICATE">CERTIFICATE</option>
                            <option value="DIPLOMA">DIPLOMA</option>
                            <option value="BACHELOR">BACHELOR</option>
                            <option value="MASTER">MASTER</option>
                            <option value="PHD">PHD</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Years</label>
                        <input type="number" wire:model.defer="duration_years" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Semesters</label>
                        <input type="number" wire:model.defer="total_semesters" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Credits</label>
                        <input type="number" step="0.5" wire:model.defer="credit_hours_required" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Min CGPA</label>
                        <input type="number" step="0.01" wire:model.defer="min_cgpa_required" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Description</label>
                    <textarea wire:model.defer="description" rows="3" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm"></textarea>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors">
                        Save Program
                    </button>
                    <button type="button" wire:click="$set('isCreating', false)" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @elseif($isCurriculumOpen)
        <!-- Curriculum Mapping view -->
        <div class="space-y-6">
            <div class="flex justify-between items-center bg-slate-900/50 p-6 rounded-2xl border border-white/10">
                <div>
                    <h3 class="text-lg font-bold text-white">Curriculum for: {{ $selectedProgram->name }} ({{ $selectedProgram->code }})</h3>
                    <p class="text-slate-400 text-xs mt-1">Requires {{ $selectedProgram->credit_hours_required }} credit hours & CGPA of {{ $selectedProgram->min_cgpa_required }} to graduate.</p>
                </div>
                <button wire:click="$set('isCurriculumOpen', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-750 text-slate-300 text-xs font-semibold rounded-xl transition-all">
                    ← Back to List
                </button>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <!-- Add to Curriculum Form -->
                <div class="p-6 rounded-2xl bg-white/5 border border-white/10 h-fit space-y-4">
                    <h4 class="text-sm font-bold text-white border-b border-white/10 pb-2">Map Subject to Curriculum</h4>
                    <form wire:submit.prevent="addSubjectToCurriculum" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-2">Subject</label>
                            <select wire:model.defer="new_subject_id" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subj)
                                    <option value="{{ $subj->id }}">{{ $subj->name }} ({{ $subj->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-2">Semester No</label>
                                <input type="number" wire:model.defer="new_semester_no" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                            </div>
                            <div class="flex flex-col justify-end">
                                <label class="flex items-center text-xs text-slate-300 h-9">
                                    <input type="checkbox" wire:model.defer="new_is_elective" class="rounded bg-slate-900 border-white/10 text-blue-600 mr-2">
                                    Elective Course
                                </label>
                            </div>
                        </div>
                        <div class="border-t border-white/5 pt-3">
                            <label class="flex items-center text-xs text-slate-300 mb-2">
                                <input type="checkbox" wire:model="new_is_prerequisite_required" class="rounded bg-slate-900 border-white/10 text-blue-600 mr-2">
                                Requires Prerequisite?
                            </label>
                            @if($new_is_prerequisite_required)
                                <select wire:model.defer="new_prerequisite_subject_id" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                                    <option value="">Select Prerequisite Subject</option>
                                    @foreach($subjects as $subj)
                                        <option value="{{ $subj->id }}">{{ $subj->name }} ({{ $subj->code }})</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs transition-colors shadow-sm">
                            Add Course
                        </button>
                    </form>
                </div>

                <!-- Curriculum List -->
                <div class="col-span-2 p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
                    <h4 class="text-sm font-bold text-white mb-4 border-b border-white/10 pb-2">Academic Roadmap (Semesters)</h4>
                    @for($i = 1; $i <= $selectedProgram->total_semesters; $i++)
                        <div class="mb-6 last:mb-0">
                            <h5 class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">Semester {{ $i }}</h5>
                            <div class="space-y-2">
                                @php
                                    $semSubjects = $mappedSubjects->where('semester_no', $i);
                                @endphp
                                @forelse($semSubjects as $pivot)
                                    <div class="flex justify-between items-center bg-slate-950/30 p-3 rounded-xl border border-white/5 hover:border-white/10 transition-colors">
                                        <div>
                                            <span class="text-xs font-bold text-white">{{ $pivot->subject->name }}</span>
                                            <span class="text-[10px] text-slate-450 ml-2 bg-white/5 px-2 py-0.5 rounded border border-white/5">{{ $pivot->subject->code }}</span>
                                            <span class="text-[10px] text-blue-300 ml-2 font-semibold">{{ $pivot->subject->credit_hours }} Credits</span>
                                            @if($pivot->is_elective)
                                                <span class="text-[9px] text-amber-400 font-bold bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-full ml-2">ELECTIVE</span>
                                            @endif
                                            @if($pivot->is_prerequisite_required && $pivot->prerequisiteSubject)
                                                <div class="text-[9px] text-rose-400 mt-1">Requires Prereq: {{ $pivot->prerequisiteSubject->name }} ({{ $pivot->prerequisiteSubject->code }})</div>
                                            @endif
                                        </div>
                                        <button wire:click="removeSubjectFromCurriculum('{{ $pivot->id }}')" class="text-rose-450 hover:text-rose-350 text-xs">Remove</button>
                                    </div>
                                @empty
                                    <div class="text-[11px] text-slate-500 italic py-2 pl-2">No subjects mapped for this semester.</div>
                                @endforelse
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @else
        <!-- Program List -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                            <th class="py-3 px-4">Program Code</th>
                            <th class="py-3 px-4">Program Name</th>
                            <th class="py-3 px-4">Faculty</th>
                            <th class="py-3 px-4">Duration/Semesters</th>
                            <th class="py-3 px-4">Degree Type</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($programs as $prog)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 font-bold text-blue-450">{{ $prog->code }}</td>
                                <td class="py-4 px-4 font-semibold text-white">{{ $prog->name }}</td>
                                <td class="py-4 px-4 text-xs text-slate-400">{{ $prog->faculty->name ?? 'Unassigned' }}</td>
                                <td class="py-4 px-4 text-xs">{{ $prog->duration_years }} Years / {{ $prog->total_semesters }} Semesters</td>
                                <td class="py-4 px-4 text-xs font-bold text-slate-300">{{ $prog->degree_type }}</td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    <button wire:click="manageCurriculum('{{ $prog->id }}')" class="text-xs bg-slate-800 hover:bg-slate-700 text-blue-400 px-3 py-1.5 rounded-lg">Curriculum Builder</button>
                                    <button wire:click="editProgram('{{ $prog->id }}')" class="text-xs text-slate-400 hover:text-white">Edit</button>
                                    <button onclick="confirm('Delete this program?') || event.stopImmediatePropagation()" wire:click="deleteProgram('{{ $prog->id }}')" class="text-xs text-rose-400 hover:text-rose-350">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">No programs registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
