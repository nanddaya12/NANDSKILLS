<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MODULE 1: ADVANCED RBAC
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Add parent_role_id for Permission Inheritance
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'parent_role_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->uuid('parent_role_id')->nullable()->after('is_system');
            });
        }

        // Add expires_at, department_id, branch_id for Temporary / Granular Access to user_roles
        // Since user_roles was a composite primary key, we drop and re-create it with additional fields
        Schema::dropIfExists('user_roles');
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('id')->default(Illuminate\Support\Facades\DB::raw('(uuid())'))->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('role_id')->constrained('roles')->cascadeOnDelete();
            $table->uuid('department_id')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('access_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('requested_role');
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->text('reason');
            $table->foreignUuid('resolved_by')->nullable()->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('login_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTime('logged_in_at');
            $table->timestamps();
        });


        // MODULE 2: MULTI CAMPUS
        Schema::create('departments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('building_id')->constrained('buildings')->cascadeOnDelete();
            $table->string('name');
            $table->integer('capacity');
            $table->timestamps();
        });


        // MODULE 3: EXAMINATION SYSTEM
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default('ACTIVE'); // ACTIVE, COMPLETED
            $table->timestamps();
        });

        Schema::create('exam_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_session_id')->constrained('exam_sessions')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->string('type'); // THEORY, PRACTICAL
            $table->integer('total_marks')->default(100);
            $table->timestamps();
        });

        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignUuid('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->dateTime('date_time');
            $table->integer('duration_minutes');
            $table->timestamps();
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete(); // student
            $table->decimal('marks_obtained', 5, 2);
            $table->string('status')->default('PASSED'); // PASSED, FAILED, ABSENT
            $table->string('grade')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('grade_scales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('grade'); // A, B, C, D, F
            $table->integer('min_score');
            $table->integer('max_score');
            $table->decimal('gpa', 3, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('transcripts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('cgpa', 3, 2)->default(0.00);
            $table->dateTime('compiled_at');
            $table->string('file_url')->nullable();
            $table->timestamps();
        });


        // MODULE 4: ASSESSMENT & ENGINE
        Schema::create('assignment_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('weight', 5, 2)->default(100.00);
            $table->timestamps();
        });

        Schema::create('rubrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->string('criteria');
            $table->integer('max_points');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('peer_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('submission_id')->references('id')->on('assignment_submissions')->cascadeOnDelete();
            $table->foreignUuid('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->integer('score');
            $table->text('comments')->nullable();
            $table->timestamps();
        });


        // MODULE 5: CERTIFICATE VERIFICATION
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('content_html')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('certificate_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('certificate_id')->constrained('certificates')->cascadeOnDelete();
            $table->dateTime('verified_at');
            $table->string('ip_address', 45)->nullable();
            $table->text('browser')->nullable();
            $table->string('status')->default('SUCCESS');
            $table->timestamps();
        });


        // MODULE 6: SUBSCRIPTIONS USAGES
        Schema::create('subscription_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('metric'); // users_count, courses_count, storage_bytes
            $table->bigInteger('current_value')->default(0);
            $table->bigInteger('max_limit');
            $table->timestamps();
        });

        Schema::create('tenant_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('UNPAID'); // UNPAID, PAID, PAST_DUE
            $table->dateTime('due_date');
            $table->dateTime('paid_at')->nullable();
            $table->string('stripe_payment_id')->nullable();
            $table->timestamps();
        });


        // MODULE 7: WORKFLOW AUTOMATION
        Schema::create('automation_workflows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('trigger_event'); // STUDENT_REGISTERED, FEE_PAID, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('workflow_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('workflow_id')->constrained('automation_workflows')->cascadeOnDelete();
            $table->string('action_type'); // SEND_EMAIL, CREATE_INVOICE, etc.
            $table->jsonb('config')->nullable();
            $table->integer('order_index');
            $table->timestamps();
        });

        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('workflow_id')->constrained('automation_workflows')->cascadeOnDelete();
            $table->text('event_data')->nullable();
            $table->string('status')->default('SUCCESS'); // SUCCESS, FAILED
            $table->text('error_message')->nullable();
            $table->dateTime('executed_at');
            $table->timestamps();
        });


        // MODULE 8: FORM BUILDER
        Schema::create('custom_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });

        Schema::create('form_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('form_id')->constrained('custom_forms')->cascadeOnDelete();
            $table->string('label');
            $table->string('type'); // TEXT, DROPDOWN, CHECKBOX, FILE
            $table->string('name');
            $table->jsonb('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('order_index');
            $table->timestamps();
        });

        Schema::create('form_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('form_id')->constrained('custom_forms')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->jsonb('response_data');
            $table->timestamps();
        });


        // MODULE 9: REPORT BUILDER
        Schema::create('report_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // STUDENT, LMS, CRM, FINANCE
            $table->jsonb('configuration');
            $table->timestamps();
        });


        // MODULE 10: LEARNING PATHS (COMPETENCIES)
        Schema::create('competencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('student_competencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('student_id')->references('id')->on('student_profiles')->cascadeOnDelete();
            $table->foreignUuid('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->string('status')->default('ACHIEVED'); // ACHIEVED, IN_PROGRESS
            $table->timestamps();
        });


        // MODULE 11: GAMIFICATION
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon_url')->nullable();
            $table->integer('xp_required')->default(100);
            $table->timestamps();
        });

        Schema::create('user_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('points');
            $table->string('reason');
            $table->timestamps();
        });


        // MODULE 12: DISCUSSION FORUMS
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('forum_topics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained('forum_categories')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('forum_reactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('post_id')->constrained('forum_posts')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reaction_type'); // LIKE, CELEBRATE, LOVE
            $table->timestamps();
        });


        // MODULE 13: CONTENT LIBRARY
        Schema::create('content_libraries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('type'); // VIDEO, PDF, AUDIO, SCORM
            $table->string('file_url');
            $table->string('storage_key');
            $table->timestamps();
        });

        Schema::create('content_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('library_id')->constrained('content_libraries')->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('file_url');
            $table->string('change_log')->nullable();
            $table->timestamps();
        });


        // MODULE 14: CMS SECTIONS
        Schema::create('website_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('page_id')->references('id')->on('website_pages')->cascadeOnDelete();
            $table->string('section_type'); // HERO, CONTACT, NOTICE
            $table->jsonb('content_data')->nullable();
            $table->integer('order_index');
            $table->timestamps();
        });

        Schema::create('website_widgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // NEWSLETTER, CALENDAR
            $table->jsonb('configuration')->nullable();
            $table->timestamps();
        });


        // MODULE 15: BLOGS & ARTICLES
        Schema::create('blogs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->timestamps();
        });


        // MODULE 16: DOCUMENT MANAGEMENT
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, APPROVED
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('document_id')->constrained('documents')->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('file_url');
            $table->timestamps();
        });

        Schema::create('document_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignUuid('approved_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('APPROVED'); // APPROVED, REJECTED
            $table->timestamps();
        });


        // MODULE 17: NOTIFICATION CENTER
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('channel'); // EMAIL, SMS, WHATSAPP, PUSH
            $table->string('subject')->nullable();
            $table->text('body');
            $table->timestamps();
        });


        // MODULE 18: API PLATFORM
        Schema::create('api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('key_hash')->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('rate_limit')->default(60);
            $table->timestamps();
        });

        Schema::create('api_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('api_key_id')->constrained('api_keys')->cascadeOnDelete();
            $table->string('endpoint');
            $table->string('method');
            $table->string('ip_address', 45)->nullable();
            $table->integer('status_code');
            $table->timestamps();
        });


        // MODULE 19: HR MANAGEMENT
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('department_id')->constrained('departments')->cascadeOnDelete();
            $table->decimal('salary', 10, 2);
            $table->string('designation');
            $table->date('hire_date');
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('type'); // SICK, CASUAL, ANNUAL
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('bonus', 10, 2)->default(0.00);
            $table->decimal('deductions', 10, 2)->default(0.00);
            $table->string('status')->default('UNPAID'); // PAID, UNPAID
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });


        // MODULE 20: ASSETS & MAINTENANCE
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->string('status')->default('AVAILABLE'); // AVAILABLE, ASSIGNED, MAINTENANCE
            $table->timestamps();
        });

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('assigned_at');
            $table->dateTime('returned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_maintenance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->text('description');
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->string('status')->default('PENDING'); // PENDING, COMPLETED
            $table->timestamps();
        });


        // MODULE 21: LIBRARY MANAGEMENT
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->timestamps();
        });

        Schema::create('book_loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('loaned_at');
            $table->dateTime('due_date');
            $table->dateTime('returned_at')->nullable();
            $table->decimal('fine_amount', 8, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('book_reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('PENDING'); // PENDING, FULFILLED, CANCELED
            $table->timestamps();
        });


        // MODULE 22: ACCREDITATIONS & COMPLIANCE
        Schema::create('accreditations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('framework_name');
            $table->string('agency');
            $table->string('status')->default('PENDING'); // PENDING, ACCREDITED
            $table->timestamps();
        });

        Schema::create('audit_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('accreditation_id')->constrained('accreditations')->cascadeOnDelete();
            $table->string('auditor_name');
            $table->dateTime('audit_date');
            $table->string('status')->default('PASSED');
            $table->timestamps();
        });

        Schema::create('compliance_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('accreditation_id')->constrained('accreditations')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_met')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Accreditations
        Schema::dropIfExists('compliance_items');
        Schema::dropIfExists('audit_records');
        Schema::dropIfExists('accreditations');

        // Library
        Schema::dropIfExists('book_reservations');
        Schema::dropIfExists('book_loans');
        Schema::dropIfExists('books');

        // Assets
        Schema::dropIfExists('asset_maintenance');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');

        // HR
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('employees');

        // API Platform
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('api_keys');

        // Notifications
        Schema::dropIfExists('notification_templates');

        // Documents
        Schema::dropIfExists('document_approvals');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');

        // Blogs
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('blogs');

        // CMS Sections
        Schema::dropIfExists('website_widgets');
        Schema::dropIfExists('website_sections');

        // Content Library
        Schema::dropIfExists('content_versions');
        Schema::dropIfExists('content_libraries');

        // Forum
        Schema::dropIfExists('forum_reactions');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forum_categories');

        // Gamification
        Schema::dropIfExists('user_points');
        Schema::dropIfExists('badges');

        // Learning Paths
        Schema::dropIfExists('student_competencies');
        Schema::dropIfExists('competencies');

        // Builders
        Schema::dropIfExists('report_templates');
        Schema::dropIfExists('form_responses');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('custom_forms');

        // Automation Workflows
        Schema::dropIfExists('workflow_logs');
        Schema::dropIfExists('workflow_actions');
        Schema::dropIfExists('automation_workflows');

        // Subscriptions
        Schema::dropIfExists('tenant_invoices');
        Schema::dropIfExists('subscription_usages');

        // Certificates
        Schema::dropIfExists('certificate_verifications');
        Schema::dropIfExists('certificate_templates');

        // Assessment Engine
        Schema::dropIfExists('peer_reviews');
        Schema::dropIfExists('rubrics');
        Schema::dropIfExists('assignment_groups');

        // Examinations
        Schema::dropIfExists('transcripts');
        Schema::dropIfExists('grade_scales');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('exam_sessions');

        // Multi Campus
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('departments');

        // Advanced RBAC
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('access_requests');
        Schema::dropIfExists('user_roles');
        
        // Re-create simple user_roles
        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'parent_role_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('parent_role_id');
            });
        }

        Schema::dropIfExists('permission_groups');
    }
};
