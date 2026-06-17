<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── FACULTIES (College/University level groupings) ────────────────────
        Schema::create('faculties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('head_user_id')->nullable(); // Department head
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, INACTIVE
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        // ── ACADEMIC YEARS ───────────────────────────────────────────────────
        Schema::create('academic_years', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // e.g. "2024-2025"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->string('status')->default('ACTIVE'); // ACTIVE, ARCHIVED
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'is_current']);
        });

        // ── ACADEMIC SESSIONS (Semesters / Terms / Quarters) ─────────────────
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name'); // e.g. "Fall 2024", "Spring 2025", "Term 1"
            $table->string('type')->default('SEMESTER'); // SEMESTER, TERM, QUARTER, TRIMESTER
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->string('status')->default('UPCOMING'); // UPCOMING, ACTIVE, COMPLETED
            $table->timestamps();
            $table->index(['tenant_id', 'is_current', 'status']);
        });

        // ── PROGRAMS (Degree/Diploma/Certificate programs) ───────────────────
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('faculty_id')->nullable()->constrained('faculties')->nullOnDelete();
            $table->uuid('department_id')->nullable();
            $table->string('name'); // e.g. "Bachelor of Computer Science"
            $table->string('code')->nullable(); // e.g. "BSCS"
            $table->string('degree_type')->default('BACHELOR'); // CERTIFICATE, DIPLOMA, BACHELOR, MASTER, PHD, ASSOCIATE
            $table->integer('duration_years')->default(4);
            $table->integer('total_semesters')->default(8);
            $table->decimal('credit_hours_required', 6, 2)->default(130);
            $table->decimal('min_cgpa_required', 3, 2)->default(2.00);
            $table->text('description')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, INACTIVE, DISCONTINUED
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        // ── SUBJECTS ─────────────────────────────────────────────────────────
        Schema::create('subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('department_id')->nullable();
            $table->string('name');
            $table->string('code')->nullable(); // e.g. "CS-301"
            $table->decimal('credit_hours', 4, 1)->default(3.0);
            $table->string('type')->default('THEORY'); // THEORY, LAB, SEMINAR, PROJECT, INTERNSHIP
            $table->boolean('is_elective')->default(false);
            $table->text('description')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        // ── PROGRAM–SUBJECT CURRICULUM MAPPING ───────────────────────────────
        Schema::create('program_subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignUuid('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->integer('semester_no')->default(1); // Which semester this subject belongs to
            $table->boolean('is_elective')->default(false);
            $table->boolean('is_prerequisite_required')->default(false);
            $table->uuid('prerequisite_subject_id')->nullable();
            $table->timestamps();
            $table->unique(['program_id', 'subject_id', 'semester_no']);
        });

        // ── GRADING RULES (per program or tenant-wide) ───────────────────────
        Schema::create('grading_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('program_id')->nullable(); // null = applies to all programs
            $table->string('grade_letter'); // A+, A, B+, B, C, D, F
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->decimal('grade_points', 3, 2); // 4.00, 3.70, 3.30, etc.
            $table->string('status')->default('PASS'); // PASS, FAIL
            $table->timestamps();
            $table->index(['tenant_id']);
        });

        // ── CGPA RULES (Academic standing thresholds) ────────────────────────
        Schema::create('cgpa_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->uuid('program_id')->nullable();
            $table->string('standing_name'); // Dean's List, Good Standing, Academic Warning, Probation, Dismissed
            $table->decimal('min_cgpa', 3, 2);
            $table->decimal('max_cgpa', 3, 2);
            $table->string('status_tag'); // EXCELLENT, GOOD, WARNING, PROBATION, DISMISSED
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['tenant_id']);
        });

        // ── ACADEMIC CALENDARS ───────────────────────────────────────────────
        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->uuid('academic_session_id')->nullable();
            $table->string('title');
            $table->date('event_date');
            $table->date('event_end_date')->nullable();
            $table->string('event_type')->default('GENERAL'); // HOLIDAY, EXAM, REGISTRATION, ORIENTATION, GENERAL, BREAK
            $table->text('description')->nullable();
            $table->boolean('is_holiday')->default(false);
            $table->string('color')->default('#3B82F6'); // For calendar display
            $table->timestamps();
            $table->index(['tenant_id', 'event_date']);
        });

        // ── STUDENT ACADEMIC ENROLLMENTS (per session) ───────────────────────
        Schema::create('student_session_enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->uuid('student_user_id');
            $table->uuid('program_id')->nullable();
            $table->integer('current_semester')->default(1);
            $table->decimal('semester_gpa', 3, 2)->nullable();
            $table->decimal('cumulative_cgpa', 3, 2)->nullable();
            $table->decimal('credits_earned', 6, 2)->default(0);
            $table->string('academic_standing')->default('GOOD'); // EXCELLENT, GOOD, WARNING, PROBATION, DISMISSED
            $table->string('status')->default('ENROLLED'); // ENROLLED, PROMOTED, TRANSFERRED, DROPPED, GRADUATED
            $table->timestamps();
            $table->index(['tenant_id', 'academic_session_id', 'student_user_id'], 'sse_tenant_session_student_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_session_enrollments');
        Schema::dropIfExists('academic_calendars');
        Schema::dropIfExists('cgpa_rules');
        Schema::dropIfExists('grading_rules');
        Schema::dropIfExists('program_subjects');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('academic_sessions');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('faculties');
    }
};
