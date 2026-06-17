<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TenantPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Models\AcademicYear;
use App\Models\AcademicSession;
use App\Models\TimetableSlot;
use App\Models\DocumentCategory;
use App\Models\ManagedDocument;
use App\Models\ComplianceFramework;
use App\Models\NotificationCategory;
use App\Services\PerformanceOptimizer;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Room;
use App\Models\Timetable;
use App\Models\TimetableEntry;
use App\Models\StudentSessionEnrollment;
use App\Models\DocumentAuditTrail;
use App\Models\Transcript;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Branch;
use App\Models\Building;
use Livewire\Livewire;

class EnterpriseExtensionTest extends TestCase
{
    use RefreshDatabase;

    private TenantPlan $plan;
    private Tenant $tenant;
    private User $admin;
    private Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = TenantPlan::create([
            'name' => 'Enterprise Plan',
            'price' => 299.00,
            'billing_interval' => 'monthly',
            'max_users' => 1000,
            'max_courses' => 100,
            'max_storage_bytes' => 53687091200,
            'features' => ['lms', 'cms', 'crm', 'sis', 'finance', 'helpdesk']
        ]);

        $this->tenant = Tenant::create([
            'name' => 'Test Academy',
            'subdomain' => 'test-academy',
            'status' => 'ACTIVE',
            'plan_id' => $this->plan->id,
        ]);

        app()->instance('currentTenant', $this->tenant);

        $this->adminRole = Role::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Tenant Admin',
        ]);

        $this->admin = User::create([
            'tenant_id' => $this->tenant->id,
            'email' => 'admin@test-academy.com',
            'password_hash' => 'dummy',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'status' => 'ACTIVE',
        ]);
        $this->admin->roles()->attach($this->adminRole->id);
    }

    public function test_academic_year_manager_crud()
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Academic\AcademicYearManager::class)
            ->set('year_name', 'Academic Year 2026-2027')
            ->set('year_start_date', '2026-09-01')
            ->set('year_end_date', '2027-06-30')
            ->call('saveYear')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('academic_years', [
            'tenant_id' => $this->tenant->id,
            'name' => 'Academic Year 2026-2027',
        ]);
    }

    public function test_timetable_builder_works_with_renamed_slots()
    {
        $this->actingAs($this->admin);

        // Create academic session
        $year = AcademicYear::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Year 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]);
        $session = AcademicSession::create([
            'tenant_id' => $this->tenant->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester 1',
            'type' => 'SEMESTER',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
        ]);

        $slot = TimetableSlot::create([
            'tenant_id' => $this->tenant->id,
            'label' => 'Period 1',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'sort_order' => 1,
        ]);

        Livewire::test(\App\Livewire\Timetable\TimetableBuilder::class)
            ->assertSet('selectedSemester', 1)
            ->assertHasNoErrors();
    }

    public function test_document_center_upload()
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Documents\DocumentCenter::class)
            ->set('new_folder_name', 'Student Files')
            ->set('new_folder_icon', '📁')
            ->call('saveFolder')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('document_categories', [
            'tenant_id' => $this->tenant->id,
            'name' => 'Student Files',
        ]);
    }

    public function test_compliance_framework_management()
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Compliance\ComplianceDashboard::class)
            ->set('fw_name', 'HEC Accreditation Standard')
            ->set('fw_agency', 'HEC')
            ->set('fw_version', 'v2')
            ->call('saveFramework')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('compliance_frameworks', [
            'tenant_id' => $this->tenant->id,
            'name' => 'HEC Accreditation Standard',
        ]);
    }

    public function test_notification_center_tabbing_and_categories()
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Notifications\NotificationCenter::class)
            ->set('cat_name', 'Exam Alerts')
            ->set('cat_icon', '📝')
            ->call('saveCategory')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('notification_categories', [
            'tenant_id' => $this->tenant->id,
            'name' => 'Exam Alerts',
        ]);
    }

    public function test_performance_optimizer_caches_stats()
    {
        PerformanceOptimizer::bustTenantStats($this->tenant->id);

        $stats = PerformanceOptimizer::tenantStats($this->tenant->id);
        $this->assertArrayHasKey('total_students', $stats);
        $this->assertArrayHasKey('active_courses', $stats);

        // Cache should return same stats
        $statsCached = PerformanceOptimizer::tenantStats($this->tenant->id);
        $this->assertEquals($stats['cached_at'], $statsCached['cached_at']);
    }

    public function test_ledger_manager_balanced_posting()
    {
        $this->actingAs($this->admin);

        $cash = Account::create([
            'tenant_id' => $this->tenant->id,
            'code' => '1010',
            'name' => 'Cash',
            'type' => 'ASSET',
        ]);

        $equity = Account::create([
            'tenant_id' => $this->tenant->id,
            'code' => '3000',
            'name' => 'Retained Earnings',
            'type' => 'EQUITY',
        ]);

        Livewire::test(\App\Livewire\Finance\LedgerManager::class)
            ->set('entry_date', '2026-06-13')
            ->set('reference_number', 'JV-TEST-001')
            ->set('description', 'Test capital injection')
            ->set('journalLines', [
                ['account_id' => $cash->id, 'type' => 'DEBIT', 'amount' => 1000.00, 'memo' => 'Debit cash'],
                ['account_id' => $equity->id, 'type' => 'CREDIT', 'amount' => 1000.00, 'memo' => 'Credit equity'],
            ])
            ->call('saveJournalEntry')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('journal_entries', [
            'tenant_id' => $this->tenant->id,
            'reference_number' => 'JV-TEST-001',
        ]);

        $this->assertEquals(1000.00, $cash->fresh()->balance);
    }

    public function test_ledger_manager_unbalanced_posting()
    {
        $this->actingAs($this->admin);

        $cash = Account::create([
            'tenant_id' => $this->tenant->id,
            'code' => '1010',
            'name' => 'Cash',
            'type' => 'ASSET',
        ]);

        $equity = Account::create([
            'tenant_id' => $this->tenant->id,
            'code' => '3000',
            'name' => 'Retained Earnings',
            'type' => 'EQUITY',
        ]);

        Livewire::test(\App\Livewire\Finance\LedgerManager::class)
            ->set('entry_date', '2026-06-13')
            ->set('reference_number', 'JV-TEST-002')
            ->set('description', 'Test unbalanced')
            ->set('journalLines', [
                ['account_id' => $cash->id, 'type' => 'DEBIT', 'amount' => 1000.00, 'memo' => 'Debit cash'],
                ['account_id' => $equity->id, 'type' => 'CREDIT', 'amount' => 900.00, 'memo' => 'Credit equity'],
            ])
            ->call('saveJournalEntry')
            ->assertHasErrors(['journalLines']);
    }

    public function test_expense_approval_posts_entries()
    {
        $this->actingAs($this->admin);

        $expenseAcc = Account::create([
            'tenant_id' => $this->tenant->id,
            'code' => '5010',
            'name' => 'Salaries',
            'type' => 'EXPENSE',
        ]);

        $cashAcc = Account::create([
            'tenant_id' => $this->tenant->id,
            'code' => '1010',
            'name' => 'Cash',
            'type' => 'ASSET',
        ]);

        $vendor = Vendor::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Vendor',
        ]);

        $expense = Expense::create([
            'tenant_id' => $this->tenant->id,
            'account_id' => $expenseAcc->id,
            'payment_account_id' => $cashAcc->id,
            'amount' => 350.00,
            'expense_date' => '2026-06-13',
            'vendor_id' => $vendor->id,
            'description' => 'Test monthly salaries payment',
            'status' => 'PENDING',
        ]);

        Livewire::test(\App\Livewire\Finance\LedgerManager::class)
            ->call('approveExpense', $expense->id)
            ->assertHasNoErrors();

        $this->assertEquals('APPROVED', $expense->fresh()->status);
        $this->assertDatabaseHas('journal_entries', [
            'tenant_id' => $this->tenant->id,
            'description' => 'Approved Expense: Test monthly salaries payment',
        ]);
    }

    public function test_secure_document_download()
    {
        $this->actingAs($this->admin);

        $category = DocumentCategory::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Category',
            'icon' => '📁',
        ]);

        $document = ManagedDocument::create([
            'tenant_id' => $this->tenant->id,
            'category_id' => $category->id,
            'title' => 'Test Document',
            'original_filename' => 'test.txt',
            'file_path' => 'documents/test.txt',
            'file_size' => 1234,
            'mime_type' => 'text/plain',
            'is_public' => false,
            'uploaded_by' => $this->admin->id,
        ]);

        $response = $this->get(route('documents.download', ['id' => $document->id]));
        $response->assertStatus(200);

        $this->assertEquals(1, $document->fresh()->download_count);
        $this->assertDatabaseHas('document_audit_trail', [
            'document_id' => $document->id,
            'user_id' => $this->admin->id,
            'action' => 'DOWNLOAD',
        ]);
    }

    public function test_timetable_builder_room_capacity_conflict()
    {
        $this->actingAs($this->admin);

        $program = Program::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'CS',
            'code' => 'CS',
            'status' => 'ACTIVE',
        ]);

        $year = AcademicYear::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Year 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]);

        $session = AcademicSession::create([
            'tenant_id' => $this->tenant->id,
            'academic_year_id' => $year->id,
            'name' => 'Sem 1',
            'type' => 'SEMESTER',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
        ]);

        // Enroll 2 students
        for ($i = 0; $i < 2; $i++) {
            $student = User::create([
                'tenant_id' => $this->tenant->id,
                'email' => "student{$i}@test.com",
                'password_hash' => 'dummy',
                'first_name' => "Student",
                'last_name' => "{$i}",
                'status' => 'ACTIVE',
            ]);

            StudentSessionEnrollment::create([
                'tenant_id' => $this->tenant->id,
                'academic_session_id' => $session->id,
                'student_user_id' => $student->id,
                'program_id' => $program->id,
                'current_semester' => 1,
                'status' => 'ACTIVE',
            ]);
        }

        $branch = \App\Models\Branch::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Main Branch',
        ]);

        $building = \App\Models\Building::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $branch->id,
            'name' => 'Science Block',
        ]);

        $room = Room::create([
            'tenant_id' => $this->tenant->id,
            'building_id' => $building->id,
            'name' => 'Small Room',
            'capacity' => 1, // Only capacity for 1
        ]);

        $subject = Subject::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Programming',
            'code' => 'CS101',
            'status' => 'ACTIVE',
        ]);

        $slot = TimetableSlot::create([
            'tenant_id' => $this->tenant->id,
            'label' => 'Period 1',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'sort_order' => 1,
        ]);

        $timetable = Timetable::create([
            'tenant_id' => $this->tenant->id,
            'program_id' => $program->id,
            'academic_session_id' => $session->id,
            'semester_no' => 1,
            'title' => 'Schedule',
            'status' => 'PUBLISHED',
        ]);

        $entry = TimetableEntry::create([
            'timetable_id' => $timetable->id,
            'slot_id' => $slot->id,
            'subject_id' => $subject->id,
            'room_id' => $room->id,
            'teacher_user_id' => $this->admin->id,
            'day_of_week' => 1,
        ]);

        Livewire::test(\App\Livewire\Timetable\TimetableBuilder::class)
            ->set('selectedProgramId', $program->id)
            ->set('selectedSessionId', $session->id)
            ->set('selectedSemester', 1)
            ->call('loadTimetable')
            ->assertSet('conflictWarnings', [
                "Room capacity exceeded: Classroom Small Room has capacity of 1 students, but current cohort has 2 students enrolled."
            ]);
    }
}
