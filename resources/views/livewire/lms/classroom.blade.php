<div class="h-full flex flex-col gap-6">
    <!-- Top Action Bar -->
    <div class="flex justify-between items-center bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
        <div class="flex items-center gap-4">
            <button wire:click="setView('classroom')" 
                class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ $view === 'classroom' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/10' : 'text-slate-600 hover:text-slate-900 bg-slate-50 border border-slate-200' }}">
                Active Classroom
            </button>
            <button wire:click="setView('catalog')" 
                class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ $view === 'catalog' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/10' : 'text-slate-600 hover:text-slate-900 bg-slate-50 border border-slate-200' }}">
                Browse Course Catalog
            </button>
        </div>
        @if(session()->has('status'))
            <span class="text-xs text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg animate-pulse font-medium">
                {{ session('status') }}
            </span>
        @endif
    </div>

    @if($view === 'catalog')
        <!-- Catalog View -->
        <div class="flex-1 bg-white border border-slate-200 rounded-2xl p-8 overflow-y-auto shadow-sm">
            <div class="max-w-4xl mx-auto space-y-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Course Catalog</h2>
                    <p class="text-sm text-slate-500 mt-1">Explore and self-enroll in available learning tracks to unlock lessons and live virtual classes.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    @forelse($availableCourses as $course)
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 flex flex-col justify-between hover:border-blue-500/30 transition-all group">
                            <div class="space-y-4">
                                <div class="h-12 w-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-600 transition-colors">{{ $course->title }}</h3>
                                    <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">{{ $course->description }}</p>
                                </div>
                            </div>
                            <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center justify-between">
                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg">
                                    FREE
                                </span>
                                <button wire:click="enrollInCourse('{{ $course->id }}')" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-xs font-bold text-white transition-all shadow-md shadow-blue-500/10">
                                    Enroll & Start Learning
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 flex flex-col items-center justify-center text-slate-400 gap-3 bg-slate-50 rounded-2xl border border-slate-200">
                            <svg class="h-12 w-12 text-slate-350" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <span class="text-sm">No new courses are currently available in the catalog.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <!-- Active Classroom View -->
        <div class="flex-1 flex gap-8 min-h-0">
            <!-- Left Panel: Chapters Navigation -->
            <div class="w-80 shrink-0 bg-white border border-slate-200 rounded-2xl flex flex-col overflow-hidden shadow-sm">
                <div class="p-6 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-base">Course Chapters</h3>
                    <p class="text-xs text-slate-500 mt-1 truncate">{{ $activeCourse->title ?? 'Select a Course' }}</p>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    @if($activeCourse)
                        @foreach($activeCourse->chapters as $chapter)
                            <div class="space-y-2">
                                <div class="text-xs font-bold text-blue-600 uppercase tracking-wider px-2">{{ $chapter->title }}</div>
                                <div class="space-y-1">
                                    @foreach($chapter->lessons as $lesson)
                                        <button wire:click="selectLesson('{{ $lesson->id }}')"
                                                class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-medium transition-all flex items-center justify-between gap-3 {{ $activeLesson && $activeLesson->id === $lesson->id ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                            <span class="truncate">{{ $lesson->title }}</span>
                                            <span class="text-[9px] uppercase font-bold opacity-75 shrink-0 px-2 py-0.5 rounded {{ $activeLesson && $activeLesson->id === $lesson->id ? 'bg-blue-700/50 text-white border border-blue-500' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">{{ $lesson->type }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="py-8 text-center text-slate-400 text-xs">
                            No course selected. Use the catalog tab to enroll.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Panel: Lesson Player & Notes -->
            <div class="flex-1 bg-white border border-slate-200 rounded-2xl flex flex-col overflow-hidden shadow-sm">
                @if($activeLesson)
                    <div class="p-6 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">{{ $activeLesson->title }}</h2>
                            <p class="text-xs text-slate-500 mt-1">Duration: {{ $activeLesson->duration_minutes }} minutes</p>
                        </div>
                        <button wire:click="completeLesson" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-xs font-bold text-white transition-all shadow-md shadow-blue-500/10">
                            Complete & Next →
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-8 space-y-8">
                        <!-- Video Player Container (Mock iframe) -->
                        @if($activeLesson->type === 'VIDEO')
                            <div class="aspect-video w-full rounded-2xl bg-black border border-slate-200 flex items-center justify-center relative group overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                                    <span class="text-xs font-semibold text-white">Video Lesson Stream (S3 Storage Secure Link)</span>
                                </div>
                                <svg class="h-16 w-16 text-blue-500 drop-shadow-lg cursor-pointer hover:scale-105 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        @endif

                        <!-- Tabs -->
                        <div class="border-b border-slate-200 flex gap-6 text-sm font-semibold">
                            <button wire:click="$set('activeTab', 'lesson')" class="pb-3 border-b-2 transition-all {{ $activeTab === 'lesson' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                                Lesson Details
                            </button>
                            <button wire:click="$set('activeTab', 'notes')" class="pb-3 border-b-2 transition-all {{ $activeTab === 'notes' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                                My Study Notes ({{ $activeLesson->notes->count() ?? 0 }})
                            </button>
                        </div>

                        <!-- Tab Content -->
                        @if($activeTab === 'lesson')
                            <div class="space-y-4 leading-relaxed text-sm text-slate-600">
                                <p>{{ $activeLesson->description }}</p>
                                <div class="prose prose-slate max-w-none text-xs leading-relaxed">
                                    {!! $activeLesson->content !!}
                                </div>
                            </div>
                        @elseif($activeTab === 'notes')
                            <div class="space-y-6">
                                <!-- Add Note Form -->
                                <div class="space-y-3">
                                    <textarea wire:model.defer="newNoteContent" placeholder="Take down important formulas or concepts..." rows="3"
                                              class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-xs"></textarea>
                                    <button wire:click="saveNote" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white transition-all">
                                        Save Study Note
                                    </button>
                                </div>

                                <!-- Notes List -->
                                <div class="space-y-4">
                                    @forelse($activeLesson->notes as $note)
                                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                                            <p class="text-xs text-slate-700 leading-relaxed">{{ $note->content }}</p>
                                            <span class="text-[9px] text-slate-400 font-bold block uppercase">{{ $note->created_at->diffForHumans() }}</span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400">No notes written for this lesson yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-400 gap-3">
                        <svg class="h-12 w-12 text-slate-350" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="text-sm">Please select a lesson to begin learning.</span>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
