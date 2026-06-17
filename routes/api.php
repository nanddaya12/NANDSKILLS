<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\StudentProfile;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\ExamResult;

// ── OPENAPI V3 SPECIFICATION ROUTE ─────────────────────────────────────────
Route::get('/v1/docs', function () {
    return response()->json([
        'openapi' => '3.0.0',
        'info' => [
            'title' => 'NANDSKILLS EduOS API',
            'version' => '1.0.0',
            'description' => 'Developer OpenAPI documentation for institutional integration with the NANDSKILLS Multi-Tenant Education Operating System.',
        ],
        'servers' => [
            ['url' => url('/api')],
        ],
        'paths' => [
            '/v1/students' => [
                'get' => [
                    'summary' => 'Get student directory',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Successful operation'],
                        '401' => ['description' => 'Unauthorized'],
                    ],
                ],
            ],
            '/v1/courses' => [
                'get' => [
                    'summary' => 'Get course catalog',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Successful operation'],
                    ],
                ],
            ],
            '/v1/attendance' => [
                'get' => [
                    'summary' => 'Get attendance worksheets',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Successful operation'],
                    ],
                ],
            ],
            '/v1/fees' => [
                'get' => [
                    'summary' => 'Get outstanding invoice logs',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Successful operation'],
                    ],
                ],
            ],
            '/v1/exams' => [
                'get' => [
                    'summary' => 'Get student exam results',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Successful operation'],
                    ],
                ],
            ],
        ],
        'components' => [
            'securitySchemes' => [
                'sanctum' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                ],
            ],
        ],
    ]);
});

// ── PROTECTED MULTI-TENANT API V1 ENDPOINTS ────────────────────────────────
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // 1. Student Directory Scoped Route
    Route::get('/students', function () {
        return response()->json(
            StudentProfile::with('user')->get()->map(fn($student) => [
                'id' => $student->id,
                'roll_number' => $student->roll_number,
                'name' => $student->user->first_name . ' ' . $student->user->last_name,
                'email' => $student->user->email,
                'status' => $student->user->status,
            ])
        );
    });

    // 2. Course Catalog Scoped Route
    Route::get('/courses', function () {
        return response()->json(
            Course::all()->map(fn($course) => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'price' => $course->price,
                'status' => $course->status,
            ])
        );
    });

    // 3. Attendance Scoped Route
    Route::get('/attendance', function () {
        return response()->json(
            Attendance::with('user')->latest()->get()->map(fn($att) => [
                'id' => $att->id,
                'date' => $att->date,
                'status' => $att->status,
                'student_name' => $att->user ? $att->user->first_name . ' ' . $att->user->last_name : 'N/A',
                'ip_address' => $att->ip_address,
            ])
        );
    });

    // 4. Fees / Invoices Scoped Route
    Route::get('/fees', function () {
        return response()->json(
            Invoice::with('studentFee.student.user')->latest()->get()->map(fn($inv) => [
                'id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'total' => $inv->total,
                'status' => $inv->status,
                'due_date' => $inv->due_date,
                'student_name' => $inv->studentFee->student->user->first_name . ' ' . $inv->studentFee->student->user->last_name,
            ])
        );
    });

    // 5. Exams Scoped Route
    Route::get('/exams', function () {
        return response()->json(
            ExamResult::with(['exam.course', 'user'])->latest()->get()->map(fn($res) => [
                'id'            => $res->id,
                'course'        => $res->exam->course->title ?? 'N/A',
                'student_name'  => $res->user->first_name . ' ' . $res->user->last_name,
                'marks_obtained'=> $res->marks_obtained,
                'grade'         => $res->grade,
                'status'        => $res->status,
            ])
        );
    });
});

// ── MOBILE APP API GATEWAY (v1/mobile) ─────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('v1/mobile')->group(function () {
    Route::get('/profile',                  [App\Http\Controllers\Api\MobileApiController::class, 'profile']);
    Route::get('/dashboard',                [App\Http\Controllers\Api\MobileApiController::class, 'dashboard']);
    Route::get('/courses',                  [App\Http\Controllers\Api\MobileApiController::class, 'myCourses']);
    Route::get('/attendance',               [App\Http\Controllers\Api\MobileApiController::class, 'myAttendance']);
    Route::get('/assignments',              [App\Http\Controllers\Api\MobileApiController::class, 'myAssignments']);
    Route::get('/results',                  [App\Http\Controllers\Api\MobileApiController::class, 'myResults']);
    Route::get('/messages',                 [App\Http\Controllers\Api\MobileApiController::class, 'myMessages']);
    Route::post('/messages/{id}/read',      [App\Http\Controllers\Api\MobileApiController::class, 'markMessageRead']);
    Route::get('/certificates',             [App\Http\Controllers\Api\MobileApiController::class, 'myCertificates']);
    Route::get('/announcements',            [App\Http\Controllers\Api\MobileApiController::class, 'announcements']);
    Route::get('/classes',                  [App\Http\Controllers\Api\MobileApiController::class, 'myClasses']);
    Route::post('/push-token',              [App\Http\Controllers\Api\MobileApiController::class, 'registerPushToken']);
});

// ── TEACHER MOBILE API (v1/teacher) ────────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('v1/teacher')->group(function () {
    Route::get('/dashboard',                    [App\Http\Controllers\Api\TeacherMobileController::class, 'dashboard']);
    Route::get('/courses',                      [App\Http\Controllers\Api\TeacherMobileController::class, 'myCourses']);
    Route::get('/attendance/{courseId}',        [App\Http\Controllers\Api\TeacherMobileController::class, 'courseAttendance']);
    Route::post('/attendance',                  [App\Http\Controllers\Api\TeacherMobileController::class, 'markAttendance']);
    Route::get('/submissions/{assignmentId}',   [App\Http\Controllers\Api\TeacherMobileController::class, 'assignmentSubmissions']);
    Route::post('/submissions/{id}/grade',      [App\Http\Controllers\Api\TeacherMobileController::class, 'gradeSubmission']);
    Route::post('/push-token',                  [App\Http\Controllers\Api\TeacherMobileController::class, 'registerPushToken']);
});

// ── PARENT MOBILE API (v1/parent) ──────────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('v1/parent')->group(function () {
    Route::get('/children',                             [App\Http\Controllers\Api\ParentMobileController::class, 'myChildren']);
    Route::get('/children/{studentId}/dashboard',       [App\Http\Controllers\Api\ParentMobileController::class, 'childDashboard']);
    Route::get('/children/{studentId}/attendance',      [App\Http\Controllers\Api\ParentMobileController::class, 'childAttendance']);
    Route::get('/children/{studentId}/results',         [App\Http\Controllers\Api\ParentMobileController::class, 'childResults']);
    Route::get('/messages',                             [App\Http\Controllers\Api\ParentMobileController::class, 'messages']);
    Route::post('/push-token',                          [App\Http\Controllers\Api\ParentMobileController::class, 'registerPushToken']);
});

// ── ADMIN MOBILE API (v1/admin) ────────────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('v1/admin')->group(function () {
    Route::get('/dashboard',                [App\Http\Controllers\Api\AdminMobileController::class, 'dashboard']);
    Route::get('/users',                    [App\Http\Controllers\Api\AdminMobileController::class, 'users']);
    Route::post('/users/{id}/toggle-active',[App\Http\Controllers\Api\AdminMobileController::class, 'toggleUserActive']);
    Route::get('/revenue',                  [App\Http\Controllers\Api\AdminMobileController::class, 'revenue']);
    Route::post('/notify',                  [App\Http\Controllers\Api\AdminMobileController::class, 'sendNotification']);
    Route::post('/push-token',              [App\Http\Controllers\Api\AdminMobileController::class, 'registerPushToken']);
});
