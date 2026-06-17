<div class="max-w-4xl mx-auto space-y-6" x-data="examPlayer({
    timeLeft: @entangle('timeLeftSeconds'),
    warningCount: @entangle('warningCount')
})">
    @if(session()->has('exam_ended'))
        <!-- Exam Ended Summary Panel -->
        <div class="p-8 rounded-3xl bg-slate-900/60 border border-white/5 backdrop-blur-md text-center space-y-6">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-blue-600/10 text-blue-400 text-3xl mb-4 border border-blue-500/20">
                🏁
            </div>
            <h2 class="text-2xl font-black text-white">Examination Session Closed</h2>
            <p class="text-slate-400 text-sm max-w-md mx-auto">
                {{ session('exam_reason') ?: 'Your exam responses have been securely stored and recorded.' }}
            </p>
            
            <div class="inline-block p-6 rounded-2xl bg-white/5 border border-white/10 mt-4">
                <span class="text-xs uppercase tracking-widest text-slate-400 font-bold block mb-2">Your Result Score</span>
                <span class="text-4xl font-black text-blue-400">{{ session('exam_score') }}%</span>
            </div>

            <div class="pt-6">
                <a href="/classroom" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-all">
                    Return to Classroom
                </a>
            </div>
        </div>
    @else
        <!-- Exam Player Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-900/60 p-5 rounded-2xl border border-white/5 gap-4 backdrop-blur-md sticky top-0 z-40 shrink-0">
            <div>
                <h2 class="text-lg font-black text-white truncate max-w-md">{{ $quiz->title }}</h2>
                <p class="text-xs text-slate-400 mt-1">Passing score: <strong class="text-slate-300">{{ $quiz->passing_score }}%</strong> | Total: <strong class="text-slate-300">{{ count($questions) }} questions</strong></p>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Warning counter badge -->
                @if($warningCount > 0)
                    <div class="px-3 py-1.5 rounded-lg bg-rose-500/10 text-rose-400 text-xs font-bold border border-rose-500/20">
                        ⚠ Warning {{ $warningCount }}/3
                    </div>
                @endif

                <!-- Countdown Timer -->
                <div class="flex items-center gap-2 bg-slate-950 px-4 py-2 rounded-xl border border-white/5 font-mono">
                    <span class="text-xs text-slate-500 uppercase tracking-widest font-sans font-bold mr-1">Time Left</span>
                    <span class="text-lg font-black text-blue-400" x-text="formatTime(timeLeft)">00:00</span>
                </div>

                <button wire:click="submitExam('Student clicked manual submit')" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs tracking-wider transition-colors uppercase">
                    Submit Exam
                </button>
            </div>
        </div>

        <!-- Proctoring Warning Banner -->
        <div class="p-4 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-semibold leading-relaxed flex items-start gap-3">
            <span class="text-base">🛡</span>
            <span>
                <strong>PROCTORING ACTIVE:</strong> This session is browser-locked. Exiting fullscreen mode, switching browser tabs, or closing this window will flag a <strong>Cheating Violation</strong>. The exam will auto-submit on 3 violations.
            </span>
        </div>

        <!-- Questions List -->
        <div class="space-y-6">
            @foreach($questions as $index => $question)
                <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-4">
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Question {{ $index + 1 }}</span>
                        <span class="px-2 py-0.5 rounded bg-white/5 text-slate-400 text-[10px] font-bold border border-white/5">{{ $question->points }} Points</span>
                    </div>

                    <p class="text-white text-base font-bold leading-relaxed">{{ $question->text }}</p>

                    <!-- Answer Options -->
                    <div class="mt-4 space-y-2.5">
                        @if($question->type === 'MCQ' || $question->type === 'TRUE_FALSE')
                            <!-- Single choice options -->
                            @foreach($question->options ?? [] as $opt)
                                <label class="flex items-center gap-3 p-4 rounded-xl border border-white/5 hover:bg-white/5 transition-all cursor-pointer {{ ($answers[$question->id] ?? null) === $opt ? 'bg-blue-600/10 border-blue-500/40' : '' }}">
                                    <input type="radio" name="q-{{ $question->id }}" value="{{ $opt }}"
                                           wire:click="saveAnswer('{{ $question->id }}', '{{ $opt }}')"
                                           {{ ($answers[$question->id] ?? null) === $opt ? 'checked' : '' }}
                                           class="rounded-full bg-slate-950 border-white/10 text-blue-600 focus:ring-0">
                                    <span class="text-sm text-slate-300 font-medium">{{ $opt }}</span>
                                </label>
                            @endforeach

                        @elseif($question->type === 'MULTIPLE_CHOICE')
                            <!-- Multiple choice options -->
                            @foreach($question->options ?? [] as $opt)
                                @php
                                    $currentSelected = $answers[$question->id] ?? [];
                                    $isOptSelected = in_array($opt, $currentSelected);
                                @endphp
                                <label class="flex items-center gap-3 p-4 rounded-xl border border-white/5 hover:bg-white/5 transition-all cursor-pointer {{ $isOptSelected ? 'bg-blue-600/10 border-blue-500/40' : '' }}">
                                    <input type="checkbox" value="{{ $opt }}"
                                           wire:click="saveAnswer('{{ $question->id }}', 
                                                @js($isOptSelected 
                                                    ? array_values(array_diff($currentSelected, [$opt])) 
                                                    : array_merge($currentSelected, [$opt])
                                                )
                                           )"
                                           {{ $isOptSelected ? 'checked' : '' }}
                                           class="rounded bg-slate-950 border-white/10 text-blue-600 focus:ring-0">
                                    <span class="text-sm text-slate-300 font-medium">{{ $opt }}</span>
                                </label>
                            @endforeach

                        @elseif($question->type === 'FILL_IN_BLANKS')
                            <!-- Fill in blanks -->
                            <input type="text" placeholder="Type your answer here..."
                                   wire:blur="saveAnswer('{{ $question->id }}', $event.target.value)"
                                   value="{{ $answers[$question->id] ?? '' }}"
                                   class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">

                        @elseif($question->type === 'SUBJECTIVE')
                            <!-- Text/Essay answers -->
                            <textarea rows="4" placeholder="Write your explanation or essay here..."
                                      wire:blur="saveAnswer('{{ $question->id }}', $event.target.value)"
                                      class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">{{ $answers[$question->id] ?? '' }}</textarea>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Inline Javascript for Proctoring Checks -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('examPlayer', (config) => ({
                timeLeft: config.timeLeft,
                warningCount: config.warningCount,
                
                init() {
                    // Start countdown timer
                    setInterval(() => {
                        if (this.timeLeft > 0) {
                            this.timeLeft--;
                            if (this.timeLeft <= 0) {
                                this.$wire.call('submitExam', 'Time limit expired');
                            }
                        }
                    }, 1000);

                    // Anti-Cheating Event Listeners
                    this.setupProctoring();
                },

                formatTime(seconds) {
                    if (seconds >= 999999) return "No Limit";
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                },

                setupProctoring() {
                    // 1. Tab switches / Focus loss
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            this.$wire.call('logCheatingViolation', 'TAB_SWITCH', 'Student switched tabs or left page.');
                        }
                    });

                    window.addEventListener('blur', () => {
                        this.$wire.call('logCheatingViolation', 'TAB_SWITCH', 'Student window focus lost.');
                    });

                    // 2. Fullscreen exit check
                    document.addEventListener('fullscreenchange', () => {
                        if (!document.fullscreenElement) {
                            this.$wire.call('logCheatingViolation', 'FULLSCREEN_EXIT', 'Student exited fullscreen proctored screen.');
                        }
                    });

                    // Automatically request fullscreen when user interacts
                    window.addEventListener('click', () => {
                        if (!document.fullscreenElement) {
                            document.documentElement.requestFullscreen().catch((err) => {
                                console.warn('Could not force fullscreen mode:', err);
                            });
                        }
                    });
                }
            }));
        });
    </script>
</div>
