<div class="space-y-8 text-slate-800 font-sans">
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Trainer Workspace</h2>
            <p class="text-slate-500 text-sm mt-1">Manage your assigned chapters, review assignment uploads, and submit student grading reports.</p>
        </div>
        <div class="px-4 py-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 text-sm font-semibold">
            Active Term: Q2 2026
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Assigned Courses -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-800">My Classes & Courses</h3>

            <div class="space-y-4">
                @forelse($courses as $course)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">{{ $course->title }}</h4>
                        <p class="text-xs text-slate-500">Chapters: {{ $course->chapters()->count() }} • Student Enrollment: {{ $course->enrollments_count }}</p>
                    </div>
                    <a href="/classroom" class="text-xs text-blue-600 hover:underline font-semibold">View Course</a>
                </div>
                @empty
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center text-slate-400 text-xs">
                    No classes assigned yet.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Grade Review Alert -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-800">Pending Assignment Submissions</h3>

            <div class="space-y-4">
                @forelse($pendingSubmissions as $sub)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">{{ $sub->assignment->title ?? 'Assignment Submission' }}</h4>
                        <p class="text-xs text-slate-500">Student: {{ $sub->user->first_name }} {{ $sub->user->last_name }} • Uploaded: {{ $sub->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="/classroom" class="text-xs px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 font-semibold transition-all text-white">Review & Grade</a>
                </div>
                @empty
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-center text-emerald-700 text-xs font-semibold">
                    No pending submissions to review. Excellent job!
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
