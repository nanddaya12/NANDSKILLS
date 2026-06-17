<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParentStudentLink;
use App\Models\StudentProfile;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\Invoice;
use App\Models\ParentMessage;
use App\Models\AcademicWarning;
use App\Models\PushToken;

/**
 * Parent Mobile API — endpoints for the Parent mobile app.
 */
class ParentMobileController extends Controller
{
    /** GET /api/v1/parent/children */
    public function myChildren(Request $request)
    {
        $user  = $request->user();
        $links = ParentStudentLink::with('student.user:id,first_name,last_name,email')
            ->where('parent_user_id', $user->id)
            ->where('is_active', true)
            ->get();

        return response()->json($links->map(fn($l) => [
            'link_id'         => $l->id,
            'student_id'      => $l->student_id,
            'student_name'    => optional($l->student?->user)->first_name . ' ' . optional($l->student?->user)->last_name,
            'relationship'    => $l->relationship,
            'can_view_grades' => $l->can_view_grades,
            'can_view_fees'   => $l->can_view_fees,
        ]));
    }

    /** GET /api/v1/parent/children/{studentId}/dashboard */
    public function childDashboard(Request $request, string $studentId)
    {
        $user = $request->user();

        // Authorization: ensure this parent is linked to this student
        $link = ParentStudentLink::where('parent_user_id', $user->id)
            ->where('student_id', $studentId)
            ->where('is_active', true)
            ->firstOrFail();

        $student   = StudentProfile::findOrFail($studentId);
        $attendPct = Attendance::studentAttendancePercent($student->user_id ?? null);

        return response()->json([
            'student_id'     => $student->id,
            'attendance_pct' => $attendPct,
            'at_risk'        => $attendPct < 75,
            'exam_count'     => $link->can_view_grades ? ExamResult::where('user_id', $student->user_id)->count() : null,
            'pending_fees'   => $link->can_view_fees ? Invoice::where('tenant_id', $student->tenant_id)->where('status', 'UNPAID')->count() : null,
            'warnings'       => AcademicWarning::where('student_id', $studentId)->where('is_resolved', false)->count(),
        ]);
    }

    /** GET /api/v1/parent/children/{studentId}/attendance */
    public function childAttendance(Request $request, string $studentId)
    {
        $user = $request->user();
        $link = ParentStudentLink::where('parent_user_id', $user->id)
            ->where('student_id', $studentId)->where('is_active', true)->firstOrFail();

        $student = StudentProfile::findOrFail($studentId);
        $records = Attendance::where('user_id', $student->user_id)->orderByDesc('date')->take(30)->get();

        return response()->json($records->map(fn($r) => [
            'date'   => optional($r->date)->toDateString(),
            'status' => $r->status,
        ]));
    }

    /** GET /api/v1/parent/children/{studentId}/results */
    public function childResults(Request $request, string $studentId)
    {
        $user = $request->user();
        $link = ParentStudentLink::where('parent_user_id', $user->id)
            ->where('student_id', $studentId)
            ->where('is_active', true)
            ->where('can_view_grades', true)
            ->firstOrFail();

        $student = StudentProfile::findOrFail($studentId);
        $results = ExamResult::with('exam.course')->where('user_id', $student->user_id)->latest()->take(20)->get();

        return response()->json($results->map(fn($r) => [
            'exam'    => $r->exam?->title,
            'course'  => $r->exam?->course?->title,
            'marks'   => $r->marks_obtained,
            'grade'   => $r->grade,
            'status'  => $r->status,
        ]));
    }

    /** GET /api/v1/parent/messages */
    public function messages(Request $request)
    {
        $user = $request->user();

        $msgs = ParentMessage::with('sender:id,first_name,last_name')
            ->where('parent_user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(20)->get();

        return response()->json($msgs->map(fn($m) => [
            'id'         => $m->id,
            'subject'    => $m->subject,
            'content'    => $m->content,
            'is_read'    => $m->is_read,
            'sender'     => optional($m->sender)->first_name . ' ' . optional($m->sender)->last_name,
            'sent_at'    => optional($m->created_at)->toIso8601String(),
        ]));
    }

    /** POST /api/v1/parent/push-token */
    public function registerPushToken(Request $request)
    {
        $request->validate(['token' => 'required|string|max:512', 'platform' => 'nullable|in:WEB,IOS,ANDROID']);
        PushToken::upsert($request->user()->id, $request->token, $request->platform ?? 'WEB', 'parent');
        return response()->json(['success' => true]);
    }
}
