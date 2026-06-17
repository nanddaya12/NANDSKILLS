<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Performance Optimization Migration
 * Adds composite indexes for the most critical query paths in NANDSKILLS:
 * — Attendance: tenant+user+date filtering (most frequent query in reports)
 * — Enrollments: user+course composite lookup
 * — Invoices: tenant+status+due_date (financial reporting)
 * — ExamResults: user+exam filtering
 * — Messages: receiver+is_read (unread inbox queries)
 * — FailedLogins: tenant+email+created_at (security auditing)
 * — Users: tenant+status filtering
 * — Sessions: user_id for quick revocation
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Attendance ───────────────────────────────────────────────────────
        if (Schema::hasTable('attendance')) {
            Schema::table('attendance', function (Blueprint $table) {
                // Composite index for per-tenant date-range attendance reports
                $this->addIndexIfMissing('attendance', 'idx_attendance_tenant_user_date', ['tenant_id', 'user_id', 'date']);
                // Index for status filtering (PRESENT / ABSENT / LATE)
                $this->addIndexIfMissing('attendance', 'idx_attendance_user_status', ['user_id', 'status']);
            });
        }

        // ── Enrollments ──────────────────────────────────────────────────────
        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $this->addIndexIfMissing('enrollments', 'idx_enrollments_user_course', ['user_id', 'course_id']);
                $this->addIndexIfMissing('enrollments', 'idx_enrollments_status', ['status']);
            });
        }

        // ── Invoices ─────────────────────────────────────────────────────────
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $this->addIndexIfMissing('invoices', 'idx_invoices_tenant_status_due', ['tenant_id', 'status', 'due_date']);
            });
        }

        // ── Exam Results ─────────────────────────────────────────────────────
        if (Schema::hasTable('exam_results')) {
            Schema::table('exam_results', function (Blueprint $table) {
                $this->addIndexIfMissing('exam_results', 'idx_exam_results_user_exam', ['user_id', 'exam_id']);
                $this->addIndexIfMissing('exam_results', 'idx_exam_results_tenant_status', ['tenant_id', 'status']);
            });
        }

        // ── Messages ─────────────────────────────────────────────────────────
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                $this->addIndexIfMissing('messages', 'idx_messages_receiver_read', ['receiver_id', 'is_read']);
                $this->addIndexIfMissing('messages', 'idx_messages_conversation', ['sender_id', 'receiver_id']);
            });
        }

        // ── Failed Logins ────────────────────────────────────────────────────
        if (Schema::hasTable('failed_logins')) {
            Schema::table('failed_logins', function (Blueprint $table) {
                $this->addIndexIfMissing('failed_logins', 'idx_failed_logins_tenant_email_date', ['tenant_id', 'email', 'created_at']);
                $this->addIndexIfMissing('failed_logins', 'idx_failed_logins_ip', ['ip_address']);
            });
        }

        // ── Users ────────────────────────────────────────────────────────────
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $this->addIndexIfMissing('users', 'idx_users_tenant_status', ['tenant_id', 'status']);
            });
        }

        // ── Sessions ─────────────────────────────────────────────────────────
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $this->addIndexIfMissing('sessions', 'idx_sessions_user', ['user_id']);
                $this->addIndexIfMissing('sessions', 'idx_sessions_last_activity', ['last_activity']);
            });
        }

        // ── Courses ──────────────────────────────────────────────────────────
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $this->addIndexIfMissing('courses', 'idx_courses_tenant_status', ['tenant_id', 'status']);
                $this->addIndexIfMissing('courses', 'idx_courses_slug', ['slug']);
            });
        }

        // ── Student Profiles ─────────────────────────────────────────────────
        if (Schema::hasTable('student_profiles')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $this->addIndexIfMissing('student_profiles', 'idx_student_profiles_tenant_status', ['tenant_id', 'status']);
                $this->addIndexIfMissing('student_profiles', 'idx_student_profiles_roll', ['tenant_id', 'roll_number']);
            });
        }
    }

    public function down(): void
    {
        $map = [
            'attendance'      => ['idx_attendance_tenant_user_date', 'idx_attendance_user_status'],
            'enrollments'     => ['idx_enrollments_user_course', 'idx_enrollments_status'],
            'invoices'        => ['idx_invoices_tenant_status_due'],
            'exam_results'    => ['idx_exam_results_user_exam', 'idx_exam_results_tenant_status'],
            'messages'        => ['idx_messages_receiver_read', 'idx_messages_conversation'],
            'failed_logins'   => ['idx_failed_logins_tenant_email_date', 'idx_failed_logins_ip'],
            'users'           => ['idx_users_tenant_status'],
            'sessions'        => ['idx_sessions_user', 'idx_sessions_last_activity'],
            'courses'         => ['idx_courses_tenant_status', 'idx_courses_slug'],
            'student_profiles'=> ['idx_student_profiles_tenant_status', 'idx_student_profiles_roll'],
        ];

        foreach ($map as $table => $indexes) {
            if (Schema::hasTable($table)) {
                foreach ($indexes as $idx) {
                    try {
                        Schema::table($table, fn(Blueprint $t) => $t->dropIndex($idx));
                    } catch (\Throwable $e) {
                        // Index may not exist on rollback; safe to ignore
                    }
                }
            }
        }
    }

    /**
     * Helper — adds an index only if it does not already exist (idempotent).
     */
    private function addIndexIfMissing(string $table, string $indexName, array $columns): void
    {
        $driver = DB::connection()->getDriverName();
        $exists = false;

        try {
            if ($driver === 'mysql') {
                $exists = collect(DB::select("SHOW INDEX FROM `{$table}`"))
                    ->contains(fn($row) => $row->Key_name === $indexName);
            } elseif ($driver === 'sqlite') {
                $exists = collect(DB::select("PRAGMA index_list(`{$table}`)"))
                    ->contains(fn($row) => $row->name === $indexName);
            } else {
                $exists = collect(Schema::getIndexes($table))
                    ->contains(fn($idx) => ($idx['name'] ?? '') === $indexName);
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        if (!$exists) {
            try {
                Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                    $t->index($columns, $indexName);
                });
            } catch (\Throwable $e) {
                // Ignore to avoid blocking
            }
        }
    }
};
