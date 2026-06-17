<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentProfile;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\ExamResult;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\Certificate;
use App\Models\VirtualClassroom;
use App\Models\Announcement;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Mobile App API Controller
 * Provides authenticated REST endpoints optimized for mobile student/trainer apps.
 */
class MobileApiController extends Controller
{
    // ── STUDENT PROFILE ──────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/profile
     * Returns the authenticated student's complete profile.
     */
    public function profile(Request $request)
    {
        $user    = $request->user();
        $student = StudentProfile::where('user_id', $user->id)->first();

        return response()->json([
            'user' => [
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'status'     => $user->status,
                'roles'      => $user->roles->pluck('name'),
            ],
            'student_profile' => $student ? [
                'roll_number'    => $student->roll_number,
                'admission_date' => $student->admission_date?->toDateString(),
                'status'         => $student->status,
            ] : null,
        ]);
    }

    // ── DASHBOARD ─────────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/dashboard
     * Returns a mobile home dashboard summary for the authenticated user.
     */
    public function dashboard(Request $request)
    {
        $user     = $request->user();
        $tenantId = $user->tenant_id;

        $enrolledCourses  = Enrollment::where('user_id', $user->id)->count();
        $totalAttendance  = Attendance::where('user_id', $user->id)->count();
        $presentCount     = Attendance::where('user_id', $user->id)->where('status', 'PRESENT')->count();
        $examResults      = ExamResult::where('user_id', $user->id)->count();
        $unreadMessages   = Message::where('receiver_id', $user->id)->where('is_read', false)->count();
        $pendingFees      = Invoice::where('tenant_id', $tenantId)->where('status', 'UNPAID')->count();

        $attendancePct = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 0;

        return response()->json([
            'enrolled_courses'  => $enrolledCourses,
            'attendance_pct'    => $attendancePct,
            'exam_results'      => $examResults,
            'unread_messages'   => $unreadMessages,
            'pending_fees'      => $pendingFees,
            'at_risk'           => $attendancePct < 75,
        ]);
    }

    // ── COURSES ─────────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/courses
     * Returns courses the student is enrolled in.
     */
    public function myCourses(Request $request)
    {
        $user = $request->user();

        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->get();

        return response()->json($enrollments->map(fn($e) => [
            'id'           => $e->course?->id,
            'title'        => $e->course?->title,
            'slug'         => $e->course?->slug,
            'status'       => $e->status,
            'progress_pct' => $e->progress_pct ?? 0,
            'enrolled_at'  => $e->created_at?->toDateString(),
        ]));
    }

    // ── ATTENDANCE ─────────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/attendance
     * Returns the student's recent attendance record.
     */
    public function myAttendance(Request $request)
    {
        $user   = $request->user();
        $limit  = min((int) $request->get('limit', 30), 100);

        $records = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take($limit)
            ->get();

        return response()->json($records->map(fn($r) => [
            'date'   => $r->date?->toDateString(),
            'status' => $r->status,
        ]));
    }

    // ── ASSIGNMENTS ─────────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/assignments
     * Returns upcoming assignments with due dates for enrolled courses.
     */
    public function myAssignments(Request $request)
    {
        $user = $request->user();

        // Get enrolled course IDs → lessons → assignments
        $enrolledCourseIds = Enrollment::where('user_id', $user->id)->pluck('course_id');

        $assignments = Assignment::whereHas('lesson', function($q) use ($enrolledCourseIds) {
                $q->whereHas('chapter', fn($q2) => $q2->whereIn('course_id', $enrolledCourseIds));
            })
            ->orderBy('due_date', 'asc')
            ->take(20)
            ->get();

        return response()->json($assignments->map(fn($a) => [
            'id'        => $a->id,
            'title'     => $a->title,
            'due_date'  => $a->due_date?->toDateString(),
            'max_score' => $a->max_points ?? 100,
        ]));
    }

    // ── EXAM RESULTS ─────────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/results
     * Returns the student's exam grades and GPA.
     */
    public function myResults(Request $request)
    {
        $user = $request->user();

        $results = ExamResult::with('exam.course')
            ->where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return response()->json($results->map(fn($r) => [
            'exam'    => $r->exam?->title,
            'course'  => $r->exam?->course?->title,
            'marks'   => $r->marks_obtained,
            'grade'   => $r->grade,
            'gpa'     => $r->gpa,
            'status'  => $r->status,
        ]));
    }

    // ── MESSAGES ─────────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/messages
     * Returns the student's direct messages.
     */
    public function myMessages(Request $request)
    {
        $user = $request->user();

        $messages = Message::with('sender')
            ->where('receiver_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return response()->json($messages->map(fn($m) => [
            'id'        => $m->id,
            'content'   => $m->content,
            'is_read'   => $m->is_read,
            'sender'    => $m->sender ? ($m->sender->first_name . ' ' . $m->sender->last_name) : 'System',
            'sent_at'   => $m->created_at?->toIso8601String(),
        ]));
    }

    /**
     * POST /api/v1/mobile/messages/{id}/read
     * Marks a message as read.
     */
    public function markMessageRead(Request $request, $id)
    {
        $msg = Message::where('receiver_id', $request->user()->id)->findOrFail($id);
        $msg->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // ── CERTIFICATES ─────────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/certificates
     * Returns issued certificates for the student.
     */
    public function myCertificates(Request $request)
    {
        $user = $request->user();

        $certs = Certificate::with('course')
            ->where('user_id', $user->id)
            ->get();

        return response()->json($certs->map(fn($c) => [
            'course'           => $c->course?->title,
            'certificate_code' => $c->certificate_code,
            'verify_url'       => url("/verify/{$c->certificate_code}"),
            'issued_on'        => $c->issue_date?->toDateString(),
        ]));
    }

    // ── ANNOUNCEMENTS ─────────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/announcements
     * Returns latest academy announcements.
     */
    public function announcements(Request $request)
    {
        $user          = $request->user();
        $announcements = Announcement::where('tenant_id', $user->tenant_id)
            ->latest()
            ->take(15)
            ->get();

        return response()->json($announcements->map(fn($a) => [
            'title'        => $a->title,
            'body'         => $a->content,
            'audience'     => $a->target_roles,
            'created_at'   => $a->created_at?->toIso8601String(),
        ]));
    }

    // ── VIRTUAL CLASSES ─────────────────────────────────────────────────────────

    /**
     * GET /api/v1/mobile/classes
     * Returns upcoming or live virtual classroom sessions.
     */
    public function myClasses(Request $request)
    {
        $user = $request->user();

        $classes = VirtualClassroom::where('tenant_id', $user->tenant_id)
            ->where('status', '!=', 'ENDED')
            ->latest()
            ->take(10)
            ->get();

        return response()->json($classes->map(fn($cls) => [
            'id'          => $cls->id,
            'title'       => $cls->title,
            'meeting_code' => $cls->meeting_code,
            'status'      => $cls->status,
            'join_url'    => url("/meeting/{$cls->id}/session"),
            'scheduled_at' => $cls->scheduled_at?->toIso8601String(),
        ]));
    }

    // ── PUSH TOKEN ─────────────────────────────────────────────────────────

    /**
     * POST /api/v1/mobile/push-token
     * Stores the device push notification token for the user.
     * (Stored in the user's session for now; integrate with FCM in production.)
     */
    public function registerPushToken(Request $request)
    {
        $request->validate(['token' => 'required|string|max:512']);

        // In production: store in a dedicated push_tokens table or user meta
        // For now store in cache keyed by user ID
        cache()->put("push_token:{$request->user()->id}", $request->token, now()->addDays(30));

        return response()->json(['success' => true, 'message' => 'Push token registered successfully.']);
    }
}
