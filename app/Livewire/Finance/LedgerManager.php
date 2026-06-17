<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use Illuminate\Support\Facades\DB;

class LedgerManager extends Component
{
    public string $activeTab = 'general_ledger'; // general_ledger, journal_entry, trial_balance, statements, budgets, vendors

    // Financial Statements Sub-Tabs
    public string $activeStatement = 'trial_balance'; // trial_balance, income_statement, balance_sheet, cash_flow

    // Lists
    public $accounts = [];
    public $journalEntries = [];
    public $expenses = [];
    public $budgets = [];
    public $vendors = [];
    public $purchaseOrders = [];

    // New Journal Entry Form State
    public string $entry_date = '';
    public string $reference_number = '';
    public string $description = '';
    public array $journalLines = [];

    // Budget creation state
    public string $new_budget_account_id = '';
    public float $new_budget_amount = 0.00;
    public string $new_budget_fiscal_year = '2026-2027';
    public string $new_budget_start_date = '';
    public string $new_budget_end_date = '';
    public string $new_budget_notes = '';

    public function mount()
    {
        if (!auth()->user() || (!auth()->user()->hasRole(['Super Admin', 'Tenant Admin']) && !auth()->user()->hasPermission('finance.manage'))) {
            abort(403, 'Unauthorized. Access to General Ledger requires finance.manage permission.');
        }

        $this->entry_date = now()->toDateString();
        $this->resetJournalLines();
        $this->loadData();
    }

    public function resetJournalLines()
    {
        $this->journalLines = [
            ['account_id' => '', 'type' => 'DEBIT', 'amount' => 0.00, 'memo' => ''],
            ['account_id' => '', 'type' => 'CREDIT', 'amount' => 0.00, 'memo' => ''],
        ];
    }

    public function addJournalLine()
    {
        $this->journalLines[] = ['account_id' => '', 'type' => 'DEBIT', 'amount' => 0.00, 'memo' => ''];
    }

    public function removeJournalLine($index)
    {
        if (count($this->journalLines) > 2) {
            unset($this->journalLines[$index]);
            $this->journalLines = array_values($this->journalLines);
        }
    }

    public function loadData()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($tenantId) {
            $this->accounts = Account::where('tenant_id', $tenantId)->orderBy('code')->get();
            $this->journalEntries = JournalEntry::where('tenant_id', $tenantId)
                ->with(['lines.account', 'postedBy'])
                ->orderBy('entry_date', 'desc')
                ->get();
            $this->expenses = Expense::where('tenant_id', $tenantId)
                ->with(['account', 'paymentAccount', 'vendor'])
                ->orderBy('expense_date', 'desc')
                ->get();
            $this->budgets = Budget::where('tenant_id', $tenantId)
                ->with('account')
                ->get();
            $this->vendors = Vendor::where('tenant_id', $tenantId)->get();
            $this->purchaseOrders = PurchaseOrder::where('tenant_id', $tenantId)
                ->with(['vendor', 'lines'])
                ->orderBy('order_date', 'desc')
                ->get();
        }
    }

    public function saveJournalEntry()
    {
        $this->validate([
            'entry_date' => 'required|date',
            'reference_number' => 'required|string',
            'description' => 'required|string',
            'journalLines' => 'required|array|min:2',
            'journalLines.*.account_id' => 'required|uuid',
            'journalLines.*.type' => 'required|in:DEBIT,CREDIT',
            'journalLines.*.amount' => 'required|numeric|min:0.01',
            'journalLines.*.memo' => 'nullable|string',
        ]);

        $debits = 0.00;
        $credits = 0.00;

        foreach ($this->journalLines as $line) {
            if ($line['type'] === 'DEBIT') {
                $debits += (float)$line['amount'];
            } else {
                $credits += (float)$line['amount'];
            }
        }

        if (abs($debits - $credits) > 0.01) {
            $this->addError('journalLines', 'Double-entry failure: Total Debits ($' . number_format($debits, 2) . ') must equal Total Credits ($' . number_format($credits, 2) . ').');
            return;
        }

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        DB::transaction(function () use ($tenantId) {
            $entry = JournalEntry::create([
                'tenant_id' => $tenantId,
                'entry_date' => $this->entry_date,
                'reference_number' => $this->reference_number,
                'description' => $this->description,
                'posted_by_user_id' => auth()->id(),
                'status' => 'POSTED', // Auto-post for general ledger entries from dashboard
            ]);

            foreach ($this->journalLines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'type' => $line['type'],
                    'amount' => $line['amount'],
                    'memo' => $line['memo'] ?? '',
                ]);
            }
        });

        session()->flash('success', 'Journal entry posted successfully.');
        $this->reset(['reference_number', 'description']);
        $this->resetJournalLines();
        $this->loadData();
    }

    public function approveExpense($expenseId)
    {
        $expense = Expense::find($expenseId);
        if ($expense) {
            $expense->update(['status' => 'APPROVED']);

            // Post double-entry journal entry for approved expense
            $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

            DB::transaction(function () use ($expense, $tenantId) {
                $entry = JournalEntry::create([
                    'tenant_id' => $tenantId,
                    'entry_date' => $expense->expense_date,
                    'reference_number' => 'EXP-' . strtoupper(substr($expense->id, 0, 8)),
                    'description' => 'Approved Expense: ' . $expense->description,
                    'posted_by_user_id' => auth()->id(),
                    'status' => 'POSTED',
                ]);

                // Debit Expense Account
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $expense->account_id,
                    'type' => 'DEBIT',
                    'amount' => $expense->amount,
                    'memo' => $expense->description,
                ]);

                // Credit Payment Account (Asset/Liability)
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $expense->payment_account_id,
                    'type' => 'CREDIT',
                    'amount' => $expense->amount,
                    'memo' => 'Payment source: ' . ($expense->paymentAccount->name ?? 'Cash/Bank'),
                ]);
            });

            session()->flash('success', 'Expense approved and ledger entries posted.');
            $this->loadData();
        }
    }

    public function rejectExpense($expenseId)
    {
        $expense = Expense::find($expenseId);
        if ($expense) {
            $expense->update(['status' => 'REJECTED']);
            session()->flash('success', 'Expense rejected.');
            $this->loadData();
        }
    }

    public function updatePoStatus($poId, $status)
    {
        $po = PurchaseOrder::find($poId);
        if ($po && in_array($status, ['SENT', 'RECEIVED', 'CANCELLED'])) {
            $po->update(['status' => $status]);
            session()->flash('success', 'Purchase Order status updated to ' . $status . '.');
            $this->loadData();
        }
    }

    public function saveBudget()
    {
        $this->validate([
            'new_budget_account_id' => 'required|uuid',
            'new_budget_amount' => 'required|numeric|min:0.01',
            'new_budget_fiscal_year' => 'required|string',
            'new_budget_start_date' => 'required|date',
            'new_budget_end_date' => 'required|date',
            'new_budget_notes' => 'nullable|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        Budget::create([
            'tenant_id' => $tenantId,
            'account_id' => $this->new_budget_account_id,
            'amount' => $this->new_budget_amount,
            'fiscal_year' => $this->new_budget_fiscal_year,
            'start_date' => $this->new_budget_start_date,
            'end_date' => $this->new_budget_end_date,
            'notes' => $this->new_budget_notes,
        ]);

        session()->flash('success', 'Budget quota allocated successfully.');
        $this->reset(['new_budget_account_id', 'new_budget_amount', 'new_budget_start_date', 'new_budget_end_date', 'new_budget_notes']);
        $this->loadData();
    }

    // Statement calculations helper
    public function getTrialBalanceData()
    {
        $debitTotal = 0.00;
        $creditTotal = 0.00;
        $accountsData = [];

        foreach ($this->accounts as $account) {
            $lines = $account->journalEntryLines()
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'POSTED');
                })->get();

            $debits = 0.00;
            $credits = 0.00;

            foreach ($lines as $line) {
                if ($line->type === 'DEBIT') {
                    $debits += (float)$line->amount;
                } else {
                    $credits += (float)$line->amount;
                }
            }

            $netBalance = $debits - $credits;

            if ($netBalance > 0) {
                $debitTotal += $netBalance;
                $accountsData[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $netBalance,
                    'credit' => 0.00
                ];
            } elseif ($netBalance < 0) {
                $absVal = abs($netBalance);
                $creditTotal += $absVal;
                $accountsData[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => 0.00,
                    'credit' => $absVal
                ];
            } else {
                $accountsData[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => 0.00,
                    'credit' => 0.00
                ];
            }
        }

        return [
            'accounts' => $accountsData,
            'debit_total' => $debitTotal,
            'credit_total' => $creditTotal,
        ];
    }

    public function getIncomeStatementData()
    {
        $revenueAccounts = [];
        $expenseAccounts = [];
        $totalRevenue = 0.00;
        $totalExpense = 0.00;

        foreach ($this->accounts as $account) {
            if ($account->type === 'REVENUE') {
                $bal = $account->balance;
                $revenueAccounts[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => $bal
                ];
                $totalRevenue += $bal;
            } elseif ($account->type === 'EXPENSE') {
                $bal = $account->balance;
                $expenseAccounts[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => $bal
                ];
                $totalExpense += $bal;
            }
        }

        return [
            'revenue_accounts' => $revenueAccounts,
            'expense_accounts' => $expenseAccounts,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_income' => $totalRevenue - $totalExpense
        ];
    }

    public function getBalanceSheetData()
    {
        $assetAccounts = [];
        $liabilityAccounts = [];
        $equityAccounts = [];
        $totalAssets = 0.00;
        $totalLiabilities = 0.00;
        $totalEquity = 0.00;

        // Calculate Net Income from Income Statement to add to retained earnings
        $incomeData = $this->getIncomeStatementData();
        $netIncome = $incomeData['net_income'];

        foreach ($this->accounts as $account) {
            $bal = $account->balance;
            if ($account->type === 'ASSET') {
                $assetAccounts[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => $bal
                ];
                $totalAssets += $bal;
            } elseif ($account->type === 'LIABILITY') {
                $liabilityAccounts[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => $bal
                ];
                $totalLiabilities += $bal;
            } elseif ($account->type === 'EQUITY') {
                // Adjust retained earnings with Net Income
                $displayBal = $bal;
                if ($account->code === '3000') { // Retained Earnings
                    $displayBal += $netIncome;
                }
                $equityAccounts[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => $displayBal
                ];
                $totalEquity += $displayBal;
            }
        }

        return [
            'assets' => $assetAccounts,
            'liabilities' => $liabilityAccounts,
            'equity' => $equityAccounts,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_equity' => $totalLiabilities + $totalEquity
        ];
    }

    public function getCashFlowData()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        // Cash/Bank account IDs
        $cashAccounts = Account::where('tenant_id', $tenantId)
            ->whereIn('code', ['1010', '1020']) // Cash on Hand, Bank Checking
            ->pluck('id')
            ->toArray();

        if (empty($cashAccounts)) {
            return [
                'inflows' => [],
                'outflows' => [],
                'total_inflow' => 0.00,
                'total_outflow' => 0.00,
                'net_cash_flow' => 0.00,
            ];
        }

        // Fetch all posted lines affecting cash/bank accounts
        $lines = JournalEntryLine::whereIn('account_id', $cashAccounts)
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'POSTED');
            })->with(['journalEntry.lines.account'])->get();

        $inflows = [];
        $outflows = [];
        $totalInflow = 0.00;
        $totalOutflow = 0.00;

        foreach ($lines as $line) {
            $amount = (float)$line->amount;
            $entry = $line->journalEntry;

            // Find offsetting accounts
            $offsettingLines = $entry->lines->filter(function ($l) use ($cashAccounts) {
                return !in_array($l->account_id, $cashAccounts);
            });

            $offsetName = 'General Inflow/Outflow';
            if ($offsettingLines->isNotEmpty()) {
                $offsetName = implode(', ', $offsettingLines->map(fn($o) => $o->account->name)->toArray());
            }

            if ($line->type === 'DEBIT') {
                // Inflow
                $inflows[] = [
                    'date' => $entry->entry_date->format('Y-m-d'),
                    'ref' => $entry->reference_number,
                    'memo' => $line->memo ?: $entry->description,
                    'offset' => $offsetName,
                    'amount' => $amount
                ];
                $totalInflow += $amount;
            } else {
                // Outflow
                $outflows[] = [
                    'date' => $entry->entry_date->format('Y-m-d'),
                    'ref' => $entry->reference_number,
                    'memo' => $line->memo ?: $entry->description,
                    'offset' => $offsetName,
                    'amount' => $amount
                ];
                $totalOutflow += $amount;
            }
        }

        return [
            'inflows' => $inflows,
            'outflows' => $outflows,
            'total_inflow' => $totalInflow,
            'total_outflow' => $totalOutflow,
            'net_cash_flow' => $totalInflow - $totalOutflow
        ];
    }

    public function render()
    {
        return view('livewire.finance.ledger-manager')
            ->layout('layouts.app');
    }
}
