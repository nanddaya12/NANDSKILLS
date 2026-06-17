<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ExamResult;
use App\Models\VirtualClassroom;
use App\Models\StudentProfile;
use App\Models\ParentStudentLink;
use App\Models\TenantSubscription;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Models\NotificationLog;
use App\Models\PushToken;
use Illuminate\Support\Facades\DB;

/**
 * Teacher Mobile API — endpoints for the Trainer mobile app.
 */
class TeacherMobileController extends Controller
{
    /** GET /api/v1/teacher/dashboard */
    public function dashboard(Request $request)
    {
        $user     = $request->user();
        $tenantId = $user->tenant_id;

        return response()->json([
            'my_courses'      => Course::where('trainer_id', $user->id)->count(),
            'total_students'  => Enrollment::whereIn('course_id',
                Course::where('trainer_id', $user->id)->pluck('id'))->distinct('user_id')->count(),
            'today_sessions'  => VirtualClassroom::where('tenant_id', $tenantId)
                ->where('trainer_id', $user->id)
                ->whereDate('scheduled_at', today())->count(),
            'pending_grading' => AssignmentSubmission::whereIn('assignment_id',
                Assignment::whereHas('lesson.chapter.course', fn($q) => $q->where('trainer_id', $user->id))
                ->pluck('id'))->where('status', 'SUBMITTED')->count(),
        ]);
    }

    /** GET /api/v1/teacher/courses */
    public function myCourses(Request $request)
    {
        $user    = $request->user();
        $courses = Course::where('trainer_id', $user->id)
            ->withCount('enrollments')
            ->latest()->get();

        return response()->json($courses->map(fn($c) => [
            'id'               => $c->id,
            'title'            => $c->title,
            'status'           => $c->status,
            'enrolled_count'   => $c->enrollments_count,
        ]));
    }

    /** GET /api/v1/teacher/attendance/{courseId} */
    public function courseAttendance(Request $request, string $courseId)
    {
        $records = Attendance::with('user:id,first_name,last_name')
            ->where('course_id', $courseId)
            ->orderByDesc('date')
            ->take(50)->get();

        return response()->json($records->map(fn($r) => [
            'student' => optional($r->user)->first_name . ' ' . optional($r->user)->last_name,
            'date'    => optional($r->date)->toDateString(),
            'status'  => $r->status,
        ]));
    }

    /** POST /api/v1/teacher/attendance */
    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'course_id'   => 'required|uuid',
            'records'     => 'required|array',
            'records.*.user_id' => 'required|uuid',
            'records.*.status'  => 'required|in:PRESENT,ABSENT,LATE,EXCUSED',
        ]);

        $user     = $request->user();
        $tenantId = $user->tenant_id;
        $count    = 0;

        foreach ($validated['records'] as $rec) {
            Attendance::updateOrCreate(
                ['user_id' => $rec['user_id'], 'course_id' => $validated['course_id'], 'date' => today()],
                ['status' => $rec['status'], 'tenant_id' => $tenantId, 'marked_by' => $user->id, 'ip_address' => $request->ip()]
            );
            $count++;
        }

        return response()->json(['success' => true, 'marked' => $count]);
    }

    /** GET /api/v1/teacher/submissions/{assignmentId} */
    public function assignmentSubmissions(Request $request, string $assignmentId)
    {
        $submissions = AssignmentSubmission::with('student:id,first_name,last_name')
            ->where('assignment_id', $assignmentId)
            ->latest()->get();

        return response()->json($submissions->map(fn($s) => [
            'id'         => $s->id,
            'student'    => optional($s->student)->first_name . ' ' . optional($s->student)->last_name,
            'status'     => $s->status,
            'score'      => $s->score,
            'submitted'  => optional($s->submitted_at)->toIso8601String(),
        ]));
    }

    /** POST /api/v1/teacher/submissions/{id}/grade */
    public function gradeSubmission(Request $request, string $id)
    {
        $validated = $request->validate([
            'score'    => 'required|numeric|min:0',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $sub = AssignmentSubmission::findOrFail($id);
        $sub->update([
            'score'    => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'status'   => 'GRADED',
            'graded_by'=> $request->user()->id,
            'graded_at'=> now(),
        ]);

        return response()->json(['success' => true, 'score' => $sub->score]);
    }

    /** POST /api/v1/teacher/push-token */
    public function registerPushToken(Request $request)
    {
        $request->validate(['token' => 'required|string|max:512', 'platform' => 'nullable|in:WEB,IOS,ANDROID']);
        PushToken::upsert($request->user()->id, $request->token, $request->platform ?? 'WEB', 'teacher');
        return response()->json(['success' => true]);
    }
}
