<div class="space-y-8">
    <!-- Header Dashboard Section -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Advanced General Ledger & ERP</h2>
            <p class="text-slate-400 text-sm mt-1">Double-entry ledger journal, real-time Trial Balance, Income Statement, Balance Sheet, Cash Flow calculations, budget allocations, and procurement management.</p>
        </div>
        <div class="flex gap-3">
            <button wire:click="loadData" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-750 font-semibold text-xs text-slate-350 transition-all border border-white/5">
                🔄 Refresh Ledger
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-white/5 gap-2">
        <button wire:click="$set('activeTab', 'general_ledger')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'general_ledger' ? 'border-blue-500 text-white bg-white/5' : 'border-transparent text-slate-400 hover:text-white' }} rounded-t-xl">
            📊 Accounts & Ledger
        </button>
        <button wire:click="$set('activeTab', 'journal_entry')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'journal_entry' ? 'border-blue-500 text-white bg-white/5' : 'border-transparent text-slate-400 hover:text-white' }} rounded-t-xl">
            ✍️ Post Journal Entry
        </button>
        <button wire:click="$set('activeTab', 'statements')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'statements' ? 'border-blue-500 text-white bg-white/5' : 'border-transparent text-slate-400 hover:text-white' }} rounded-t-xl">
            🏛️ Financial Statements
        </button>
        <button wire:click="$set('activeTab', 'budgets')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'budgets' ? 'border-blue-500 text-white bg-white/5' : 'border-transparent text-slate-400 hover:text-white' }} rounded-t-xl">
            🪙 Budget Quotas
        </button>
        <button wire:click="$set('activeTab', 'vendors')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'vendors' ? 'border-blue-500 text-white bg-white/5' : 'border-transparent text-slate-400 hover:text-white' }} rounded-t-xl">
            🤝 Procurement & Vendors
        </button>
    </div>

    <!-- Success Feedback -->
    @if(session()->has('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Content Sections -->

    <!-- TAB 1: ACCOUNTS & GENERAL LEDGER -->
    @if($activeTab === 'general_ledger')
        <div class="grid grid-cols-3 gap-6">
            <!-- Chart of Accounts -->
            <div class="col-span-1 bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Chart of Accounts</h3>
                <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($accounts as $acc)
                        <div class="p-3 bg-slate-900/40 border border-white/5 rounded-xl hover:border-blue-500/30 transition-all">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded font-mono font-bold">{{ $acc->code }}</span>
                                    <h4 class="text-xs font-bold text-white mt-1.5">{{ $acc->name }}</h4>
                                </div>
                                <span class="text-[9px] px-2 py-0.5 rounded font-bold uppercase {{ $acc->type === 'ASSET' ? 'bg-blue-500/10 text-blue-400' : ($acc->type === 'LIABILITY' ? 'bg-amber-500/10 text-amber-400' : ($acc->type === 'REVENUE' ? 'bg-emerald-500/10 text-emerald-400' : ($acc->type === 'EXPENSE' ? 'bg-rose-500/10 text-rose-400' : 'bg-purple-500/10 text-purple-400'))) }}">
                                    {{ $acc->type }}
                                </span>
                            </div>
                            <div class="mt-3 flex justify-between items-center text-xs">
                                <span class="text-slate-500">Balance:</span>
                                <strong class="text-slate-300">${{ number_format($acc->balance, 2) }}</strong>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 text-xs italic">No accounts configured.</p>
                    @endforelse
                </div>
            </div>

            <!-- Ledger Transactions -->
            <div class="col-span-2 bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Posted Ledger Postings</h3>
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($journalEntries as $entry)
                        <div class="p-4 bg-slate-950 border border-white/5 rounded-xl space-y-3">
                            <div class="flex justify-between text-xs text-slate-400 pb-2 border-b border-white/5">
                                <div>
                                    Ref: <strong class="text-white font-mono">{{ $entry->reference_number }}</strong>
                                </div>
                                <div>
                                    Date: <strong>{{ $entry->entry_date->format('M d, Y') }}</strong>
                                </div>
                            </div>
                            <p class="text-xs text-slate-350">{{ $entry->description }}</p>
                            
                            <!-- Lines Table -->
                            <table class="w-full text-left text-[11px] border-collapse mt-2">
                                <thead>
                                    <tr class="border-b border-white/10 text-slate-500 uppercase tracking-wider font-bold">
                                        <th class="py-1">Account</th>
                                        <th class="py-1 text-right">Debit</th>
                                        <th class="py-1 text-right">Credit</th>
                                        <th class="py-1 text-right">Memo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 text-slate-300">
                                    @foreach($entry->lines as $line)
                                        <tr>
                                            <td class="py-2">
                                                <span class="font-mono text-slate-500">[{{ $line->account->code }}]</span> {{ $line->account->name }}
                                            </td>
                                            <td class="py-2 text-right font-mono text-blue-400">
                                                {{ $line->type === 'DEBIT' ? '$' . number_format($line->amount, 2) : '-' }}
                                            </td>
                                            <td class="py-2 text-right font-mono text-emerald-400">
                                                {{ $line->type === 'CREDIT' ? '$' . number_format($line->amount, 2) : '-' }}
                                            </td>
                                            <td class="py-2 text-right text-slate-400 font-italic">{{ $line->memo ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <p class="text-slate-500 text-xs italic py-12 text-center">No ledger entries recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: POST JOURNAL ENTRY -->
    @if($activeTab === 'journal_entry')
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-6 max-w-4xl mx-auto">
            <div>
                <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Create New General Journal Entry</h3>
                <p class="text-slate-400 text-xs mt-1">General journal records require balanced debits and credits. When saved, the entry will be posted directly to the ledger.</p>
            </div>

            <form wire:submit.prevent="saveJournalEntry" class="space-y-6">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Journal Entry Date</label>
                        <input type="date" wire:model="entry_date" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs">
                        @error('entry_date') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Reference Voucher #</label>
                        <input type="text" wire:model="reference_number" placeholder="e.g. JV-2026-003" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs">
                        @error('reference_number') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Voucher Description</label>
                        <input type="text" wire:model="description" placeholder="Explain the transaction details..." class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs">
                        @error('description') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Ledger Lines -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b border-white/5">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Debit/Credit Items</h4>
                        <button type="button" wire:click="addJournalLine" class="text-xs bg-slate-800 text-slate-300 hover:text-white px-3 py-1.5 rounded-lg border border-white/5 transition-colors">
                            ➕ Add Account Line
                        </button>
                    </div>

                    @error('journalLines') 
                        <div class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-xl">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="space-y-2">
                        @foreach($journalLines as $idx => $line)
                            <div class="grid grid-cols-12 gap-3 items-center bg-slate-900/40 p-3 border border-white/5 rounded-xl">
                                <div class="col-span-4">
                                    <select wire:model="journalLines.{{ $idx }}.account_id" class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-2 px-2 text-xs">
                                        <option value="">Select Account...</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ $acc->type }})</option>
                                        @endforeach
                                    </select>
                                    @error("journalLines.{$idx}.account_id") <span class="text-rose-500 text-[9px] block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-2">
                                    <select wire:model="journalLines.{{ $idx }}.type" class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-2 px-2 text-xs">
                                        <option value="DEBIT">DEBIT</option>
                                        <option value="CREDIT">CREDIT</option>
                                    </select>
                                    @error("journalLines.{$idx}.type") <span class="text-rose-500 text-[9px] block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-2">
                                    <input type="number" step="0.01" min="0.01" wire:model="journalLines.{{ $idx }}.amount" placeholder="0.00" class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-2 px-2 text-xs font-mono">
                                    @error("journalLines.{$idx}.amount") <span class="text-rose-500 text-[9px] block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-3">
                                    <input type="text" wire:model="journalLines.{{ $idx }}.memo" placeholder="Memo..." class="w-full bg-slate-950 border border-white/10 text-white rounded-lg py-2 px-2 text-xs">
                                </div>
                                <div class="col-span-1 text-center">
                                    <button type="button" wire:click="removeJournalLine({{ $idx }})" class="text-rose-400 hover:text-rose-350 text-xs">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-white/5 pt-4">
                    <button type="button" wire:click="resetJournalLines" class="px-4 py-2 bg-slate-800 hover:bg-slate-750 text-slate-400 text-xs rounded-xl font-bold transition-all">
                        Reset lines
                    </button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs rounded-xl font-bold transition-all">
                        💾 Post Voucher to Ledger
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 3: FINANCIAL STATEMENTS -->
    @if($activeTab === 'statements')
        <div class="space-y-6">
            <!-- Sub tabs -->
            <div class="flex gap-2 bg-slate-950/40 p-2 rounded-xl border border-white/5 w-fit">
                <button wire:click="$set('activeStatement', 'trial_balance')" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeStatement === 'trial_balance' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                    ⚖️ Trial Balance
                </button>
                <button wire:click="$set('activeStatement', 'income_statement')" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeStatement === 'income_statement' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                    📈 Income Statement
                </button>
                <button wire:click="$set('activeStatement', 'balance_sheet')" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeStatement === 'balance_sheet' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                    🏦 Balance Sheet
                </button>
                <button wire:click="$set('activeStatement', 'cash_flow')" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeStatement === 'cash_flow' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                    💸 Cash Flow Statement
                </button>
            </div>

            <!-- Statement View Rendering -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <!-- TRIAL BALANCE -->
                @if($activeStatement === 'trial_balance')
                    @php $tb = $this->getTrialBalanceData(); @endphp
                    <div class="space-y-6">
                        <div class="text-center space-y-1 pb-4 border-b border-white/5">
                            <h3 class="text-lg font-black text-white uppercase tracking-wider">{{ optional(app('currentTenant'))->name ?? 'NANDSKILLS UNIVERSITY' }}</h3>
                            <h4 class="text-xs uppercase font-bold text-slate-400 tracking-wider">Trial Balance Ledger Statement</h4>
                            <p class="text-[10px] text-slate-500">As of {{ now()->toFormattedDateString() }}</p>
                        </div>

                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider font-bold text-[10px]">
                                    <th class="py-3">Code</th>
                                    <th class="py-3">Account Title</th>
                                    <th class="py-3">Type</th>
                                    <th class="py-3 text-right">Debit Balance</th>
                                    <th class="py-3 text-right">Credit Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-slate-300">
                                @foreach($tb['accounts'] as $acc)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="py-2.5 font-mono text-slate-500">{{ $acc['code'] }}</td>
                                        <td class="py-2.5 font-bold text-white">{{ $acc['name'] }}</td>
                                        <td class="py-2.5 text-slate-400 text-[10px] uppercase">{{ $acc['type'] }}</td>
                                        <td class="py-2.5 text-right font-mono text-blue-400">
                                            {{ $acc['debit'] > 0 ? '$' . number_format($acc['debit'], 2) : '-' }}
                                        </td>
                                        <td class="py-2.5 text-right font-mono text-emerald-400">
                                            {{ $acc['credit'] > 0 ? '$' . number_format($acc['credit'], 2) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-white/20 font-bold bg-slate-950">
                                    <td colspan="3" class="py-3 text-right text-white uppercase tracking-wider text-[10px]">Aggregate Balance Totals</td>
                                    <td class="py-3 text-right font-mono text-blue-400 text-sm">${{ number_format($tb['debit_total'], 2) }}</td>
                                    <td class="py-3 text-right font-mono text-emerald-400 text-sm">${{ number_format($tb['credit_total'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        @if(abs($tb['debit_total'] - $tb['credit_total']) < 0.01)
                            <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-450 text-xs rounded-xl flex items-center justify-center font-bold">
                                ✅ General Ledger is in balance. Trial balance verified.
                            </div>
                        @else
                            <div class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-450 text-xs rounded-xl flex items-center justify-center font-bold">
                                ❌ Out of Balance: Trial Balance debits and credits do not match.
                            </div>
                        @endif
                    </div>
                @endif

                <!-- INCOME STATEMENT -->
                @if($activeStatement === 'income_statement')
                    @php $is = $this->getIncomeStatementData(); @endphp
                    <div class="space-y-6 max-w-3xl mx-auto">
                        <div class="text-center space-y-1 pb-4 border-b border-white/5">
                            <h3 class="text-lg font-black text-white uppercase tracking-wider">{{ optional(app('currentTenant'))->name ?? 'NANDSKILLS UNIVERSITY' }}</h3>
                            <h4 class="text-xs uppercase font-bold text-slate-400 tracking-wider">Income Statement</h4>
                            <p class="text-[10px] text-slate-500">For Period Ending {{ now()->toFormattedDateString() }}</p>
                        </div>

                        <!-- Revenue -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-white uppercase tracking-wider text-slate-400 pb-1 border-b border-white/5">Operating Revenue</h4>
                            <div class="space-y-1">
                                @foreach($is['revenue_accounts'] as $acc)
                                    <div class="flex justify-between text-xs py-1.5 px-2 text-slate-300">
                                        <span>{{ $acc['name'] }} ({{ $acc['code'] }})</span>
                                        <span class="font-mono">${{ number_format($acc['balance'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between font-bold text-xs py-2 px-2 bg-slate-900 border-t border-white/15 text-white">
                                <span>Gross Operating Revenue</span>
                                <span class="font-mono">${{ number_format($is['total_revenue'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Expense -->
                        <div class="space-y-2 pt-4">
                            <h4 class="text-xs font-bold text-white uppercase tracking-wider text-slate-400 pb-1 border-b border-white/5">Operating Expenses</h4>
                            <div class="space-y-1">
                                @foreach($is['expense_accounts'] as $acc)
                                    <div class="flex justify-between text-xs py-1.5 px-2 text-slate-300">
                                        <span>{{ $acc['name'] }} ({{ $acc['code'] }})</span>
                                        <span class="font-mono">${{ number_format($acc['balance'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between font-bold text-xs py-2 px-2 bg-slate-900 border-t border-white/15 text-white">
                                <span>Total Operating Expenses</span>
                                <span class="font-mono">${{ number_format($is['total_expense'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Net Income -->
                        <div class="flex justify-between font-bold text-sm py-3 px-3 rounded-xl bg-blue-600/25 border border-blue-500/20 text-white mt-6">
                            <span>NET ACADEMIC INCOME</span>
                            <span class="font-mono">${{ number_format($is['net_income'], 2) }}</span>
                        </div>
                    </div>
                @endif

                <!-- BALANCE SHEET -->
                @if($activeStatement === 'balance_sheet')
                    @php $bs = $this->getBalanceSheetData(); @endphp
                    <div class="space-y-6 max-w-3xl mx-auto">
                        <div class="text-center space-y-1 pb-4 border-b border-white/5">
                            <h3 class="text-lg font-black text-white uppercase tracking-wider">{{ optional(app('currentTenant'))->name ?? 'NANDSKILLS UNIVERSITY' }}</h3>
                            <h4 class="text-xs uppercase font-bold text-slate-400 tracking-wider">Balance Sheet</h4>
                            <p class="text-[10px] text-slate-500">As of {{ now()->toFormattedDateString() }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-8 items-start">
                            <!-- Assets -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider text-slate-400 pb-1 border-b border-white/5">Assets</h4>
                                <div class="space-y-1.5">
                                    @foreach($bs['assets'] as $asset)
                                        <div class="flex justify-between text-xs py-1 text-slate-350">
                                            <span>{{ $asset['name'] }}</span>
                                            <span class="font-mono text-blue-400">${{ number_format($asset['balance'], 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="flex justify-between font-bold text-xs py-2 px-2 bg-slate-900 border-t border-white/15 text-white">
                                    <span>Total Assets</span>
                                    <span class="font-mono text-blue-400">${{ number_format($bs['total_assets'], 2) }}</span>
                                </div>
                            </div>

                            <!-- Liabilities & Equity -->
                            <div class="space-y-6">
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-white uppercase tracking-wider text-slate-400 pb-1 border-b border-white/5">Liabilities</h4>
                                    <div class="space-y-1.5">
                                        @foreach($bs['liabilities'] as $liability)
                                            <div class="flex justify-between text-xs py-1 text-slate-350">
                                                <span>{{ $liability['name'] }}</span>
                                                <span class="font-mono text-amber-400">${{ number_format($liability['balance'], 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex justify-between font-bold text-xs py-2 px-2 bg-slate-900 border-t border-white/15 text-white">
                                        <span>Total Liabilities</span>
                                        <span class="font-mono text-amber-400">${{ number_format($bs['total_liabilities'], 2) }}</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-white uppercase tracking-wider text-slate-400 pb-1 border-b border-white/5">Equity</h4>
                                    <div class="space-y-1.5">
                                        @foreach($bs['equity'] as $eq)
                                            <div class="flex justify-between text-xs py-1 text-slate-350">
                                                <span>{{ $eq['name'] }}</span>
                                                <span class="font-mono text-purple-400">${{ number_format($eq['balance'], 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex justify-between font-bold text-xs py-2 px-2 bg-slate-900 border-t border-white/15 text-white">
                                        <span>Total Equity</span>
                                        <span class="font-mono text-purple-400">${{ number_format($bs['total_equity'], 2) }}</span>
                                    </div>
                                </div>

                                <div class="flex justify-between font-bold text-xs py-2.5 px-2 bg-slate-950 border border-white/10 text-white rounded-xl">
                                    <span>Total Liabilities & Equity</span>
                                    <span class="font-mono">${{ number_format($bs['total_liabilities_equity'], 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Verification stamp -->
                        @if(abs($bs['total_assets'] - $bs['total_liabilities_equity']) < 0.01)
                            <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-450 text-xs rounded-xl flex items-center justify-center font-bold">
                                ✅ Balance Equation Balanced: Assets = Liabilities + Equity (Verified)
                            </div>
                        @else
                            <div class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-450 text-xs rounded-xl flex items-center justify-center font-bold">
                                ❌ Out of Balance: Assets do not equal Liabilities + Equity. Check ledger adjustments.
                            </div>
                        @endif
                    </div>
                @endif

                <!-- CASH FLOW -->
                @if($activeStatement === 'cash_flow')
                    @php $cf = $this->getCashFlowData(); @endphp
                    <div class="space-y-6 max-w-3xl mx-auto">
                        <div class="text-center space-y-1 pb-4 border-b border-white/5">
                            <h3 class="text-lg font-black text-white uppercase tracking-wider">{{ optional(app('currentTenant'))->name ?? 'NANDSKILLS UNIVERSITY' }}</h3>
                            <h4 class="text-xs uppercase font-bold text-slate-400 tracking-wider">Cash Flow Statement</h4>
                            <p class="text-[10px] text-slate-500">For Period Ending {{ now()->toFormattedDateString() }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <!-- Inflows -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider text-emerald-400 pb-1 border-b border-white/5">Cash Inflows</h4>
                                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
                                    @forelse($cf['inflows'] as $inflow)
                                        <div class="p-2 bg-slate-900/60 rounded-lg text-xs space-y-1">
                                            <div class="flex justify-between font-bold text-slate-200">
                                                <span>{{ $inflow['ref'] }}</span>
                                                <span class="text-emerald-400 font-mono">+${{ number_format($inflow['amount'], 2) }}</span>
                                            </div>
                                            <p class="text-[10px] text-slate-400">{{ $inflow['memo'] }}</p>
                                            <p class="text-[9px] text-slate-500">Source: {{ $inflow['offset'] }}</p>
                                        </div>
                                    @empty
                                        <p class="text-slate-500 text-xs italic">No cash inflows.</p>
                                    @endforelse
                                </div>
                                <div class="flex justify-between font-bold text-xs py-2 px-2 bg-slate-900 border-t border-white/15 text-white">
                                    <span>Total Cash Receipts</span>
                                    <span class="font-mono text-emerald-400">${{ number_format($cf['total_inflow'], 2) }}</span>
                                </div>
                            </div>

                            <!-- Outflows -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider text-rose-450 pb-1 border-b border-white/5">Cash Outflows</h4>
                                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
                                    @forelse($cf['outflows'] as $outflow)
                                        <div class="p-2 bg-slate-900/60 rounded-lg text-xs space-y-1">
                                            <div class="flex justify-between font-bold text-slate-200">
                                                <span>{{ $outflow['ref'] }}</span>
                                                <span class="text-rose-400 font-mono">-${{ number_format($outflow['amount'], 2) }}</span>
                                            </div>
                                            <p class="text-[10px] text-slate-400">{{ $outflow['memo'] }}</p>
                                            <p class="text-[9px] text-slate-500">Destination: {{ $outflow['offset'] }}</p>
                                        </div>
                                    @empty
                                        <p class="text-slate-500 text-xs italic">No cash payments.</p>
                                    @endforelse
                                </div>
                                <div class="flex justify-between font-bold text-xs py-2 px-2 bg-slate-900 border-t border-white/15 text-white">
                                    <span>Total Cash Payments</span>
                                    <span class="font-mono text-rose-450">${{ number_format($cf['total_outflow'], 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Net flow -->
                        <div class="flex justify-between font-bold text-sm py-3 px-3 rounded-xl bg-slate-900 border border-white/5 text-white mt-6">
                            <span>NET CASH INCREASE / (DECREASE)</span>
                            <span class="font-mono text-blue-400">${{ number_format($cf['net_cash_flow'], 2) }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB 4: BUDGET QUOTAS -->
    @if($activeTab === 'budgets')
        <div class="grid grid-cols-3 gap-6">
            <!-- Create Budget Form -->
            <div class="col-span-1 bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Allocate Budget Quota</h3>
                
                <form wire:submit.prevent="saveBudget" class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Target Account</label>
                        <select wire:model="new_budget_account_id" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs">
                            <option value="">Select Account...</option>
                            @foreach($accounts->where('type', 'EXPENSE') as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                            @endforeach
                        </select>
                        @error('new_budget_account_id') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Budget Amount ($)</label>
                        <input type="number" step="0.01" wire:model="new_budget_amount" placeholder="0.00" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs font-mono">
                        @error('new_budget_amount') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Fiscal Year</label>
                        <input type="text" wire:model="new_budget_fiscal_year" placeholder="e.g. 2026-2027" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs">
                        @error('new_budget_fiscal_year') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 mb-1">Start Date</label>
                            <input type="date" wire:model="new_budget_start_date" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-2 text-[10px]">
                            @error('new_budget_start_date') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 mb-1">End Date</label>
                            <input type="date" wire:model="new_budget_end_date" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-2 text-[10px]">
                            @error('new_budget_end_date') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Notes</label>
                        <textarea wire:model="new_budget_notes" rows="2" placeholder="Budget allocation notes..." class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl transition-colors mt-2">
                        Allocate Quota
                    </button>
                </form>
            </div>

            <!-- Budgets vs Actuals Comparison -->
            <div class="col-span-2 bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Active Budget Comparison Tracker</h3>
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($budgets as $bg)
                        @php
                            $actualSpent = $bg->account->balance;
                            $remaining = (float)$bg->amount - $actualSpent;
                            $pctUsed = $bg->amount > 0 ? ($actualSpent / $bg->amount) * 100 : 0;
                        @endphp
                        <div class="p-4 bg-slate-950 border border-white/5 rounded-xl space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <div>
                                    <h4 class="font-bold text-white">{{ $bg->account->name }}</h4>
                                    <span class="text-[9px] bg-slate-800 text-slate-450 px-2 py-0.5 rounded font-mono font-bold mt-1 inline-block">Code: {{ $bg->account->code }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block">Fiscal: <strong>{{ $bg->fiscal_year }}</strong></span>
                                    <span class="text-[9px] text-slate-500">{{ $bg->start_date->format('M Y') }} — {{ $bg->end_date->format('M Y') }}</span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="space-y-1 pt-1">
                                <div class="w-full bg-slate-900 h-2.5 rounded-full overflow-hidden border border-white/5">
                                    <div class="h-full {{ $pctUsed > 100 ? 'bg-rose-500' : ($pctUsed > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $pctUsed) }}%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] text-slate-400">
                                    <span>Spent: ${{ number_format($actualSpent, 2) }}</span>
                                    <span>Quota: ${{ number_format((float)$bg->amount, 2) }}</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center text-[11px] pt-1.5 border-t border-white/5">
                                <span class="text-slate-500">Status standing:</span>
                                @if($pctUsed > 100)
                                    <span class="px-2 py-0.5 rounded font-bold bg-rose-500/10 text-rose-450 border border-rose-500/20">❌ EXCEEDED BY ${{ number_format(abs($remaining), 2) }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded font-bold bg-emerald-500/10 text-emerald-450 border border-emerald-500/20">✅ SAFE — ${{ number_format($remaining, 2) }} remaining</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 text-xs italic py-12 text-center">No budgets created yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 5: PROCUREMENT, VENDORS & PO STATUS TRACKING -->
    @if($activeTab === 'vendors')
        <div class="space-y-6">
            <!-- Expenses Approvals & PO List -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Expense Approvals -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
                    <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Pending & Processed Expenses</h3>
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                        @forelse($expenses as $exp)
                            <div class="p-3 bg-slate-950 border border-white/5 rounded-xl space-y-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-xs font-bold text-white">{{ $exp->description }}</h4>
                                        <p class="text-[10px] text-slate-500 mt-1">Vendor: <strong>{{ $exp->vendor->name ?? 'N/A' }}</strong></p>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded font-mono font-bold">${{ number_format($exp->amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[10px] text-slate-400 pt-2 border-t border-white/5">
                                    <span>Date: {{ $exp->expense_date->format('M d, Y') }}</span>
                                    <div class="flex gap-2">
                                        @if($exp->status === 'PENDING')
                                            <button wire:click="approveExpense('{{ $exp->id }}')" class="px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[9px]">Approve</button>
                                            <button wire:click="rejectExpense('{{ $exp->id }}')" class="px-2.5 py-1 rounded bg-rose-600 hover:bg-rose-500 text-white font-bold text-[9px]">Reject</button>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $exp->status === 'APPROVED' ? 'bg-emerald-500/10 text-emerald-450 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-450 border border-rose-500/20' }}">
                                                {{ $exp->status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-500 text-xs italic">No expenses submitted.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Purchase Orders -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4">
                    <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Purchase Orders & Procurement</h3>
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                        @forelse($purchaseOrders as $po)
                            <div class="p-3 bg-slate-950 border border-white/5 rounded-xl space-y-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="text-xs font-bold text-white">PO #{{ strtoupper(substr($po->id, 0, 8)) }}</h4>
                                        <p class="text-[10px] text-slate-500">Vendor: <strong>{{ $po->vendor->name }}</strong></p>
                                    </div>
                                    <span class="text-xs font-bold text-blue-400 font-mono">${{ number_format((float)$po->total_amount, 2) }}</span>
                                </div>

                                <div class="text-[10px] text-slate-400 space-y-1 bg-white/5 p-2 rounded-lg">
                                    @foreach($po->lines as $line)
                                        <div class="flex justify-between">
                                            <span>{{ $line->item_description }} (x{{ $line->quantity }})</span>
                                            <span>${{ number_format($line->total, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex justify-between items-center text-[10px] pt-2 border-t border-white/5">
                                    <div class="text-slate-500">
                                        Due: <strong>{{ $po->due_date ? $po->due_date->format('M d, Y') : 'N/A' }}</strong>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $po->status === 'RECEIVED' ? 'bg-emerald-500/10 text-emerald-450 border border-emerald-500/20' : ($po->status === 'SENT' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-slate-800 text-slate-400') }}">
                                            {{ $po->status }}
                                        </span>
                                        
                                        @if($po->status === 'SENT')
                                            <button wire:click="updatePoStatus('{{ $po->id }}', 'RECEIVED')" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-750 text-white font-bold text-[9px]">Mark Received</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-500 text-xs italic">No purchase orders listed.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
