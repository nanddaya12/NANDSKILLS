<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('source')->nullable(); // Organic, Paid, Referral
            $table->string('status')->default('NEW'); // NEW, CONTACT_ATTEMPTED, WORKING, QUALIFIED, UNQUALIFIED
            $table->decimal('value', 10, 2)->default(0.00);
            $table->foreignUuid('assigned_to_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->integer('order_index');
            $table->string('color')->default('#2563EB');
            $table->integer('probability')->default(100);
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignUuid('stage_id')->constrained('pipeline_stages')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->timestamp('closing_date')->nullable();
            $table->foreignUuid('assigned_to_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->string('status')->default('OPEN'); // OPEN, WON, LOST
            $table->timestamps();
        });

        Schema::create('crm_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lead_id')->nullable()->constrained('leads')->cascadeOnDelete();
            $table->foreignUuid('deal_id')->nullable()->constrained('deals')->cascadeOnDelete();
            $table->string('type'); // CALL, EMAIL, MEETING, NOTE, TASK
            $table->text('description');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignUuid('performed_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('pipeline_stages');
        Schema::dropIfExists('leads');
    }
};
