<div class="space-y-8">
    <!-- Top Header -->
    <div class="flex justify-between items-center bg-slate-900/60 p-6 rounded-2xl border border-white/5 backdrop-blur-md">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Student Fees & Installments Portal</h2>
            <p class="text-slate-400 text-sm mt-1">Allocate student fee plans, distribute scholarships, divide dues into installments, and track payment schedules.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Fee Issuance Form Panel -->
        <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-6 self-start">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Allocate Custom Student Fee</h3>

            <form wire:submit.prevent="issueFee" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Select Student</label>
                    <select wire:model.defer="selectedStudent" required
                            class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        <option value="">Choose Student Profile...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user->first_name }} {{ $student->user->last_name }} ({{ $student->roll_number }})</option>
                        @endforeach
                    </select>
                    @error('selectedStudent') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Fee Category</label>
                    <select wire:model.defer="selectedFeeStructure" required
                            class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        <option value="">Choose Fee structure...</option>
                        @foreach($feeStructures as $structure)
                            <option value="{{ $structure->id }}">{{ $structure->name }} - ${{ number_format($structure->amount, 2) }}</option>
                        @endforeach
                    </select>
                    @error('selectedFeeStructure') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Discount ($)</label>
                        <input type="number" step="0.01" wire:model.defer="discount" required
                               class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('discount') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Scholarship ($)</label>
                        <input type="number" step="0.01" wire:model.defer="scholarship" required
                               class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('scholarship') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Payment Installments</label>
                    <select wire:model.defer="installments" required
                            class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        <option value="1">1 (Single Full Payment)</option>
                        <option value="2">2 (Two Installments)</option>
                        <option value="3">3 (Three Installments)</option>
                        <option value="4">4 (Four Installments)</option>
                        <option value="6">6 (Six Installments)</option>
                        <option value="12">12 (Monthly Installments)</option>
                    </select>
                    @error('installments') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-all shadow-md shadow-blue-600/10">
                    Allocate Fee Schedule
                </button>
            </form>
        </div>

        <!-- Student Fees List Panel -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Active Fee Schedules Table -->
            <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Allocated Student Fees</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-bold text-slate-400">
                                <th class="py-3 px-4">Student</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4">Net Amount</th>
                                <th class="py-3 px-4">Installments</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($studentFees as $fee)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-4 font-bold text-white">
                                        {{ $fee->student->user->first_name }} {{ $fee->student->user->last_name }}
                                    </td>
                                    <td class="py-4 px-4 text-slate-400 font-semibold">{{ $fee->feeStructure->name }}</td>
                                    <td class="py-4 px-4 text-blue-400 font-extrabold">${{ number_format($fee->net_amount, 2) }}</td>
                                    <td class="py-4 px-4 text-xs font-semibold text-slate-500">{{ $fee->invoices->count() }} bills</td>
                                    <td class="py-4 px-4">
                                        @if($fee->status === 'PAID')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">PAID</span>
                                        @elseif($fee->status === 'PARTIALLY_PAID')
                                            <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-bold border border-amber-500/20">PARTIAL</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-bold border border-rose-500/20">UNPAID</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button wire:click="selectFeeForInstallments('{{ $fee->id }}')" class="text-xs text-blue-400 hover:text-blue-300 font-bold bg-blue-500/10 hover:bg-blue-500/20 px-3 py-1.5 rounded-lg border border-blue-500/20">
                                            Split Payments
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500">No student fees allocated.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Invoices / Receipts Table -->
            <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Installment Invoices Log</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead>
                            <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-bold text-slate-400">
                                <th class="py-3 px-4">Invoice #</th>
                                <th class="py-3 px-4">Student</th>
                                <th class="py-3 px-4">Total Amount</th>
                                <th class="py-3 px-4">Due Date</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-4 px-4 font-extrabold text-white">{{ $invoice->invoice_number }}</td>
                                    <td class="py-4 px-4 font-semibold text-slate-300">
                                        {{ $invoice->studentFee->student->user->first_name }} {{ $invoice->studentFee->student->user->last_name }}
                                    </td>
                                    <td class="py-4 px-4 text-blue-400 font-black">${{ number_format($invoice->total, 2) }}</td>
                                    <td class="py-4 px-4 text-xs font-semibold text-slate-500">{{ $invoice->due_date->format('M d, Y') }}</td>
                                    <td class="py-4 px-4">
                                        @if($invoice->status === 'PAID')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">PAID</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-bold border border-rose-500/20">UNPAID</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <a href="/api/v1/receipts/{{ $invoice->id }}/download" target="_blank" class="text-xs text-amber-400 hover:text-amber-300 font-bold bg-amber-500/10 hover:bg-amber-500/20 px-3 py-1.5 rounded-lg border border-amber-500/20">
                                            Print PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500">No invoices generated.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Installment Modal -->
    @if($showInstallmentModal && $selectedStudentFeeForInstallments)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl p-6 shadow-2xl space-y-6">
                <div>
                    <h3 class="text-lg font-black text-white">Setup Installment Breakdowns</h3>
                    <p class="text-xs text-slate-400 mt-1">Split outstanding fee amount into equal parts over consecutive months.</p>
                </div>

                <div class="space-y-4">
                    <div class="bg-slate-950 p-4 rounded-xl border border-white/5 text-sm space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Total Net Fee:</span>
                            <span class="text-white font-bold">${{ number_format($selectedStudentFeeForInstallments->net_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Student Name:</span>
                            <span class="text-white font-bold">{{ $selectedStudentFeeForInstallments->student->user->first_name }} {{ $selectedStudentFeeForInstallments->student->user->last_name }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Number of Splits</label>
                        <select wire:model.defer="numberOfInstallments"
                                class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                            <option value="2">2 monthly bills</option>
                            <option value="3">3 monthly bills</option>
                            <option value="4">4 monthly bills</option>
                            <option value="6">6 monthly bills</option>
                            <option value="12">12 monthly bills</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button wire:click="applyInstallmentPlan" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-colors">
                        Generate Plan
                    </button>
                    <button wire:click="$set('showInstallmentModal', false)" class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
