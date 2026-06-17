<div class="space-y-8">
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">ERP Finance Control</h2>
            <p class="text-slate-400 text-sm mt-1">Issue student fee bills, allocate scholarships, record cash payments, and track outstanding collections.</p>
        </div>
        <button wire:click="$toggle('isIssuingBill')" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-sm transition-all shadow-md shadow-blue-600/10">
            {{ $isIssuingBill ? 'View Invoice List' : 'Issue New Invoice' }}
        </button>
    </div>

    @if($isIssuingBill)
        <!-- Issue Bill Form -->
        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md max-w-2xl mx-auto space-y-6">
            <h3 class="text-lg font-bold text-white">Issue Student Invoice</h3>

            <form wire:submit.prevent="issueBill" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Select Student</label>
                    <select wire:model.defer="selectedStudent" required
                            class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        <option value="">Choose Student Profile...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user->first_name }} {{ $student->user->last_name }} ({{ $student->roll_number }})</option>
                        @endforeach
                    </select>
                    @error('selectedStudent') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Select Fee Category</label>
                    <select wire:model.defer="selectedFeeStructure" required
                            class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        <option value="">Choose Fee...</option>
                        @foreach($feeStructures as $structure)
                            <option value="{{ $structure->id }}">{{ $structure->name }} - ${{ number_format($structure->amount, 2) }}</option>
                        @endforeach
                    </select>
                    @error('selectedFeeStructure') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Direct Discount ($)</label>
                        <input type="number" step="0.01" wire:model.defer="discount" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('discount') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Scholarship Allowance ($)</label>
                        <input type="number" step="0.01" wire:model.defer="scholarship" required
                               class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        @error('scholarship') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors">
                        Issue Student Bill
                    </button>
                    <button type="button" wire:click="$set('isIssuingBill', false)" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Invoices List -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead>
                        <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-semibold text-slate-400">
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4">Student</th>
                            <th class="py-3 px-4">Net Amount</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Due Date</th>
                            <th class="py-3 px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 font-bold text-white">{{ $invoice->invoice_number }}</td>
                                <td class="py-4 px-4 font-semibold text-slate-200">
                                    {{ $invoice->studentFee->student->user->first_name }} {{ $invoice->studentFee->student->user->last_name }}
                                </td>
                                <td class="py-4 px-4 text-blue-400 font-extrabold">${{ number_format($invoice->total, 2) }}</td>
                                <td class="py-4 px-4">
                                    @if($invoice->status === 'PAID')
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">PAID</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-bold border border-rose-500/20">UNPAID</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-xs">{{ $invoice->due_date->format('M d, Y') }}</td>
                                <td class="py-4 px-4">
                                    @if($invoice->status !== 'PAID')
                                        <button wire:click="simulatePayment('{{ $invoice->id }}')" class="text-xs text-blue-400 hover:underline">
                                            Simulate Pay
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-500">No Action</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">No invoices issued.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
