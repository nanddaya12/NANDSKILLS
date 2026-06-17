<div class="space-y-8 text-slate-800 font-sans">
    <!-- Quick Analytics Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Enrolled Students</span>
            <div class="flex items-baseline justify-between mt-4">
                <span class="text-3xl font-extrabold text-slate-900">{{ $studentCount }}</span>
                <span class="text-xs font-bold text-emerald-600">SIS Registry</span>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Assigned Trainers</span>
            <div class="flex items-baseline justify-between mt-4">
                <span class="text-3xl font-extrabold text-slate-900">{{ $trainerCount }}</span>
                <span class="text-xs font-bold text-slate-400">All Active</span>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active LMS Courses</span>
            <div class="flex items-baseline justify-between mt-4">
                <span class="text-3xl font-extrabold text-slate-900">{{ $courseCount }}</span>
                <span class="text-xs font-bold text-blue-600">Programs</span>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Unpaid Invoices</span>
            <div class="flex items-baseline justify-between mt-4">
                <span class="text-3xl font-extrabold text-rose-600">${{ number_format($unpaidInvoiceSum, 2) }}</span>
                <span class="text-xs font-bold text-rose-600">Pending</span>
            </div>
        </div>
    </div>

    <!-- Operations Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Courses List -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800">Active LMS Programs</h2>
                <a href="/courses" class="text-xs font-semibold text-blue-600 hover:underline">Manage Courses →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($activePrograms as $item)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 flex gap-4 items-center">
                    <div class="h-12 w-12 shrink-0 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-extrabold uppercase text-sm">
                        {{ substr($item->title, 0, 2) }}
                    </div>
                    <div class="space-y-0.5">
                        <h4 class="text-sm font-bold text-slate-800">{{ $item->title }}</h4>
                        <p class="text-xs text-slate-400">Enrolled: {{ $item->enrollments_count }}</p>
                    </div>
                </div>
                @empty
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center text-slate-400 col-span-2 text-xs">
                    No active LMS programs found.
                </div>
                @endforelse
            </div>
        </div>

        <!-- CRM Pipeline Quick View -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800">CRM Lead Channels</h2>
                <a href="/leads" class="text-xs text-blue-600 hover:underline font-semibold">CRM Board →</a>
            </div>

            <div class="space-y-4">
                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-slate-600">New Leads</span>
                        <span class="text-slate-900">{{ $newLeads }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-blue-500 h-full" style="width: {{ $totalLeads > 0 ? ($newLeads / $totalLeads) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-slate-600">Working Leads</span>
                        <span class="text-slate-900">{{ $workingLeads }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-full" style="width: {{ $totalLeads > 0 ? ($workingLeads / $totalLeads) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-slate-600">Deals Won</span>
                        <span class="text-slate-900">{{ $wonLeads }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full" style="width: {{ $totalLeads > 0 ? ($wonLeads / $totalLeads) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
