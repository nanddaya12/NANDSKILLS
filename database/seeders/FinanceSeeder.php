<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Account;
use App\Models\Vendor;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('subdomain', 'nandskills')->first();
        if (!$tenant) {
            return;
        }

        app()->instance('currentTenant', $tenant);

        // ── Seed Chart of Accounts ──
        $accounts = [
            // Assets
            ['code' => '1010', 'name' => 'Cash on Hand', 'type' => 'ASSET', 'description' => 'Cash vault and petty cash.'],
            ['code' => '1020', 'name' => 'Bank Checking Account', 'type' => 'ASSET', 'description' => 'Institutional banking checking.'],
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'ASSET', 'description' => 'Uncollected student tuition invoices.'],
            ['code' => '1300', 'name' => 'Prepaid Expenses', 'type' => 'ASSET', 'description' => 'Prepaid rents or insurances.'],

            // Liabilities
            ['code' => '2010', 'name' => 'Accounts Payable', 'type' => 'LIABILITY', 'description' => 'Outstanding vendor invoices.'],
            ['code' => '2100', 'name' => 'Unearned Tuition Fees', 'type' => 'LIABILITY', 'description' => 'Prepaid tuition fees for semesters not started.'],

            // Equity
            ['code' => '3000', 'name' => 'Institutional Retained Earnings', 'type' => 'EQUITY', 'description' => 'Accumulated academic earnings.'],

            // Revenue
            ['code' => '4010', 'name' => 'Tuition Fee Revenue', 'type' => 'REVENUE', 'description' => 'Earned academic course fees.'],
            ['code' => '4020', 'name' => 'Admissions Fee Revenue', 'type' => 'REVENUE', 'description' => 'One-time admission application fees.'],

            // Expenses
            ['code' => '5010', 'name' => 'Trainer Salaries Expense', 'type' => 'EXPENSE', 'description' => 'Instructors monthly salaries.'],
            ['code' => '5020', 'name' => 'Campus Rent Expense', 'type' => 'EXPENSE', 'description' => 'Rentals of buildings and facilities.'],
            ['code' => '5030', 'name' => 'Utility and Energy Expense', 'type' => 'EXPENSE', 'description' => 'Electricity, gas, and internet connectivity.'],
            ['code' => '5040', 'name' => 'Software & Subscription Expense', 'type' => 'EXPENSE', 'description' => 'SaaS subscription tools and LMS platform hosting.'],
        ];

        $createdAccounts = [];
        foreach ($accounts as $acc) {
            $createdAccounts[$acc['code']] = Account::firstOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $acc['code']],
                ['name' => $acc['name'], 'type' => $acc['type'], 'description' => $acc['description']]
            );
        }

        // ── Seed Vendors ──
        $vendors = [
            ['name' => 'City Power & Utilities', 'contact_name' => 'John Energy', 'email' => 'utilities@citypower.com', 'phone' => '+92-300-9876543', 'address' => 'Power House Sector, Jamshoro'],
            ['name' => 'TechSolutions Computer Hardware', 'contact_name' => 'Saeed Tech', 'email' => 'sales@techsolutions.com', 'phone' => '+92-321-4455667', 'address' => 'Saddar IT Market, Karachi'],
            ['name' => 'Campus Landlord Holdings', 'contact_name' => 'Aslam Malik', 'email' => 'rentals@landlord.com', 'phone' => '+92-333-1122334', 'address' => 'Malir Cantonment, Karachi'],
        ];

        $createdVendors = [];
        foreach ($vendors as $ven) {
            $createdVendors[] = Vendor::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $ven['name']],
                $ven
            );
        }

        // ── Seed Budgets ──
        Budget::firstOrCreate([
            'tenant_id' => $tenant->id,
            'account_id' => $createdAccounts['5030']->id, // Utilities
            'fiscal_year' => '2026-2027',
            'amount' => 12000.00,
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'notes' => 'Annual electricity and internet quota.'
        ]);

        Budget::firstOrCreate([
            'tenant_id' => $tenant->id,
            'account_id' => $createdAccounts['5040']->id, // Software
            'fiscal_year' => '2026-2027',
            'amount' => 8500.00,
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'notes' => 'Annual SaaS and hosting infrastructure.'
        ]);

        // ── Seed a Balanced Journal Entry (Double-Entry example) ──
        // Example: Initial capital deposit of $50,000 into Bank Checking
        $entry1 = JournalEntry::create([
            'tenant_id' => $tenant->id,
            'entry_date' => '2026-06-01',
            'reference_number' => 'JV-2026-001',
            'description' => 'Initial capital injection into checking account.',
            'status' => 'POSTED',
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry1->id,
            'account_id' => $createdAccounts['1020']->id, // Bank Checking
            'type' => 'DEBIT',
            'amount' => 50000.00,
            'memo' => 'Capital receipt.',
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry1->id,
            'account_id' => $createdAccounts['3000']->id, // Retained Earnings / Equity
            'type' => 'CREDIT',
            'amount' => 50000.00,
            'memo' => 'Owner capital allocation.',
        ]);

        // Example: Monthly Campus Rent payment of $2,500
        $entry2 = JournalEntry::create([
            'tenant_id' => $tenant->id,
            'entry_date' => '2026-06-05',
            'reference_number' => 'JV-2026-002',
            'description' => 'Paid campus monthly facility rental.',
            'status' => 'POSTED',
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry2->id,
            'account_id' => $createdAccounts['5020']->id, // Rent Expense
            'type' => 'DEBIT',
            'amount' => 2500.00,
            'memo' => 'June rent.',
        ]);

        JournalEntryLine::create([
            'journal_entry_id' => $entry2->id,
            'account_id' => $createdAccounts['1020']->id, // Bank Checking
            'type' => 'CREDIT',
            'amount' => 2500.00,
            'memo' => 'June rent check payment.',
        ]);

        // ── Seed an Expense Record ──
        Expense::create([
            'tenant_id' => $tenant->id,
            'account_id' => $createdAccounts['5030']->id, // Utilities
            'payment_account_id' => $createdAccounts['1010']->id, // Cash
            'amount' => 450.00,
            'expense_date' => '2026-06-10',
            'vendor_id' => $createdVendors[0]->id, // City Power
            'description' => 'Office internet subscription renewal.',
            'status' => 'APPROVED',
        ]);

        // ── Seed a Purchase Order ──
        $po = PurchaseOrder::create([
            'tenant_id' => $tenant->id,
            'vendor_id' => $createdVendors[1]->id, // TechSolutions
            'order_date' => '2026-06-12',
            'due_date' => '2026-06-25',
            'status' => 'SENT',
            'total_amount' => 3500.00,
            'terms' => 'Net 30 days',
            'notes' => 'Procurement of laboratory desktop workstations.'
        ]);

        PurchaseOrderLine::create([
            'purchase_order_id' => $po->id,
            'item_description' => 'Intel Core i7 Desktop Computer Systems',
            'quantity' => 5,
            'unit_price' => 700.00,
            'total' => 3500.00
        ]);
    }
}
