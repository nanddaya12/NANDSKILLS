<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->string('language')->default('English');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('version')->default(1);
            $table->uuid('parent_id')->nullable(); // For course versioning
            $table->boolean('is_bundle')->default(false);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('course_categories', function (Blueprint $table) {
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['course_id', 'category_id']);
        });

        Schema::create('learning_paths', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->timestamps();
        });

        Schema::create('learning_path_courses', function (Blueprint $table) {
            $table->foreignUuid('learning_path_id')->constrained('learning_paths')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->integer('order_index');
            $table->primary(['learning_path_id', 'course_id']);
        });

        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignUuid('prerequisite_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->primary(['course_id', 'prerequisite_id']);
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order_index');
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('type'); // VIDEO, AUDIO, PDF, DOC, PPT, HTML, SCORM, ASSIGNMENT, QUIZ
            $table->string('url')->nullable();
            $table->string('storage_key')->nullable();
            $table->boolean('is_downloadable')->default(false);
            $table->integer('order_index');
            $table->integer('duration_minutes')->default(0);
            $table->timestamps();
        });

        Schema::create('lesson_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('lesson_resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_url');
            $table->string('storage_key');
            $table->integer('file_size');
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->integer('progress_percent')->default(0);
            $table->string('status')->default('ACTIVE'); // ACTIVE, COMPLETED, DROPPED
            $table->dateTime('enrolled_at');
            $table->dateTime('completed_at')->nullable();

            $table->unique(['user_id', 'course_id']);
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->integer('max_points');
            $table->dateTime('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('SUBMITTED'); // SUBMITTED, GRADED, RESUBMIT_REQUESTED
            $table->string('submission_url');
            $table->string('storage_key')->nullable();
            $table->integer('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->dateTime('submitted_at');
            $table->dateTime('graded_at')->nullable();
            $table->foreignUuid('graded_by')->nullable()->references('id')->on('users');
        });

        Schema::create('quizzes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit_minutes')->default(0); // 0 = no limit
            $table->integer('max_attempts')->default(1);
            $table->integer('passing_score')->default(60); // percent
            $table->boolean('shuffle_questions')->default(false);
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->text('text');
            $table->string('type'); // MCQ, MULTIPLE_CHOICE, TRUE_FALSE, FILL_IN_BLANKS, SUBJECTIVE
            $table->integer('points')->default(1);
            $table->jsonb('options')->nullable(); // array of options
            $table->jsonb('correct_answer');      // correct answer data
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->jsonb('answers')->nullable(); // answers selected
            $table->string('status')->default('IN_PROGRESS'); // IN_PROGRESS, COMPLETED
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->date('date');
            $table->string('status'); // PRESENT, ABSENT, LATE, EXCUSED
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('marked_by_id')->references('id')->on('users');
            $table->string('qr_code')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('course_id')->nullable()->constrained('courses')->cascadeOnDelete();
            $table->string('certificate_code')->unique();
            $table->string('verification_url');
            $table->dateTime('issue_date');
            $table->string('pdf_url');
            $table->string('storage_key');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('lesson_resources');
        Schema::dropIfExists('lesson_notes');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('course_prerequisites');
        Schema::dropIfExists('learning_path_courses');
        Schema::dropIfExists('learning_paths');
        Schema::dropIfExists('course_categories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('courses');
    }
};
