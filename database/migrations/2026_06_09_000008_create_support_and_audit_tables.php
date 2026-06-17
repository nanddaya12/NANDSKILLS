<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->jsonb('target_roles'); // array of targeted role names (e.g., ["student", "parent"])
            $table->foreignUuid('created_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('helpdesk_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('assignee_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('OPEN'); // OPEN, IN_PROGRESS, RESOLVED, CLOSED
            $table->string('priority')->default('MEDIUM'); // LOW, MEDIUM, HIGH, URGENT
            $table->foreignUuid('category_id')->constrained('ticket_categories')->cascadeOnDelete();
            $table->timestamp('sla_deadline')->nullable();
            $table->boolean('escalated')->default(false);
            $table->timestamps();
        });

        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ticket_id')->constrained('helpdesk_tickets')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // CREATE, UPDATE, DELETE, LOGIN, LOGOUT
            $table->string('entity'); // Course, Invoice, User, etc.
            $table->uuid('entity_id')->nullable();
            $table->jsonb('before_state')->nullable();
            $table->jsonb('after_state')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('helpdesk_tickets');
        Schema::dropIfExists('ticket_categories');
        Schema::dropIfExists('announcements');
    }
};
