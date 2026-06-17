<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── CHART OF ACCOUNTS ────────────────────────────────────────────────
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('code')->unique(); // Account code, e.g. "1000", "2000", "5010"
            $table->string('name'); // e.g. "Cash", "Accounts Payable", "Tuition Revenue"
            $table->string('type'); // ASSET, LIABILITY, EQUITY, REVENUE, EXPENSE
            $table->text('description')->nullable();
            $table->uuid('parent_id')->nullable(); // For hierarchical chart of accounts
            $table->timestamps();
            $table->index(['tenant_id', 'code', 'type']);
        });

        // ── VENDORS ──────────────────────────────────────────────────────────
        Schema::create('vendors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_number')->nullable();
            $table->timestamps();
            $table->index(['tenant_id']);
        });

        // ── JOURNAL ENTRIES ──────────────────────────────────────────────────
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->date('entry_date');
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->uuid('posted_by_user_id')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, POSTED
            $table->timestamps();
            $table->index(['tenant_id', 'entry_date', 'status']);
        });

        // ── JOURNAL ENTRY LINES (Debits & Credits) ──────────────────────────
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('type'); // DEBIT, CREDIT
            $table->decimal('amount', 15, 2);
            $table->string('memo')->nullable();
            $table->timestamps();
            $table->index(['journal_entry_id', 'account_id']);
        });

        // ── EXPENSES ──────────────────────────────────────────────────────────
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete(); // Expense account
            $table->foreignUuid('payment_account_id')->constrained('accounts')->cascadeOnDelete(); // Asset account (Cash/Bank)
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->foreignUuid('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('receipt_file_path')->nullable();
            $table->string('status')->default('APPROVED'); // PENDING, APPROVED, REJECTED
            $table->timestamps();
            $table->index(['tenant_id', 'expense_date']);
        });

        // ── BUDGETS ──────────────────────────────────────────────────────────
        Schema::create('budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete(); // Revenue or Expense account
            $table->decimal('amount', 15, 2);
            $table->string('fiscal_year'); // e.g. "2026-2027"
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'account_id', 'fiscal_year']);
        });

        // ── PURCHASE ORDERS ──────────────────────────────────────────────────
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->date('order_date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, SENT, RECEIVED, CANCELLED
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'vendor_id', 'status']);
        });

        // ── PURCHASE ORDER LINES ─────────────────────────────────────────────
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->text('item_description');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
            $table->index(['purchase_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('accounts');
    }
};
