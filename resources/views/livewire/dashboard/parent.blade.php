<div class="space-y-8 text-slate-800 font-sans">
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Parent Workspace</h2>
            <p class="text-slate-500 text-sm mt-1">Monitor your child's academic records, check daily attendance logs, and reconcile outstanding invoices.</p>
        </div>
        <div class="px-4 py-2 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 text-sm font-semibold">
            Linked Students: {{ count($students) }}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Student Performance Progress -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-800">Student Progress Summary</h3>

            <div class="space-y-6">
                @forelse($students as $child)
                <div class="space-y-3 p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-800">Child: {{ $child->user->first_name }} {{ $child->user->last_name }}</span>
                        <span class="text-xs text-slate-400">Roll: {{ $child->roll_number }}</span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex justify-between text-xs text-slate-500">
                            <span>Attendance Rate</span>
                            <span class="text-emerald-600 font-bold">96%</span>
                        </div>
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full" style="width: 96%"></div>
                        </div>
                    </div>

                    @foreach($child->enrollments as $enrollment)
                    <div class="space-y-1 mt-2">
                        <div class="flex justify-between text-xs text-slate-500">
                            <span>{{ $enrollment->course->title }} Progress</span>
                            <span class="text-blue-600 font-bold">{{ $enrollment->progress_percent }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-600 h-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @empty
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center text-slate-400 text-xs">
                    No student records linked to your parent account. Please contact administrative support.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Student Invoices list -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-800">Academic Billing & Invoices</h3>

            <div class="space-y-4">
                @forelse($invoices as $invoice)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between {{ $invoice->status === 'PAID' ? 'opacity-65' : '' }}">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">Invoice {{ $invoice->invoice_number }}</h4>
                        <p class="text-xs {{ $invoice->status === 'PAID' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $invoice->studentFee->feeStructure->name ?? 'Tuition Fee' }} • Status: {{ $invoice->status }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-extrabold {{ $invoice->status === 'PAID' ? 'text-slate-500' : 'text-rose-600' }}">${{ number_format($invoice->total, 2) }}</span>
                        @if($invoice->status !== 'PAID')
                        <a href="/billing" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-750 text-xs font-semibold transition-all text-white">Pay Now</a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center text-slate-400 text-xs">
                    No billing statements found.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
