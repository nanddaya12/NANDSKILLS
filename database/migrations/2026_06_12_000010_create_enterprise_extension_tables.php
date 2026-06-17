<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── DOCUMENT CATEGORIES ───────────────────────────────────────────────
        Schema::create('document_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('icon')->default('📄');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['tenant_id', 'parent_id']);
        });

        // ── MANAGED DOCUMENTS ─────────────────────────────────────────────────
        Schema::create('managed_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('category_id')->nullable();
            $table->string('owner_type')->nullable(); // student, staff, admission, tenant
            $table->uuid('owner_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->bigInteger('file_size')->default(0); // bytes
            $table->string('mime_type')->nullable();
            $table->string('disk')->default('local'); // local, s3, minio
            $table->integer('version')->default(1);
            $table->string('status')->default('DRAFT'); // DRAFT, PENDING_APPROVAL, APPROVED, REJECTED, EXPIRED
            $table->date('expiry_date')->nullable();
            $table->uuid('uploaded_by');
            $table->boolean('is_public')->default(false);
            $table->bigInteger('download_count')->default(0);
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'owner_type', 'owner_id']);
            $table->index(['tenant_id', 'category_id']);
        });

        // ── DOCUMENT VERSIONS ─────────────────────────────────────────────────
        Schema::create('managed_document_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('document_id')->constrained('managed_documents')->cascadeOnDelete();
            $table->integer('version_no');
            $table->string('file_path');
            $table->bigInteger('file_size')->default(0);
            $table->uuid('uploaded_by');
            $table->text('change_notes')->nullable();
            $table->timestamps();
        });

        // ── DOCUMENT APPROVALS ────────────────────────────────────────────────
        Schema::create('managed_document_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('document_id')->constrained('managed_documents')->cascadeOnDelete();
            $table->uuid('approver_user_id');
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->text('notes')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
        });

        // ── DOCUMENT AUDIT TRAIL ──────────────────────────────────────────────
        Schema::create('document_audit_trail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('document_id')->constrained('managed_documents')->cascadeOnDelete();
            $table->uuid('user_id')->nullable();
            $table->string('action'); // UPLOADED, DOWNLOADED, VIEWED, APPROVED, REJECTED, EXPIRED, DELETED
            $table->string('ip_address', 45)->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
            $table->index(['document_id', 'action']);
        });

        // ── NOTIFICATION CATEGORIES ───────────────────────────────────────────
        Schema::create('notification_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('icon')->default('🔔');
            $table->string('color')->default('#3B82F6');
            $table->timestamps();
        });

        // ── NOTIFICATION TEMPLATES ────────────────────────────────────────────
        Schema::dropIfExists('notification_templates');
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('category_id')->nullable();
            $table->string('name');
            $table->string('subject'); // Email subject / notification title
            $table->text('body_html')->nullable(); // Email/in-app body (supports {{student_name}} vars)
            $table->text('body_sms')->nullable(); // SMS body (short)
            $table->string('channel')->default('in_app'); // in_app, email, sms, whatsapp, push
            $table->string('audience')->default('students'); // students, teachers, parents, all, departments
            $table->json('audience_filter')->nullable(); // branch_id, department_id, program_id
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['tenant_id', 'channel', 'is_active']);
        });

        // ── SCHEDULED NOTIFICATIONS ───────────────────────────────────────────
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('template_id')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->string('channel')->default('in_app');
            $table->string('audience')->default('all');
            $table->json('audience_filter')->nullable();
            $table->dateTime('scheduled_at');
            $table->boolean('is_recurring')->default(false);
            $table->string('cron_expression')->nullable(); // For recurring e.g. "0 9 * * 1"
            $table->string('status')->default('PENDING'); // PENDING, SENT, CANCELLED, FAILED
            $table->uuid('created_by');
            $table->timestamps();
            $table->index(['tenant_id', 'status', 'scheduled_at']);
        });

        // ── NOTIFICATION LOGS ─────────────────────────────────────────────────
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('template_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('channel');
            $table->string('status')->default('PENDING'); // SENT, FAILED, PENDING, READ
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'user_id', 'status']);
        });

        // ── ACTIVITY LOGS (Enterprise Audit) ──────────────────────────────────
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('user_id')->nullable();
            $table->string('role_name')->nullable();
            $table->string('model_type')->nullable(); // App\Models\Student, etc.
            $table->uuid('model_id')->nullable();
            $table->string('action'); // CREATE, UPDATE, DELETE, LOGIN, LOGOUT, APPROVE, FINANCIAL, EXAM, ATTENDANCE
            $table->json('before_value')->nullable();
            $table->json('after_value')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'action', 'created_at']);
            $table->index(['tenant_id', 'user_id', 'created_at']);
            $table->index(['tenant_id', 'model_type', 'model_id']);
        });

        // ── COMPLIANCE FRAMEWORKS ─────────────────────────────────────────────
        Schema::create('compliance_frameworks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // e.g. "HEC Accreditation", "ISO 9001"
            $table->string('agency')->nullable(); // HEC, PEC, NCEAC, TVET, ISO
            $table->string('standard_version')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, EXPIRED, PENDING
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->integer('completion_percentage')->default(0);
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        // ── COMPLIANCE REQUIREMENTS ────────────────────────────────────────────
        Schema::create('compliance_requirements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('framework_id')->constrained('compliance_frameworks')->cascadeOnDelete();
            $table->string('requirement_code')->nullable(); // e.g. "HEC-3.1"
            $table->string('category'); // ACADEMIC, INFRASTRUCTURE, FACULTY, RESEARCH, GOVERNANCE
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('evidence_required')->default(true);
            $table->string('status')->default('PENDING'); // PENDING, IN_PROGRESS, MET, NOT_MET, WAIVED
            $table->timestamps();
            $table->index(['framework_id', 'status']);
        });

        // ── COMPLIANCE EVIDENCE ────────────────────────────────────────────────
        Schema::create('compliance_evidences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('requirement_id')->constrained('compliance_requirements')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->uuid('uploaded_by');
            $table->boolean('is_verified')->default(false);
            $table->uuid('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // ── COMPLIANCE AUDITS ─────────────────────────────────────────────────
        Schema::create('compliance_audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('framework_id')->nullable();
            $table->date('audit_date');
            $table->string('auditor_name')->nullable();
            $table->string('audit_type')->default('INTERNAL'); // INTERNAL, EXTERNAL
            $table->string('result')->default('PENDING'); // PASS, FAIL, PARTIAL, PENDING
            $table->text('findings')->nullable();
            $table->string('report_path')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'audit_date']);
        });

        // ── CORRECTIVE ACTIONS ────────────────────────────────────────────────
        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('audit_id')->nullable();
            $table->uuid('requirement_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->uuid('responsible_user_id')->nullable();
            $table->date('due_date')->nullable();
            $table->string('priority')->default('MEDIUM'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->string('status')->default('OPEN'); // OPEN, IN_PROGRESS, CLOSED, OVERDUE
            $table->text('resolution_notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        // ── BACKUP LOGS ────────────────────────────────────────────────────────
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('type')->default('MANUAL'); // MANUAL, SCHEDULED_DAILY, SCHEDULED_WEEKLY, SCHEDULED_MONTHLY
            $table->string('status')->default('PENDING'); // PENDING, IN_PROGRESS, COMPLETED, FAILED
            $table->string('path')->nullable(); // local or cloud path
            $table->string('disk')->default('local'); // local, s3, minio
            $table->bigInteger('size_bytes')->nullable();
            $table->uuid('triggered_by')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status', 'created_at']);
        });

        // ── COUPON CODES (SaaS Billing) ────────────────────────────────────────
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('discount_type')->default('PERCENTAGE'); // PERCENTAGE, FIXED
            $table->decimal('discount_value', 10, 2);
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('valid_from')->nullable();
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('applicable_plan_id')->nullable();
            $table->timestamps();
        });

        // ── NEWS ARTICLES (CMS) ────────────────────────────────────────────────
        Schema::create('news_articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('author_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status', 'published_at']);
        });

        // ── EVENTS (Public Calendar) ───────────────────────────────────────────
        Schema::create('public_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('event_type')->default('GENERAL'); // GENERAL, ADMISSION, SEMINAR, WORKSHOP, SPORTS, CULTURAL
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime')->nullable();
            $table->string('venue')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_public')->default(true);
            $table->string('status')->default('UPCOMING'); // UPCOMING, LIVE, COMPLETED, CANCELLED
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status', 'start_datetime']);
        });

        // ── PUSH NOTIFICATION TOKENS ───────────────────────────────────────────
        Schema::create('push_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('user_id');
            $table->string('token');
            $table->string('platform')->default('WEB'); // WEB, IOS, ANDROID
            $table->string('app_type')->default('student'); // student, teacher, parent, admin
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'token']);
            $table->index(['tenant_id', 'user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_tokens');
        Schema::dropIfExists('public_events');
        Schema::dropIfExists('news_articles');
        Schema::dropIfExists('coupon_codes');
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('corrective_actions');
        Schema::dropIfExists('compliance_audits');
        Schema::dropIfExists('compliance_evidences');
        Schema::dropIfExists('compliance_requirements');
        Schema::dropIfExists('compliance_frameworks');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('scheduled_notifications');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notification_categories');
        Schema::dropIfExists('document_audit_trail');
        Schema::dropIfExists('managed_document_approvals');
        Schema::dropIfExists('managed_document_versions');
        Schema::dropIfExists('managed_documents');
        Schema::dropIfExists('document_categories');
    }
};
