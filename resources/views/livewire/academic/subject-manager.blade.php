<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Subject Master Registry</h2>
            <p class="text-slate-400 text-sm mt-1">Manage institutional course inventory, lectures, labs, seminar credits, and department allocations.</p>
        </div>
        <button wire:click="openCreate" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-sm transition-all shadow-md shadow-blue-600/10">
            Add New Subject
        </button>
    </div>

    @if($isCreating)
        <!-- Subject Form -->
        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md max-w-2xl mx-auto space-y-6">
            <h3 class="text-lg font-bold text-white">{{ $subject_id ? 'Modify' : 'Create' }} Subject Inventory</h3>

            <form wire:submit.prevent="saveSubject" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Subject Name</label>
                        <input type="text" wire:model.defer="name" required placeholder="e.g. Advanced Software Engineering" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        @error('name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Subject Code</label>
                        <input type="text" wire:model.defer="code" required placeholder="e.g. CS-402" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                        @error('code') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Credit Hours</label>
                        <input type="number" step="0.5" wire:model.defer="credit_hours" required class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Subject Type</label>
                        <select wire:model.defer="type" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            <option value="THEORY">THEORY</option>
                            <option value="LAB">LAB</option>
                            <option value="SEMINAR">SEMINAR</option>
                            <option value="PROJECT">PROJECT</option>
                            <option value="INTERNSHIP">INTERNSHIP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Department (Optional)</label>
                        <select wire:model.defer="department_id" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.defer="is_elective" id="is_elective" class="rounded bg-slate-900 border-white/10 text-blue-600">
                    <label for="is_elective" class="text-sm text-slate-300">This course is elective / optional</label>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Description / Syllabus Overview</label>
                    <textarea wire:model.defer="description" rows="3" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:border-blue-500 text-sm"></textarea>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors">
                        Save Subject
                    </button>
                    <button type="button" wire:click="$set('isCreating', false)" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Subjects List -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                            <th class="py-3 px-4">Subject Code</th>
                            <th class="py-3 px-4">Subject Name</th>
                            <th class="py-3 px-4">Department</th>
                            <th class="py-3 px-4">Credit Hours</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($subjects as $subj)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 font-bold text-blue-450">{{ $subj->code }}</td>
                                <td class="py-4 px-4 font-semibold text-white">
                                    {{ $subj->name }}
                                    @if($subj->is_elective)
                                        <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[9px] ml-2">ELECTIVE</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-xs text-slate-400">{{ $subj->department->name ?? 'Unassigned' }}</td>
                                <td class="py-4 px-4 text-xs font-bold text-blue-400">{{ $subj->credit_hours }} Credits</td>
                                <td class="py-4 px-4 text-xs font-semibold">{{ $subj->type }}</td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    <button wire:click="editSubject('{{ $subj->id }}')" class="text-xs text-slate-400 hover:text-white">Edit</button>
                                    <button onclick="confirm('Delete this subject?') || event.stopImmediatePropagation()" wire:click="deleteSubject('{{ $subj->id }}')" class="text-xs text-rose-400 hover:text-rose-350">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">No subjects registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
