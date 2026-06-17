<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">📊 Reports & Data Exports</h2>
            <p class="text-sm text-slate-500 mt-1">Generate and download institutional reports in CSV format for analysis, printing, or external use.</p>
        </div>
        <a href="/admin/analytics" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            📈 Analytics Dashboard
        </a>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Attendance Report -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-xl bg-blue-50 text-2xl flex items-center justify-center border border-blue-100 group-hover:scale-110 transition-transform">📅</div>
                <div class="flex-1 space-y-1">
                    <h3 class="font-bold text-slate-900 text-base">Attendance Report</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Daily student attendance worksheets with status, date, and IP address logs.</p>
                </div>
            </div>
            <form action="{{ route('reports.attendance') }}" method="GET" class="mt-5 space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">From</label>
                        <input type="date" name="from" value="{{ now()->startOfMonth()->toDateString() }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">To</label>
                        <input type="date" name="to" value="{{ now()->endOfMonth()->toDateString() }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring">
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                    ⬇ Download CSV
                </button>
            </form>
        </div>

        <!-- Exam Results Report -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-xl bg-purple-50 text-2xl flex items-center justify-center border border-purple-100 group-hover:scale-110 transition-transform">📝</div>
                <div class="flex-1 space-y-1">
                    <h3 class="font-bold text-slate-900 text-base">Exam Grades Report</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">All student exam results with marks, GPA, grade letters, and pass/fail status.</p>
                </div>
            </div>
            <div class="mt-5">
                <a href="{{ route('reports.exams') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 block text-center">
                    ⬇ Download CSV
                </a>
            </div>
        </div>

        <!-- Financial Ledger -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-xl bg-emerald-50 text-2xl flex items-center justify-center border border-emerald-100 group-hover:scale-110 transition-transform">💳</div>
                <div class="flex-1 space-y-1">
                    <h3 class="font-bold text-slate-900 text-base">Financial Ledger</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">All student invoices with amounts, discounts, tax, net pay, and payment statuses.</p>
                </div>
            </div>
            <div class="mt-5">
                <a href="{{ route('reports.financial') }}" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 block text-center">
                    ⬇ Download CSV
                </a>
            </div>
        </div>

        <!-- Student Directory -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-xl bg-sky-50 text-2xl flex items-center justify-center border border-sky-100 group-hover:scale-110 transition-transform">👥</div>
                <div class="flex-1 space-y-1">
                    <h3 class="font-bold text-slate-900 text-base">Student Directory</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Complete student registry with roll numbers, contact details, and admission dates.</p>
                </div>
            </div>
            <div class="mt-5">
                <a href="{{ route('reports.students') }}" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 block text-center">
                    ⬇ Download CSV
                </a>
            </div>
        </div>

        <!-- Payroll Ledger -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-xl bg-amber-50 text-2xl flex items-center justify-center border border-amber-100 group-hover:scale-110 transition-transform">💼</div>
                <div class="flex-1 space-y-1">
                    <h3 class="font-bold text-slate-900 text-base">Payroll Ledger</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Staff salary payouts with amounts, bonuses, deductions, and payment dates.</p>
                </div>
            </div>
            <div class="mt-5">
                <a href="{{ route('reports.payroll') }}" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 block text-center">
                    ⬇ Download CSV
                </a>
            </div>
        </div>

        <!-- Low Attendance Risk Report -->
        <div class="bg-white border border-rose-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-xl bg-rose-50 text-2xl flex items-center justify-center border border-rose-100 group-hover:scale-110 transition-transform">⚠️</div>
                <div class="flex-1 space-y-1">
                    <h3 class="font-bold text-slate-900 text-base">⚠️ At-Risk Attendance</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Students with attendance percentage below threshold. Identify intervention targets.</p>
                </div>
            </div>
            <form action="{{ route('reports.low_attendance') }}" method="GET" class="mt-5 space-y-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Threshold %</label>
                    <input type="number" name="threshold" value="75" min="1" max="100" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring">
                </div>
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                    ⬇ Download Risk CSV
                </button>
            </form>
        </div>

    </div>
</div>
