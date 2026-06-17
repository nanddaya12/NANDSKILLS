<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\AccessRequest;
use App\Models\LoginHistory;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Building;
use App\Models\Room;
use App\Models\ExamSession;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\ExamResult;
use App\Models\GradeScale;
use App\Models\Transcript;
use App\Models\AssignmentGroup;
use App\Models\Rubric;
use App\Models\PeerReview;
use App\Models\CertificateTemplate;
use App\Models\CertificateVerification;
use App\Models\SubscriptionUsage;
use App\Models\TenantInvoice;
use App\Models\AutomationWorkflow;
use App\Models\WorkflowAction;
use App\Models\WorkflowLog;
use App\Models\CustomForm;
use App\Models\FormField;
use App\Models\FormResponse;
use App\Models\ReportTemplate;
use App\Models\Competency;
use App\Models\StudentCompetency;
use App\Models\Badge;
use App\Models\UserPoint;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use App\Models\ForumReaction;
use App\Models\ContentLibrary;
use App\Models\ContentVersion;
use App\Models\WebsiteSection;
use App\Models\WebsiteWidget;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\DocumentApproval;
use App\Models\NotificationTemplate;
use App\Models\ApiKey;
use App\Models\ApiLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\AssetAssignment;
use App\Models\AssetMaintenance;
use App\Models\Book;
use App\Models\BookLoan;
use App\Models\BookReservation;
use App\Models\Accreditation;
use App\Models\AuditRecord;
use App\Models\ComplianceItem;
use App\Models\User;
use App\Models\Course;
use App\Models\StudentProfile;
use App\Models\Asset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

use Livewire\Attributes\Url;

class EnterpriseConsole extends Component
{
    #[Url]
    public string $activeTab = 'rbac';

    // Module Form States
    // 1. RBAC
    public string $newRoleName = '';
    public string $newRoleDescription = '';
    
    // 2. Campus
    public string $selectedBranch = '';
    public string $selectedBuilding = '';
    public string $newRoomName = '';
    public int $newRoomCapacity = 30;
    public string $newBuildingName = '';
    public string $newBuildingDesc = '';
    public string $newDepartmentName = '';
    public string $newDepartmentDesc = '';

    // 3. Exams
    public string $selectedCourse = '';
    public string $selectedExamSession = '';
    public string $newExamTitle = '';
    public string $newExamType = 'THEORY'; // THEORY, PRACTICAL
    public int $newExamMarks = 100;
    public string $selectedStudentForResult = '';
    public string $selectedExamForResult = '';
    public float $obtainedMarks = 0.00;

    // 7. Workflow
    public string $newWorkflowName = '';
    public string $newWorkflowTrigger = 'STUDENT_REGISTERED';

    // 8. Custom Forms
    public string $newFormTitle = '';
    public string $newFormDesc = '';

    // 11. Gamification
    public string $selectedUserForPoints = '';
    public int $pointsToAward = 10;
    public string $pointsReason = 'Active Forum Participation';
    public string $newBadgeName = '';
    public string $newBadgeDesc = '';
    public int $newBadgeXp = 100;

    // 19. HR
    public string $selectedEmployeeForPayroll = '';
    public float $payrollAmount = 3000.00;
    public string $selectedEmployeeForLeave = '';
    public string $leaveType = 'CASUAL'; // CASUAL, SICK, ANNUAL
    public string $leaveStartDate = '';
    public string $leaveEndDate = '';

    // 20. Assets
    public string $newAssetName = '';
    public string $newAssetSerial = '';
    public string $selectedAssetForAssign = '';
    public string $selectedUserForAsset = '';

    // 21. Library
    public string $newBookTitle = '';
    public string $newBookAuthor = '';
    public string $newBookIsbn = '';
    public string $selectedBookForLoan = '';
    public string $selectedUserForLoan = '';
    public string $loanDueDate = '';

    // API Tokens
    public string $newApiKeyName = '';
    public int $newApiKeyRateLimit = 60;
    public string $generatedToken = '';

    // 22. Accreditations
    public string $newAccreditationFramework = '';
    public string $newAccreditationAgency = '';
    public string $selectedAccreditationForCompliance = '';
    public string $newComplianceItemName = '';
    public string $newComplianceItemDesc = '';

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized action. Enterprise Console is restricted to Administrators.');
        }

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId) {
            $firstBranch = Branch::first();
            if ($firstBranch) {
                $this->selectedBranch = $firstBranch->id;
            }
            $firstSession = ExamSession::first();
            if ($firstSession) {
                $this->selectedExamSession = $firstSession->id;
            }
            $firstCourse = Course::first();
            if ($firstCourse) {
                $this->selectedCourse = $firstCourse->id;
            }
        }
    }

    // Tab switcher
    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    // Tab 1: RBAC Actions
    public function createCustomRole()
    {
        $this->validate([
            'newRoleName' => 'required|string|max:100',
            'newRoleDescription' => 'nullable|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        Role::create([
            'tenant_id' => $tenantId,
            'name' => $this->newRoleName,
            'description' => $this->newRoleDescription,
            'is_system' => false,
        ]);

        $this->newRoleName = '';
        $this->newRoleDescription = '';
        session()->flash('status', 'Custom tenant role created successfully!');
    }

    public function approveAccessRequest(string $requestId)
    {
        $request = AccessRequest::find($requestId);
        if ($request) {
            $request->update([
                'status' => 'APPROVED',
                'resolved_by' => auth()->id(),
            ]);
            session()->flash('status', 'Access request approved.');
        }
    }

    // Tab 2: Campus Actions
    public function createRoom()
    {
        $this->validate([
            'selectedBuilding' => 'required|exists:buildings,id',
            'newRoomName' => 'required|string|max:100',
            'newRoomCapacity' => 'required|integer|min:1',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        Room::create([
            'tenant_id' => $tenantId,
            'building_id' => $this->selectedBuilding,
            'name' => $this->newRoomName,
            'capacity' => $this->newRoomCapacity,
        ]);

        $this->newRoomName = '';
        $this->newRoomCapacity = 30;
        session()->flash('status', 'Classroom room added successfully.');
    }

    // Tab 3: Exams Actions
    public function createExam()
    {
        $this->validate([
            'selectedExamSession' => 'required|exists:exam_sessions,id',
            'selectedCourse' => 'required|exists:courses,id',
            'newExamTitle' => 'required|string|max:200',
            'newExamType' => 'required|string',
            'newExamMarks' => 'required|integer|min:1',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        Exam::create([
            'tenant_id' => $tenantId,
            'exam_session_id' => $this->selectedExamSession,
            'course_id' => $this->selectedCourse,
            'title' => $this->newExamTitle,
            'type' => $this->newExamType,
            'total_marks' => $this->newExamMarks,
        ]);

        $this->newExamTitle = '';
        session()->flash('status', 'Examination schedule created successfully.');
    }

    // Tab 7: Workflow Actions
    public function createWorkflow()
    {
        $this->validate([
            'newWorkflowName' => 'required|string|max:100',
            'newWorkflowTrigger' => 'required|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        $workflow = AutomationWorkflow::create([
            'tenant_id' => $tenantId,
            'name' => $this->newWorkflowName,
            'trigger_event' => $this->newWorkflowTrigger,
            'is_active' => true,
        ]);

        // Add default action
        WorkflowAction::create([
            'tenant_id' => $tenantId,
            'workflow_id' => $workflow->id,
            'action_type' => 'SEND_EMAIL',
            'config' => json_encode(['template' => 'Default Template']),
            'order_index' => 1,
        ]);

        $this->newWorkflowName = '';
        session()->flash('status', 'Automation workflow trigger saved successfully.');
    }

    public function triggerWorkflow(string $workflowId)
    {
        $workflow = AutomationWorkflow::find($workflowId);
        if ($workflow) {
            $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
            
            // Log executing action
            WorkflowLog::create([
                'tenant_id' => $tenantId,
                'workflow_id' => $workflow->id,
                'event_data' => json_encode(['triggered_by' => auth()->user()->email]),
                'status' => 'SUCCESS',
                'executed_at' => now(),
            ]);

            session()->flash('status', 'Workflow "' . $workflow->name . '" triggered and processed successfully.');
        }
    }

    // Tab 8: Form Builder
    public function createForm()
    {
        $this->validate([
            'newFormTitle' => 'required|string|max:200',
            'newFormDesc' => 'nullable|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        $form = CustomForm::create([
            'tenant_id' => $tenantId,
            'title' => $this->newFormTitle,
            'description' => $this->newFormDesc,
            'is_public' => true,
        ]);

        // Seed basic fields
        FormField::create([
            'tenant_id' => $tenantId,
            'form_id' => $form->id,
            'label' => 'Full Name',
            'type' => 'TEXT',
            'name' => 'full_name',
            'is_required' => true,
            'order_index' => 1,
        ]);

        FormField::create([
            'tenant_id' => $tenantId,
            'form_id' => $form->id,
            'label' => 'Email Address',
            'type' => 'TEXT',
            'name' => 'email',
            'is_required' => true,
            'order_index' => 2,
        ]);

        $this->newFormTitle = '';
        $this->newFormDesc = '';
        session()->flash('status', 'Dynamic builder form saved with default input layouts.');
    }

    // Tab 11: Gamification Actions
    public function awardPoints()
    {
        $this->validate([
            'selectedUserForPoints' => 'required|exists:users,id',
            'pointsToAward' => 'required|integer|min:1',
            'pointsReason' => 'required|string|max:200',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        UserPoint::create([
            'tenant_id' => $tenantId,
            'user_id' => $this->selectedUserForPoints,
            'points' => $this->pointsToAward,
            'reason' => $this->pointsReason,
        ]);

        session()->flash('status', 'Awarded ' . $this->pointsToAward . ' XP points successfully.');
    }

    // Tab 19: HR Payroll Actions
    public function runEmployeePayroll()
    {
        $this->validate([
            'selectedEmployeeForPayroll' => 'required|exists:employees,id',
            'payrollAmount' => 'required|numeric|min:0',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        Payroll::create([
            'tenant_id' => $tenantId,
            'employee_id' => $this->selectedEmployeeForPayroll,
            'amount' => $this->payrollAmount,
            'bonus' => 0.00,
            'deductions' => 0.00,
            'status' => 'PAID',
            'paid_at' => now(),
        ]);

        session()->flash('status', 'Payroll processed and marked as PAID.');
    }

    // Tab 20: Asset Actions
    public function createAsset()
    {
        $this->validate([
            'newAssetName' => 'required|string|max:100',
            'newAssetSerial' => 'nullable|string|max:100',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        Asset::create([
            'tenant_id' => $tenantId,
            'name' => $this->newAssetName,
            'serial_number' => $this->newAssetSerial,
            'status' => 'AVAILABLE',
        ]);

        $this->newAssetName = '';
        $this->newAssetSerial = '';
        session()->flash('status', 'Asset registered successfully.');
    }

    // Tab 21: Library Actions
    public function createBook()
    {
        $this->validate([
            'newBookTitle' => 'required|string|max:200',
            'newBookAuthor' => 'required|string|max:100',
            'newBookIsbn' => 'nullable|string|max:50',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        Book::create([
            'tenant_id' => $tenantId,
            'title' => $this->newBookTitle,
            'author' => $this->newBookAuthor,
            'isbn' => $this->newBookIsbn,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);

        $this->newBookTitle = '';
        $this->newBookAuthor = '';
        $this->newBookIsbn = '';
        session()->flash('status', 'Book created inside library inventory.');
    }

    // Tab 22: Accreditation
    public function createAccreditation()
    {
        $this->validate([
            'newAccreditationFramework' => 'required|string|max:200',
            'newAccreditationAgency' => 'required|string|max:100',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        $acc = Accreditation::create([
            'tenant_id' => $tenantId,
            'framework_name' => $this->newAccreditationFramework,
            'agency' => $this->newAccreditationAgency,
            'status' => 'PENDING',
        ]);

        // Add compliance checklist item
        ComplianceItem::create([
            'tenant_id' => $tenantId,
            'accreditation_id' => $acc->id,
            'name' => 'Initial Documentation Audit',
            'description' => 'Submission of academic program outline.',
            'is_met' => false,
        ]);

        $this->newAccreditationFramework = '';
        $this->newAccreditationAgency = '';
        session()->flash('status', 'Accreditation compliance audit framework initialized.');
    }

    public function checkCompliance(string $complianceId)
    {
        $item = ComplianceItem::find($complianceId);
        if ($item) {
            $item->update(['is_met' => !$item->is_met]);
            session()->flash('status', 'Compliance criteria toggle updated.');
        }
    }

    public function createBuilding()
    {
        $this->validate([
            'selectedBranch' => 'required|exists:branches,id',
            'newBuildingName' => 'required|string|max:100',
            'newBuildingDesc' => 'nullable|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        Building::create([
            'tenant_id' => $tenantId,
            'branch_id' => $this->selectedBranch,
            'name' => $this->newBuildingName,
            'description' => $this->newBuildingDesc,
        ]);

        $this->newBuildingName = '';
        $this->newBuildingDesc = '';
        session()->flash('status', 'Building created successfully!');
    }

    public function createDepartment()
    {
        $this->validate([
            'selectedBranch' => 'required|exists:branches,id',
            'newDepartmentName' => 'required|string|max:100',
            'newDepartmentDesc' => 'nullable|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        Department::create([
            'tenant_id' => $tenantId,
            'branch_id' => $this->selectedBranch,
            'name' => $this->newDepartmentName,
            'description' => $this->newDepartmentDesc,
        ]);

        $this->newDepartmentName = '';
        $this->newDepartmentDesc = '';
        session()->flash('status', 'Department created successfully!');
    }

    public function submitExamResult()
    {
        $this->validate([
            'selectedStudentForResult' => 'required|exists:users,id',
            'selectedExamForResult' => 'required|exists:exams,id',
            'obtainedMarks' => 'required|numeric|min:0',
        ]);

        $exam = Exam::find($this->selectedExamForResult);
        if ($this->obtainedMarks > $exam->total_marks) {
            session()->flash('error', 'Obtained marks cannot exceed total marks (' . $exam->total_marks . ').');
            return;
        }

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        // Calculate pass/fail status
        $passingMarks = $exam->total_marks * 0.5; // default 50%
        $status = $this->obtainedMarks >= $passingMarks ? 'PASSED' : 'FAILED';

        // Calculate Grade & GPA using GradeScale mapping, or fallback
        $percent = ($this->obtainedMarks / $exam->total_marks) * 100;
        $gradeScale = GradeScale::where('tenant_id', $tenantId)
            ->where('min_score', '<=', $percent)
            ->where('max_score', '>=', $percent)
            ->first();

        if ($gradeScale) {
            $grade = $gradeScale->grade;
            $gpa = $gradeScale->gpa;
        } else {
            // Standard fallback
            if ($percent >= 85) { $grade = 'A'; $gpa = 4.00; }
            elseif ($percent >= 70) { $grade = 'B'; $gpa = 3.00; }
            elseif ($percent >= 50) { $grade = 'C'; $gpa = 2.00; }
            else { $grade = 'F'; $gpa = 0.00; }
        }

        ExamResult::create([
            'tenant_id' => $tenantId,
            'exam_id' => $exam->id,
            'user_id' => $this->selectedStudentForResult,
            'marks_obtained' => $this->obtainedMarks,
            'status' => $status,
            'grade' => $grade,
            'gpa' => $gpa,
        ]);

        $this->obtainedMarks = 0.00;
        $this->selectedStudentForResult = '';
        session()->flash('status', 'Exam result recorded and graded successfully!');
    }

    public function submitLeaveRequest()
    {
        $this->validate([
            'selectedEmployeeForLeave' => 'required|exists:employees,id',
            'leaveType' => 'required|string|max:50',
            'leaveStartDate' => 'required|date',
            'leaveEndDate' => 'required|date|after_or_equal:leaveStartDate',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        LeaveRequest::create([
            'tenant_id' => $tenantId,
            'employee_id' => $this->selectedEmployeeForLeave,
            'type' => $this->leaveType,
            'start_date' => $this->leaveStartDate,
            'end_date' => $this->leaveEndDate,
            'status' => 'PENDING',
        ]);

        $this->leaveStartDate = '';
        $this->leaveEndDate = '';
        session()->flash('status', 'Leave request submitted successfully.');
    }

    public function approveLeaveRequest(string $id)
    {
        $req = LeaveRequest::find($id);
        if ($req) {
            $req->update(['status' => 'APPROVED']);
            session()->flash('status', 'Leave request approved.');
        }
    }

    public function rejectLeaveRequest(string $id)
    {
        $req = LeaveRequest::find($id);
        if ($req) {
            $req->update(['status' => 'REJECTED']);
            session()->flash('status', 'Leave request rejected.');
        }
    }

    public function createBadge()
    {
        $this->validate([
            'newBadgeName' => 'required|string|max:100',
            'newBadgeDesc' => 'nullable|string',
            'newBadgeXp' => 'required|integer|min:1',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        Badge::create([
            'tenant_id' => $tenantId,
            'name' => $this->newBadgeName,
            'description' => $this->newBadgeDesc,
            'xp_required' => $this->newBadgeXp,
        ]);

        $this->newBadgeName = '';
        $this->newBadgeDesc = '';
        $this->newBadgeXp = 100;
        session()->flash('status', 'New custom badge registered in reward inventory.');
    }

    public function assignAsset()
    {
        $this->validate([
            'selectedAssetForAssign' => 'required|exists:assets,id',
            'selectedUserForAsset' => 'required|exists:users,id',
        ]);

        $asset = Asset::find($this->selectedAssetForAssign);
        if ($asset->status !== 'AVAILABLE') {
            session()->flash('error', 'Asset is currently not available for assignment.');
            return;
        }

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        
        $asset->update(['status' => 'ASSIGNED']);
        
        AssetAssignment::create([
            'tenant_id' => $tenantId,
            'asset_id' => $asset->id,
            'user_id' => $this->selectedUserForAsset,
            'assigned_at' => now(),
        ]);

        session()->flash('status', 'Asset checked out successfully.');
    }

    public function issueBookLoan()
    {
        $this->validate([
            'selectedBookForLoan' => 'required|exists:books,id',
            'selectedUserForLoan' => 'required|exists:users,id',
            'loanDueDate' => 'required|date|after:today',
        ]);

        $book = Book::find($this->selectedBookForLoan);
        if ($book->available_copies <= 0) {
            session()->flash('error', 'All copies of this book are currently loaned out.');
            return;
        }

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $book->decrement('available_copies');

        BookLoan::create([
            'tenant_id' => $tenantId,
            'book_id' => $book->id,
            'user_id' => $this->selectedUserForLoan,
            'loaned_at' => now(),
            'due_date' => $this->loanDueDate,
            'fine_amount' => 0.00,
        ]);

        $this->loanDueDate = '';
        session()->flash('status', 'Book issued successfully.');
    }

    public function returnBookLoan(string $loanId)
    {
        $loan = BookLoan::find($loanId);
        if ($loan && !$loan->returned_at) {
            $loan->update(['returned_at' => now()]);
            $loan->book->increment('available_copies');
            session()->flash('status', 'Book marked as returned.');
        }
    }

    public function createComplianceItem()
    {
        $this->validate([
            'selectedAccreditationForCompliance' => 'required|exists:accreditations,id',
            'newComplianceItemName' => 'required|string|max:200',
            'newComplianceItemDesc' => 'nullable|string',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        ComplianceItem::create([
            'tenant_id' => $tenantId,
            'accreditation_id' => $this->selectedAccreditationForCompliance,
            'name' => $this->newComplianceItemName,
            'description' => $this->newComplianceItemDesc,
            'is_met' => false,
        ]);

        $this->newComplianceItemName = '';
        $this->newComplianceItemDesc = '';
        session()->flash('status', 'Compliance criteria item added to checklist.');
    }

    public function generateApiKey()
    {
        $this->validate([
            'newApiKeyName' => 'required|string|max:100',
            'newApiKeyRateLimit' => 'required|integer|min:1|max:10000',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId) return;

        $plainToken = 'ns_key_' . Str::random(32);

        ApiKey::create([
            'tenant_id' => $tenantId,
            'name' => $this->newApiKeyName,
            'key_hash' => hash('sha256', $plainToken),
            'is_active' => true,
            'rate_limit' => $this->newApiKeyRateLimit,
        ]);

        $this->generatedToken = $plainToken;
        $this->newApiKeyName = '';
        $this->newApiKeyRateLimit = 60;
        session()->flash('status', 'Personal API access token generated successfully.');
    }

    public function revokeApiKey(string $id)
    {
        $key = ApiKey::find($id);
        if ($key) {
            $key->delete();
            session()->flash('status', 'API access token revoked.');
        }
    }

    public function render()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        // Fetch data based on selected tab
        $data = [];
        if ($tenantId) {
            switch ($this->activeTab) {
                case 'rbac':
                    $data['roles'] = Role::all();
                    $data['accessRequests'] = AccessRequest::with('user')->get();
                    $data['loginHistories'] = LoginHistory::with('user')->orderBy('logged_in_at', 'desc')->take(10)->get();
                    break;
                case 'campus':
                    $data['branches'] = Branch::all();
                    $data['departments'] = Department::all();
                    $data['buildings'] = Building::all();
                    $data['rooms'] = Room::with('building')->get();
                    break;
                case 'exams':
                    $data['examSessions'] = ExamSession::all();
                    $data['exams'] = Exam::with('examSession', 'course')->get();
                    $data['examResults'] = ExamResult::with('exam', 'user')->orderBy('created_at', 'desc')->take(10)->get();
                    $data['courses'] = Course::all();
                    $data['students'] = StudentProfile::with('user')->get();
                    break;
                case 'automations':
                    $data['workflows'] = AutomationWorkflow::all();
                    $data['workflowLogs'] = WorkflowLog::with('workflow')->orderBy('executed_at', 'desc')->take(10)->get();
                    break;
                case 'forms':
                    $data['forms'] = CustomForm::all();
                    $data['formResponses'] = FormResponse::with('form', 'user')->orderBy('created_at', 'desc')->take(10)->get();
                    break;
                case 'gamification':
                    $data['badges'] = Badge::all();
                    $data['userPoints'] = UserPoint::with('user')->orderBy('created_at', 'desc')->take(10)->get();
                    $data['users'] = User::all();
                    break;
                case 'hr':
                    $data['employees'] = Employee::with('user', 'department')->get();
                    $data['payrolls'] = Payroll::with('employee.user')->orderBy('created_at', 'desc')->take(10)->get();
                    $data['leaveRequests'] = LeaveRequest::with('employee.user')->orderBy('created_at', 'desc')->take(10)->get();
                    break;
                case 'assets':
                    $data['assets'] = Asset::all();
                    $data['assetAssignments'] = AssetAssignment::with('asset', 'user')->orderBy('assigned_at', 'desc')->take(10)->get();
                    $data['users'] = User::all();
                    break;
                case 'library':
                    $data['books'] = Book::all();
                    $data['bookLoans'] = BookLoan::with('book', 'user')->orderBy('loaned_at', 'desc')->take(10)->get();
                    $data['users'] = User::all();
                    break;
                case 'accreditation':
                    $data['accreditations'] = Accreditation::all();
                    $data['complianceItems'] = ComplianceItem::with('accreditation')->get();
                    break;
                case 'billing':
                    $data['usages'] = SubscriptionUsage::all();
                    $data['invoices'] = TenantInvoice::orderBy('due_date', 'desc')->get();
                    break;
                case 'api':
                    $data['apiKeys'] = ApiKey::all();
                    $data['apiLogs'] = ApiLog::with('apiKey')->orderBy('created_at', 'desc')->take(10)->get();
                    break;
            }
        }

        return view('livewire.admin.enterprise-console', $data)
            ->layout('layouts.app');
    }
}
