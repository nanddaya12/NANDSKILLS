<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->string('frequency'); // ONCE, MONTHLY, TERM
            $table->timestamps();
        });

        Schema::create('student_fees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->references('id')->on('student_profiles')->cascadeOnDelete();
            $table->foreignUuid('fee_structure_id')->constrained('fee_structures')->cascadeOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('scholarship_amount', 10, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2);
            $table->string('status')->default('UNPAID'); // UNPAID, PARTIALLY_PAID, PAID
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('student_fee_id')->constrained('student_fees')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('UNPAID'); // UNPAID, PAID, PARTIALLY_PAID, OVERDUE, VOID
            $table->date('due_date');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method'); // STRIPE, PAYPAL, BANK_TRANSFER, CASH
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('COMPLETED'); // PENDING, COMPLETED, FAILED, REFUNDED
            $table->dateTime('paid_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('fee_structures');
    }
};
