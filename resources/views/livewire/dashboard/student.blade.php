<div class="space-y-8 text-slate-800 font-sans">
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Welcome back, {{ auth()->user()->first_name }}!</h2>
            <p class="text-slate-500 text-sm mt-1">Check your active lessons, complete quizzes, and verify your certification progress.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 font-bold uppercase">Overall Progress:</span>
            <div class="h-10 w-10 rounded-full border border-blue-200 bg-blue-50 flex items-center justify-center font-bold text-sm text-blue-600">
                {{ count($enrollments) > 0 ? round($enrollments->avg('progress_percent')) : 0 }}%
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Courses in Progress -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-800">My Active Courses</h3>

            <div class="space-y-4">
                @forelse($enrollments as $enrollment)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">{{ $enrollment->course->title }}</h4>
                            <p class="text-xs text-slate-500">Chapters: {{ $enrollment->course->chapters()->count() }} • Progress: {{ $enrollment->progress_percent }}%</p>
                        </div>
                        <a href="/classroom" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-xs font-semibold transition-all text-white">Resume Class</a>
                    </div>
                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-blue-650 h-full bg-blue-600" style="width: {{ $enrollment->progress_percent }}%"></div>
                    </div>
                </div>
                @empty
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center text-slate-400 text-xs">
                    You are not currently enrolled in any courses. Browse our program catalog to begin.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Assignments & Certificates Sidebar -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-8">
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-800">Upcoming Deadlines</h3>
                @forelse($deadlines as $deadline)
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 space-y-2">
                    <h4 class="text-xs font-bold uppercase text-amber-700">{{ $deadline->title }}</h4>
                    <p class="text-slate-600 text-xs">Due: {{ $deadline->due_date ? \Carbon\Carbon::parse($deadline->due_date)->diffForHumans() : 'No due date' }}</p>
                </div>
                @empty
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center text-slate-400 text-xs">
                    No upcoming deadlines! Keep it up.
                </div>
                @endforelse
            </div>

            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-800">My Certificates</h3>
                @forelse($certificates as $cert)
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-emerald-700 uppercase">{{ $cert->course->title ?? 'Program Certificate' }}</h4>
                        <p class="text-slate-550 text-[10px] mt-1">Issued: {{ \Carbon\Carbon::parse($cert->issue_date)->format('M Y') }}</p>
                    </div>
                    <a href="{{ $cert->pdf_url }}" target="_blank" class="text-xs text-emerald-700 hover:underline font-bold">Download PDF</a>
                </div>
                @empty
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center text-slate-400 text-xs">
                    Complete your courses to earn your official certifications.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
