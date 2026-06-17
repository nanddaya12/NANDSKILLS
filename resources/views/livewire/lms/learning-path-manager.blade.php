<div class="space-y-8">
    <!-- Top Header -->
    <div class="flex justify-between items-center bg-slate-900/60 p-6 rounded-2xl border border-white/5 backdrop-blur-md">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Curriculum Learning Paths</h2>
            <p class="text-slate-400 text-sm mt-1">Configure prerequisite dependencies, complete skill trees, and unlock academic tracks.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 text-rose-400 border border-rose-500/20 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Prerequisites Builder Panel (Admins Only) -->
        @if($isAdmin)
            <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-6 self-start">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Configure Dependency Lock</h3>
                
                <form wire:submit.prevent="addPrerequisite" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Target Course</label>
                        <select wire:model="selectedCourseId" required
                                class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                            <option value="">Select Target Course...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('selectedCourseId') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Required Prerequisite Course</label>
                        <select wire:model="prerequisiteCourseId" required
                                class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                            <option value="">Select Prerequisite Course...</option>
                            @foreach($courses as $course)
                                @if($course->id !== $selectedCourseId)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('prerequisiteCourseId') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-all shadow-md shadow-blue-600/10">
                        Link Prerequisite
                    </button>
                </form>
                <p class="text-[10px] text-slate-500 leading-normal">Creating a dependency will lock access to the target course for all students until they complete the prerequisite course.</p>
            </div>
        @else
            <!-- Student Progress Status Card -->
            <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-6 self-start">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">My Progress Overview</h3>
                <div class="space-y-4">
                    <div class="bg-slate-950 p-4 rounded-xl border border-white/5 text-sm space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Completed Courses:</span>
                            <span class="text-emerald-400 font-extrabold">{{ count($completedCourseIds) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Total Available Tracks:</span>
                            <span class="text-white font-extrabold">{{ count($courses) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Skill Trees / Learning Pathway Map -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6">Course Directory Skill Tree</h3>
                
                <div class="space-y-4">
                    @forelse($courses as $course)
                        @php
                            $isLocked = $this->isCourseLocked($course);
                            $isCompleted = in_array($course->id, $completedCourseIds);
                        @endphp
                        
                        <div class="p-5 rounded-2xl border transition-all duration-300 {{ $isCompleted ? 'border-emerald-500/20 bg-emerald-500/[0.01]' : ($isLocked ? 'border-white/5 bg-slate-950/20 opacity-60' : 'border-blue-500/20 bg-blue-500/[0.01]') }}">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex gap-4 items-start">
                                    <!-- Lock / Status Icon -->
                                    <div class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center font-bold text-lg border {{ $isCompleted ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : ($isLocked ? 'bg-slate-800 text-slate-500 border-white/5' : 'bg-blue-500/10 text-blue-400 border-blue-500/20') }}">
                                        @if($isCompleted)
                                            ✓
                                        @elseif($isLocked)
                                            🔒
                                        @else
                                            📖
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-base font-extrabold text-white">{{ $course->title }}</h4>
                                        <p class="text-slate-400 text-xs mt-1 leading-normal">{{ $course->short_description ?? 'No description provided.' }}</p>
                                        
                                        <!-- Prerequisites List -->
                                        @if($course->prerequisites->isNotEmpty())
                                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Prereqs:</span>
                                                @foreach($course->prerequisites as $prereq)
                                                    <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded border {{ in_array($prereq->id, $completedCourseIds) ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-800 text-slate-400 border-white/5' }}">
                                                        {{ $prereq->title }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    @if($isCompleted)
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase border border-emerald-500/20">Completed</span>
                                    @elseif($isLocked)
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-400 text-[10px] font-black uppercase border border-white/5">Locked</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase border border-blue-500/20">Unlocked</span>
                                    @endif

                                    <!-- Admin actions -->
                                    @if($isAdmin && $course->prerequisites->isNotEmpty())
                                        <div class="mt-4 flex flex-col gap-1.5 align-right text-right">
                                            @foreach($course->prerequisites as $prereq)
                                                <button wire:click="removePrerequisite('{{ $course->id }}', '{{ $prereq->id }}')" class="text-[9px] font-bold text-rose-400 hover:underline">
                                                    Remove prereq: {{ substr($prereq->title, 0, 12) }}...
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-500 text-sm">No courses matching current academy profile.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
