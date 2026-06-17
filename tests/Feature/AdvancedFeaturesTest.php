<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TenantPlan;
use App\Models\Tenant;
use App\Models\Course;
use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use App\Models\StudentProfile;
use App\Models\VirtualClassroom;
use App\Models\Attendance;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private TenantPlan $plan;
    private Tenant $tenant1;
    private Tenant $tenant2;
    private User $trainer1;
    private User $student1;
    private Course $course1;
    private Branch $branch1;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard plan
        $this->plan = TenantPlan::create([
            'name' => 'Enterprise Plan',
            'price' => 299.00,
            'billing_interval' => 'monthly',
            'max_users' => 1000,
            'max_courses' => 100,
            'max_storage_bytes' => 53687091200,
            'features' => ['lms', 'cms', 'crm', 'sis', 'finance', 'helpdesk']
        ]);

        // Create Tenant 1
        $this->tenant1 = Tenant::create([
            'name' => 'Academy One',
            'subdomain' => 'academy1',
            'status' => 'ACTIVE',
            'plan_id' => $this->plan->id,
        ]);

        // Create Tenant 2
        $this->tenant2 = Tenant::create([
            'name' => 'Academy Two',
            'subdomain' => 'academy2',
            'status' => 'ACTIVE',
            'plan_id' => $this->plan->id,
        ]);

        // Set context to Tenant 1
        app()->instance('currentTenant', $this->tenant1);

        // Roles
        $trainerRole = Role::create(['tenant_id' => $this->tenant1->id, 'name' => 'Trainer']);
        $studentRole = Role::create(['tenant_id' => $this->tenant1->id, 'name' => 'Student']);

        // Users
        $this->trainer1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'email' => 'trainer@academy1.com',
            'password_hash' => 'dummy',
            'first_name' => 'Trainer',
            'last_name' => 'One',
            'status' => 'ACTIVE',
        ]);
        $this->trainer1->roles()->attach($trainerRole->id);

        $this->student1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'email' => 'student@academy1.com',
            'password_hash' => 'dummy',
            'first_name' => 'Student',
            'last_name' => 'One',
            'status' => 'ACTIVE',
        ]);
        $this->student1->roles()->attach($studentRole->id);

        // Branch
        $this->branch1 = Branch::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'Main Branch',
            'status' => 'ACTIVE',
        ]);

        // Student Profile
        StudentProfile::create([
            'user_id' => $this->student1->id,
            'tenant_id' => $this->tenant1->id,
            'roll_number' => 'ROLL-001',
            'admission_date' => today(),
            'status' => 'ACTIVE',
        ]);

        // Course
        $this->course1 = Course::create([
            'tenant_id' => $this->tenant1->id,
            'title' => 'Web Development',
            'slug' => 'web-dev',
            'description' => 'Course description.',
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_virtual_classrooms_belong_to_tenants()
    {
        // 1. Create a meeting under Tenant 1 context
        app()->instance('currentTenant', $this->tenant1);
        $meeting1 = VirtualClassroom::create([
            'tenant_id' => $this->tenant1->id,
            'course_id' => $this->course1->id,
            'trainer_id' => $this->trainer1->id,
            'topic' => 'Intro to Programming',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 60,
            'meeting_id' => 'nandskills_meet_abc123',
            'status' => 'UPCOMING',
        ]);

        $this->assertEquals($this->tenant1->id, $meeting1->tenant_id);

        // 2. Query under Tenant 2 context (should not return the meeting)
        app()->instance('currentTenant', $this->tenant2);
        $this->assertCount(0, VirtualClassroom::all());

        // 3. Query under Tenant 1 context (should return the meeting)
        app()->instance('currentTenant', $this->tenant1);
        $this->assertCount(1, VirtualClassroom::all());
    }

    public function test_self_service_attendance_checkin_success()
    {
        app()->instance('currentTenant', $this->tenant1);
        $this->actingAs($this->student1);

        $code = 'ATT999';
        $cacheKey = 'active_checkin_code_' . $code;

        // Store active code in cache
        Cache::put($cacheKey, [
            'tenant_id' => $this->tenant1->id,
            'branch_id' => $this->branch1->id,
            'date' => today()->toDateString(),
            'marked_by_id' => $this->trainer1->id,
        ], 1800);

        // Simulate student submitting the code
        $livewire = \Livewire\Livewire::test(\App\Livewire\Sis\AttendanceCheckIn::class)
            ->set('code', $code)
            ->call('submitCheckIn');

        $livewire->assertHasNoErrors();
        $livewire->assertSee('Check-in successful! Your attendance has been logged.');

        // Verify attendance record exists
        $attendance = Attendance::where('user_id', $this->student1->id)
            ->where('branch_id', $this->branch1->id)
            ->first();

        $this->assertNotNull($attendance);
        $this->assertEquals('PRESENT', $attendance->status);
        $this->assertEquals($code, $attendance->qr_code);
    }

    public function test_self_service_attendance_checkin_fails_for_other_tenant()
    {
        // Set context to Tenant 1 but cache code belongs to Tenant 2
        app()->instance('currentTenant', $this->tenant1);
        $this->actingAs($this->student1);

        $code = 'ATT888';
        $cacheKey = 'active_checkin_code_' . $code;

        // Code registered under Tenant 2
        Cache::put($cacheKey, [
            'tenant_id' => $this->tenant2->id,
            'branch_id' => $this->branch1->id,
            'date' => today()->toDateString(),
            'marked_by_id' => $this->trainer1->id,
        ], 1800);

        $livewire = \Livewire\Livewire::test(\App\Livewire\Sis\AttendanceCheckIn::class)
            ->set('code', $code)
            ->call('submitCheckIn');

        $livewire->assertSee('The check-in code belongs to a different academy instance.');

        $this->assertCount(0, Attendance::all());
    }

    public function test_self_service_attendance_checkin_fails_with_expired_code()
    {
        app()->instance('currentTenant', $this->tenant1);
        $this->actingAs($this->student1);

        $livewire = \Livewire\Livewire::test(\App\Livewire\Sis\AttendanceCheckIn::class)
            ->set('code', 'EXPIRE')
            ->call('submitCheckIn');

        $livewire->assertSee('The check-in code is invalid, incorrect, or has expired.');
    }

    public function test_custom_tenant_roles_are_isolated()
    {
        // 1. Create a custom role in Tenant 1
        app()->instance('currentTenant', $this->tenant1);
        $role1 = Role::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'Custom Agent One',
            'description' => 'Test',
            'is_system' => false,
        ]);

        $this->assertEquals($this->tenant1->id, $role1->tenant_id);

        // 2. Query under Tenant 2 context (should not return the role)
        app()->instance('currentTenant', $this->tenant2);
        $this->assertNull(Role::where('name', 'Custom Agent One')->first());

        // 3. Query under Tenant 1 context (should return the role)
        app()->instance('currentTenant', $this->tenant1);
        $this->assertNotNull(Role::where('name', 'Custom Agent One')->first());
    }

    public function test_exams_are_isolated()
    {
        app()->instance('currentTenant', $this->tenant1);
        $sess = \App\Models\ExamSession::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'Session 1',
        ]);
        $exam = \App\Models\Exam::create([
            'tenant_id' => $this->tenant1->id,
            'exam_session_id' => $sess->id,
            'course_id' => $this->course1->id,
            'title' => 'Midterm 1',
            'type' => 'THEORY',
            'total_marks' => 100,
        ]);

        $this->assertEquals($this->tenant1->id, $exam->tenant_id);

        // Scope test
        app()->instance('currentTenant', $this->tenant2);
        $this->assertCount(0, \App\Models\Exam::all());

        app()->instance('currentTenant', $this->tenant1);
        $this->assertCount(1, \App\Models\Exam::all());
    }

    public function test_workflows_are_isolated()
    {
        app()->instance('currentTenant', $this->tenant1);
        $wf = \App\Models\AutomationWorkflow::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'Workflow 1',
            'trigger_event' => 'STUDENT_REGISTERED',
            'is_active' => true,
        ]);

        $this->assertEquals($this->tenant1->id, $wf->tenant_id);

        // Scope test
        app()->instance('currentTenant', $this->tenant2);
        $this->assertCount(0, \App\Models\AutomationWorkflow::all());

        app()->instance('currentTenant', $this->tenant1);
        $this->assertCount(1, \App\Models\AutomationWorkflow::all());
    }

    public function test_enterprise_console_operations()
    {
        app()->instance('currentTenant', $this->tenant1);

        // Create a Tenant Admin role and attach it to an admin user.
        $adminRole = Role::create(['tenant_id' => $this->tenant1->id, 'name' => 'Tenant Admin']);
        $adminUser = User::create([
            'tenant_id' => $this->tenant1->id,
            'email' => 'admin@academy1.com',
            'password_hash' => 'dummy',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'status' => 'ACTIVE',
        ]);
        $adminUser->roles()->attach($adminRole->id);
        $this->actingAs($adminUser);

        // Test 1: Campus Building and Department Creation
        $livewire = \Livewire\Livewire::test(\App\Livewire\Admin\EnterpriseConsole::class)
            ->set('selectedBranch', $this->branch1->id)
            ->set('newBuildingName', 'Science Block')
            ->set('newBuildingDesc', 'Chemistry Labs')
            ->call('createBuilding')
            ->assertHasNoErrors()
            ->assertSee('Building created successfully!');

        $this->assertDatabaseHas('buildings', [
            'tenant_id' => $this->tenant1->id,
            'name' => 'Science Block',
        ]);

        $livewire->set('selectedBranch', $this->branch1->id)
            ->set('newDepartmentName', 'Physics Dept')
            ->set('newDepartmentDesc', 'Physics Labs')
            ->call('createDepartment')
            ->assertHasNoErrors()
            ->assertSee('Department created successfully!');

        $this->assertDatabaseHas('departments', [
            'tenant_id' => $this->tenant1->id,
            'name' => 'Physics Dept',
        ]);

        // Test 2: Exam Result submission with Grading
        $sess = \App\Models\ExamSession::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'Final Term 2026',
        ]);
        $exam = \App\Models\Exam::create([
            'tenant_id' => $this->tenant1->id,
            'exam_session_id' => $sess->id,
            'course_id' => $this->course1->id,
            'title' => 'Advanced Math',
            'type' => 'THEORY',
            'total_marks' => 100,
        ]);

        // Submit Grade
        $livewire->set('selectedExamForResult', $exam->id)
            ->set('selectedStudentForResult', $this->student1->id)
            ->set('obtainedMarks', 90)
            ->call('submitExamResult')
            ->assertHasNoErrors()
            ->assertSee('Exam result recorded and graded successfully!');

        $this->assertDatabaseHas('exam_results', [
            'tenant_id' => $this->tenant1->id,
            'exam_id' => $exam->id,
            'user_id' => $this->student1->id,
            'marks_obtained' => 90,
            'grade' => 'A',
            'status' => 'PASSED',
        ]);

        // Test 3: Leave Request filing and Approval
        $dept = \App\Models\Department::where('name', 'Physics Dept')->first();
        $employee = \App\Models\Employee::create([
            'tenant_id' => $this->tenant1->id,
            'user_id' => $this->trainer1->id,
            'department_id' => $dept->id,
            'designation' => 'Lecturer',
            'salary' => 5000.00,
            'status' => 'ACTIVE',
            'hire_date' => today(),
        ]);

        $livewire->set('selectedEmployeeForLeave', $employee->id)
            ->set('leaveType', 'SICK')
            ->set('leaveStartDate', '2026-06-12')
            ->set('leaveEndDate', '2026-06-14')
            ->call('submitLeaveRequest')
            ->assertHasNoErrors()
            ->assertSee('Leave request submitted successfully.');

        $leaveRequest = \App\Models\LeaveRequest::where('employee_id', $employee->id)->first();
        $this->assertNotNull($leaveRequest);
        $this->assertEquals('PENDING', $leaveRequest->status);

        $livewire->call('approveLeaveRequest', $leaveRequest->id)
            ->assertSee('Leave request approved.');

        $this->assertEquals('APPROVED', $leaveRequest->fresh()->status);

        // Test 4: Assets Checkout
        $asset = \App\Models\Asset::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'MacBook Pro',
            'serial_number' => 'MBP123',
            'status' => 'AVAILABLE',
        ]);

        $livewire->set('selectedAssetForAssign', $asset->id)
            ->set('selectedUserForAsset', $this->student1->id)
            ->call('assignAsset')
            ->assertHasNoErrors()
            ->assertSee('Asset checked out successfully.');

        $this->assertEquals('ASSIGNED', $asset->fresh()->status);
        $this->assertDatabaseHas('asset_assignments', [
            'tenant_id' => $this->tenant1->id,
            'asset_id' => $asset->id,
            'user_id' => $this->student1->id,
        ]);

        // Test 5: Library book loan and return
        $book = \App\Models\Book::create([
            'tenant_id' => $this->tenant1->id,
            'title' => 'Laravel Up and Running',
            'author' => 'Matt Stauffer',
            'isbn' => '978-1491936085',
            'total_copies' => 3,
            'available_copies' => 3,
        ]);

        $livewire->set('selectedBookForLoan', $book->id)
            ->set('selectedUserForLoan', $this->student1->id)
            ->set('loanDueDate', '2026-06-20')
            ->call('issueBookLoan')
            ->assertHasNoErrors()
            ->assertSee('Book issued successfully.');

        $this->assertEquals(2, $book->fresh()->available_copies);
        $loan = \App\Models\BookLoan::where('book_id', $book->id)->first();
        $this->assertNotNull($loan);

        $livewire->call('returnBookLoan', $loan->id)
            ->assertSee('Book marked as returned.');

        $this->assertEquals(3, $book->fresh()->available_copies);
        $this->assertNotNull($loan->fresh()->returned_at);
    }
}
