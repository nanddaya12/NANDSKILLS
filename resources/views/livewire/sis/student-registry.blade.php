<div class="space-y-8">
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Student Information System (SIS)</h2>
            <p class="text-slate-400 text-sm mt-1">Manage institutional student files, assign rolls, link parent accounts, and review academic profiles.</p>
        </div>
        <button wire:click="$toggle('isCreating')" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-sm transition-all shadow-md shadow-blue-600/10">
            {{ $isCreating ? 'View Students' : 'Register New Student' }}
        </button>
    </div>

    @if($isCreating)
        <!-- Register Student Form -->
        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md max-w-2xl mx-auto space-y-6">
            <h3 class="text-lg font-bold text-white">Register Student Profile</h3>

            <form wire:submit.prevent="createStudent" class="space-y-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">First Name</label>
                        <input type="text" wire:model.defer="first_name" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('first_name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Last Name</label>
                        <input type="text" wire:model.defer="last_name" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('last_name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Email Address</label>
                    <input type="email" wire:model.defer="email" required
                           class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                    @error('email') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Roll Number</label>
                        <input type="text" wire:model.defer="roll_number" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"
                               placeholder="NS-2026-001">
                        @error('roll_number') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Admission Date</label>
                        <input type="date" wire:model.defer="admission_date" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('admission_date') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors">
                        Create Student File
                    </button>
                    <button type="button" wire:click="$set('isCreating', false)" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Student Directory list -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                            <th class="py-3 px-4">Roll Number</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Admission Date</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($students as $profile)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 font-bold text-blue-400">{{ $profile->roll_number }}</td>
                                <td class="py-4 px-4 font-semibold text-white">{{ $profile->user->first_name }} {{ $profile->user->last_name }}</td>
                                <td class="py-4 px-4 text-xs text-slate-450">{{ $profile->user->email }}</td>
                                <td class="py-4 px-4 text-xs">{{ $profile->admission_date->format('M d, Y') }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">
                                        {{ $profile->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-500">No students registered in the directory.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
