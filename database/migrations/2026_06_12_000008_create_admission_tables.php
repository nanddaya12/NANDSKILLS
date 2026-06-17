<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── ADMISSION FORMS (configurable application templates) ─────────────
        Schema::create('admission_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('program_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('fields_config')->nullable(); // Dynamic form fields
            $table->boolean('is_active')->default(true);
            $table->date('application_open_date')->nullable();
            $table->date('application_close_date')->nullable();
            $table->decimal('application_fee', 10, 2)->default(0);
            $table->timestamps();
            $table->index(['tenant_id', 'is_active']);
        });

        // ── ADMISSIONS (individual applications) ─────────────────────────────
        Schema::create('admissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('admission_form_id')->nullable();
            $table->uuid('program_id')->nullable();
            $table->uuid('academic_session_id')->nullable();
            // Applicant info (pre-account creation)
            $table->string('applicant_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('previous_qualification')->nullable();
            $table->decimal('previous_grade', 5, 2)->nullable();
            $table->json('form_responses')->nullable(); // Dynamic field responses
            // Status tracking
            $table->string('status')->default('DRAFT');
            // DRAFT → SUBMITTED → UNDER_REVIEW → INTERVIEW_SCHEDULED → APPROVED → REJECTED → ENROLLED
            $table->string('application_number')->nullable()->unique();
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('decision_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            // Auto-created user_id after enrollment
            $table->uuid('created_user_id')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'program_id', 'academic_session_id']);
        });

        // ── ADMISSION DOCUMENTS ───────────────────────────────────────────────
        Schema::create('admission_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->string('document_type'); // TRANSCRIPT, ID_CARD, PHOTO, RECOMMENDATION, CERTIFICATE
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable(); // bytes
            $table->boolean('is_verified')->default(false);
            $table->uuid('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
        });

        // ── ADMISSION TESTS ───────────────────────────────────────────────────
        Schema::create('admission_tests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->string('test_name'); // Entry Test, NTS, SAT, IELTS, etc.
            $table->decimal('score', 6, 2);
            $table->decimal('max_score', 6, 2)->default(100);
            $table->decimal('passing_score', 6, 2)->nullable();
            $table->date('taken_at');
            $table->string('result')->default('PENDING'); // PASS, FAIL, PENDING
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── ADMISSION INTERVIEWS ──────────────────────────────────────────────
        Schema::create('admission_interviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->uuid('interviewer_id')->nullable();
            $table->dateTime('scheduled_at');
            $table->string('format')->default('IN_PERSON'); // IN_PERSON, ONLINE, PHONE
            $table->string('meeting_link')->nullable();
            $table->string('status')->default('SCHEDULED'); // SCHEDULED, COMPLETED, CANCELLED, NO_SHOW
            $table->text('notes')->nullable();
            $table->string('result')->nullable(); // RECOMMENDED, NOT_RECOMMENDED, CONDITIONAL
            $table->integer('score')->nullable(); // 1-10
            $table->timestamps();
        });

        // ── MERIT LISTS ───────────────────────────────────────────────────────
        Schema::create('merit_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('program_id')->nullable();
            $table->uuid('academic_session_id')->nullable();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->decimal('merit_score', 6, 2)->default(0); // Computed score
            $table->integer('rank')->nullable();
            $table->string('status')->default('LISTED'); // LISTED, OFFERED, ACCEPTED, DECLINED
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'program_id', 'academic_session_id', 'rank']);
        });

        // ── ADMISSION NOTIFICATIONS / WORKFLOW LOGS ───────────────────────────
        Schema::create('admission_workflow_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->uuid('changed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_workflow_logs');
        Schema::dropIfExists('merit_lists');
        Schema::dropIfExists('admission_interviews');
        Schema::dropIfExists('admission_tests');
        Schema::dropIfExists('admission_documents');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('admission_forms');
    }
};
