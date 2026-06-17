<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── TIMETABLE SLOTS (time period definitions) ─────────────────────────
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('branch_id')->nullable();
            $table->string('label'); // "Period 1", "9:00 AM – 9:50 AM"
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_break')->default(false); // Lunch, Break slots
            $table->timestamps();
            $table->index(['tenant_id']);
        });

        // ── TIMETABLES (master schedule containers) ───────────────────────────
        Schema::create('timetables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('branch_id')->nullable();
            $table->uuid('academic_session_id')->nullable();
            $table->uuid('program_id')->nullable();
            $table->string('title');
            $table->integer('semester_no')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        // ── TIMETABLE ENTRIES (individual schedule cells) ─────────────────────
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('timetable_id')->constrained('timetables')->cascadeOnDelete();
            $table->foreignUuid('slot_id')->constrained('timetable_slots')->cascadeOnDelete();
            $table->uuid('subject_id')->nullable();
            $table->uuid('room_id')->nullable();
            $table->uuid('teacher_user_id')->nullable();
            $table->tinyInteger('day_of_week'); // 1=Mon, 2=Tue ... 7=Sun
            $table->date('entry_date_override')->nullable(); // For one-off date overrides
            $table->string('entry_type')->default('REGULAR'); // REGULAR, MAKEUP, EXAM, LAB
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['timetable_id', 'day_of_week']);
        });

        // ── PARENT-STUDENT LINKS ──────────────────────────────────────────────
        Schema::create('parent_student_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('parent_user_id');
            $table->uuid('student_user_id');
            $table->string('relationship')->default('GUARDIAN'); // FATHER, MOTHER, GUARDIAN, SIBLING
            $table->boolean('is_primary')->default(true);
            $table->boolean('can_view_fees')->default(true);
            $table->boolean('can_view_attendance')->default(true);
            $table->boolean('can_view_grades')->default(true);
            $table->boolean('can_message_teachers')->default(true);
            $table->timestamps();
            $table->unique(['parent_user_id', 'student_user_id']);
            $table->index(['tenant_id', 'parent_user_id']);
            $table->index(['tenant_id', 'student_user_id']);
        });

        // ── PARENT MESSAGES (Teacher ↔ Parent direct messaging) ───────────────
        Schema::create('parent_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('sender_user_id');
            $table->uuid('receiver_user_id');
            $table->uuid('student_user_id')->nullable(); // Context: which student
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'receiver_user_id', 'is_read']);
        });

        // ── BEHAVIOR REPORTS ──────────────────────────────────────────────────
        Schema::create('behavior_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('student_user_id');
            $table->uuid('reported_by');
            $table->date('incident_date');
            $table->string('incident_type')->default('GENERAL'); // ACADEMIC, BEHAVIORAL, ATTENDANCE, DISCIPLINARY
            $table->string('severity')->default('LOW'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->text('description');
            $table->text('action_taken')->nullable();
            $table->boolean('parent_notified')->default(false);
            $table->timestamp('parent_notified_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'student_user_id', 'incident_date']);
        });

        // ── COUNSELING CASES ──────────────────────────────────────────────────
        Schema::create('student_counseling_cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('student_user_id');
            $table->uuid('counselor_user_id')->nullable();
            $table->string('case_type')->default('ACADEMIC'); // ACADEMIC, PERSONAL, CAREER, FINANCIAL, BEHAVIORAL
            $table->string('priority')->default('MEDIUM'); // LOW, MEDIUM, HIGH, URGENT
            $table->string('status')->default('OPEN'); // OPEN, IN_PROGRESS, RESOLVED, CLOSED
            $table->text('summary');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status', 'priority']);
            $table->index(['tenant_id', 'student_user_id']);
        });

        // ── COUNSELING SESSIONS ───────────────────────────────────────────────
        Schema::create('counseling_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->constrained('student_counseling_cases')->cascadeOnDelete();
            $table->date('session_date');
            $table->integer('duration_minutes')->default(30);
            $table->text('notes')->nullable();
            $table->text('next_steps')->nullable();
            $table->boolean('is_confidential')->default(true);
            $table->timestamps();
        });

        // ── INTERVENTION PLANS ────────────────────────────────────────────────
        Schema::create('intervention_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->constrained('student_counseling_cases')->cascadeOnDelete();
            $table->string('goal');
            $table->json('action_steps')->nullable();
            $table->date('target_date')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, ACHIEVED, REVISED, ABANDONED
            $table->text('outcome')->nullable();
            $table->timestamps();
        });

        // ── ACADEMIC WARNINGS ─────────────────────────────────────────────────
        Schema::create('academic_warnings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('student_user_id');
            $table->uuid('issued_by');
            $table->string('warning_type'); // LOW_CGPA, LOW_ATTENDANCE, INCOMPLETE_SUBMISSIONS, PROBATION
            $table->decimal('cgpa_at_warning', 3, 2)->nullable();
            $table->decimal('attendance_pct_at_warning', 5, 2)->nullable();
            $table->text('description');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'student_user_id', 'is_resolved']);
        });

        // ── DISCIPLINARY ACTIONS ──────────────────────────────────────────────
        Schema::create('disciplinary_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('student_user_id');
            $table->uuid('issued_by');
            $table->string('action_type'); // WARNING, SUSPENSION, EXPULSION, COMMUNITY_SERVICE, FINE, PROBATION
            $table->text('description');
            $table->date('issued_at');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'student_user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_actions');
        Schema::dropIfExists('academic_warnings');
        Schema::dropIfExists('intervention_plans');
        Schema::dropIfExists('counseling_sessions');
        Schema::dropIfExists('student_counseling_cases');
        Schema::dropIfExists('behavior_reports');
        Schema::dropIfExists('parent_messages');
        Schema::dropIfExists('parent_student_links');
        Schema::dropIfExists('timetable_entries');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('timetable_slots');
    }
};
