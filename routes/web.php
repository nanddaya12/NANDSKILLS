<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\StudentLogin;
use App\Livewire\Auth\StudentRegister;
use App\Livewire\Auth\Mfa;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Cms\PageRenderer;
use App\Livewire\Cms\CmsBuilder;
use App\Livewire\Dashboard\Home;
use App\Livewire\Lms\Classroom;
use App\Livewire\Lms\CourseAdmin;
use App\Livewire\Crm\LeadKanban;
use App\Livewire\Sis\StudentRegistry;
use App\Livewire\Sis\AttendanceManager;
use App\Livewire\Sis\AttendanceCheckIn;
use App\Livewire\Lms\VirtualClassroom;
use App\Livewire\Finance\BillingDashboard;
use App\Livewire\Helpdesk\TicketDashboard;
use App\Livewire\Admin\WhiteLabelSettings;
use App\Livewire\Admin\EnterpriseConsole;
use App\Http\Controllers\MeetingSignalController;
use App\Http\Controllers\Auth\SocialAuthController;

// ── PUBLIC FRONT-END ─────────────────────────────────────────────────────
// CMS landing page — publicly accessible
Route::get('/', PageRenderer::class)->name('home');
Route::get('/verify/{code?}', App\Livewire\Auth\PublicVerification::class)->name('public.verify');

// ── STUDENT AUTH ─────────────────────────────────────────────────────────
// Student portal and social OAuth — accessible from public/tenant domains.
// Admin portals (Super Admin + Staff) are in routes/admin.php and protected
// by domain-based middleware (BlockAdminOnPublicDomain).
Route::middleware('guest')->group(function () {

    // Student login with Email, OTP, and Social OAuth
    Route::get('/student/login', StudentLogin::class)->name('student.login');

    // Student self-registration (separate from Tenant/Academy registration)
    Route::get('/student/register', StudentRegister::class)->name('student.register');
    Route::get('/register', StudentRegister::class)->name('auth.register'); // backward compat link
    Route::get('/mfa', Mfa::class)->name('auth.mfa');
    Route::get('/forgot-password', ForgotPassword::class)->name('auth.forgot-password');
    Route::get('/reset-password', ResetPassword::class)->name('auth.reset-password');

    // Social OAuth (Students only)
    Route::get('/auth/social/{provider}', [SocialAuthController::class, 'redirect'])->name('auth.social');
    Route::get('/auth/social/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.social.callback');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Home::class)->name('dashboard');
    
    // Livewire dashboard page items
    Route::get('/classroom', Classroom::class)->name('classroom');
    Route::get('/courses', CourseAdmin::class)->name('courses');
    Route::get('/leads', LeadKanban::class)->name('leads');
    Route::get('/students', StudentRegistry::class)->name('students');
    Route::get('/billing', BillingDashboard::class)->name('billing');
    Route::get('/admin/subscription', App\Livewire\Finance\SubscriptionDashboard::class)->name('admin.subscription');
    Route::get('/admin/analytics', App\Livewire\Admin\AnalyticsDashboard::class)->name('admin.analytics');
    Route::get('/finance/fees', App\Livewire\Finance\StudentFeeInvoice::class)->name('finance.fees');
    Route::get('/admin/finance/ledger', App\Livewire\Finance\LedgerManager::class)->name('admin.finance.ledger');
    Route::get('/tickets', TicketDashboard::class)->name('tickets');
    Route::get('/communication', App\Livewire\Dashboard\CommunicationHub::class)->name('communication.hub');
    Route::get('/learning-paths', App\Livewire\Lms\LearningPathManager::class)->name('learning.paths');
    Route::get('/placements', App\Livewire\Sis\PlacementPortal::class)->name('placements.index');
    Route::get('/admin/certificates/design', App\Livewire\Admin\CertificateDesigner::class)->name('admin.certificates.design');
    Route::get('/admin/security', App\Livewire\Admin\SecurityCenter::class)->name('admin.security');
    Route::get('/admin/marketplace', App\Livewire\Admin\Marketplace::class)->name('admin.marketplace');
    Route::get('/reports', App\Livewire\Reports\ReportCenter::class)->name('reports.index');

    // Report CSV Export Routes
    Route::get('/reports/attendance',     [App\Http\Controllers\ReportExporter::class, 'attendanceReport'])->name('reports.attendance');
    Route::get('/reports/exams',          [App\Http\Controllers\ReportExporter::class, 'examReport'])->name('reports.exams');
    Route::get('/reports/financial',      [App\Http\Controllers\ReportExporter::class, 'financialReport'])->name('reports.financial');
    Route::get('/reports/students',       [App\Http\Controllers\ReportExporter::class, 'studentDirectoryReport'])->name('reports.students');
    Route::get('/reports/payroll',        [App\Http\Controllers\ReportExporter::class, 'payrollReport'])->name('reports.payroll');
    Route::get('/reports/low-attendance', [App\Http\Controllers\ReportExporter::class, 'lowAttendanceReport'])->name('reports.low_attendance');

    // Fee Receipts & Late Fee Actions
    Route::get('/api/v1/receipts/{invoiceId}/download', [App\Http\Controllers\FeeController::class, 'downloadReceipt'])->name('receipts.download');
    Route::post('/api/v1/finance/late-fees', [App\Http\Controllers\FeeController::class, 'applyLateFees'])->name('finance.late-fees');
    Route::get('/settings', WhiteLabelSettings::class)->name('settings');
    Route::get('/builder', CmsBuilder::class)->name('builder');
    Route::get('/admin/console', EnterpriseConsole::class)->name('admin.console');

    // Academic Management System Routes
    Route::get('/academic/setup', App\Livewire\Academic\AcademicYearManager::class)->name('academic.setup');
    Route::get('/academic/programs', App\Livewire\Academic\ProgramManager::class)->name('academic.programs');
    Route::get('/academic/subjects', App\Livewire\Academic\SubjectManager::class)->name('academic.subjects');
    Route::get('/academic/promotions', App\Livewire\Academic\SessionPromotion::class)->name('academic.promotions');
    Route::get('/admin/academic/transcripts', App\Livewire\Academic\TranscriptEngine::class)->name('admin.academic.transcripts');

    // Admissions Management Routes (Admin/Staff)
    Route::get('/admin/admissions/tracker', App\Livewire\Admissions\AdmissionTracker::class)->name('admissions.tracker');
    Route::get('/admin/admissions/workflow', App\Livewire\Admissions\AdmissionWorkflow::class)->name('admissions.workflow');
    Route::get('/admin/admissions/merit-list', App\Livewire\Admissions\MeritList::class)->name('admissions.merit-list');

    // Timetable & Scheduling Routes
    Route::get('/admin/timetable/builder', App\Livewire\Timetable\TimetableBuilder::class)->name('timetable.builder');

    // Parent Engagement Portal Routes
    Route::get('/parent/dashboard', App\Livewire\Parent\ParentDashboard::class)->name('parent.dashboard');

    // Counseling & Success Interventions Routes
    Route::get('/admin/counseling/cases', App\Livewire\Counseling\CaseManager::class)->name('counseling.cases');

    // ── Phase 18: Digital Document Management ────────────────────────────
    Route::get('/admin/documents', App\Livewire\Documents\DocumentCenter::class)->name('documents.center');
    Route::get('/admin/documents/download/{id}', [App\Http\Controllers\DocumentCenterController::class, 'download'])->name('documents.download');

    // ── Phase 19: Advanced Notification Engine ───────────────────────────
    Route::get('/admin/notifications', App\Livewire\Notifications\NotificationCenter::class)->name('notifications.center');

    // ── Phase 20: Enterprise Audit & Activity Monitoring ─────────────────
    Route::get('/admin/audit', App\Livewire\Audit\AuditDashboard::class)->name('audit.dashboard');
    Route::get('/admin/audit/security', App\Livewire\Audit\SecurityMonitor::class)->name('audit.security');

    // ── Phase 21: Accreditation & Compliance Center ───────────────────────
    Route::get('/admin/compliance', App\Livewire\Compliance\ComplianceDashboard::class)->name('compliance.dashboard');

    // ── Phase 22: Public Website CMS & Marketing ─────────────────────────
    Route::get('/admin/cms/website', App\Livewire\Cms\PublicWebsiteManager::class)->name('cms.website');

    // ── Phase 23: Multi-Campus Intelligence ──────────────────────────────
    Route::get('/admin/campus/compare', App\Livewire\Admin\CampusComparisonDashboard::class)->name('campus.compare');

    // ── Phase 24: Backup & Disaster Recovery ─────────────────────────────
    Route::get('/admin/backup', App\Livewire\Admin\BackupCenter::class)->name('admin.backup');

    // ── Phase 25: SaaS Billing Automation ────────────────────────────────
    Route::get('/admin/saas/billing', App\Livewire\Admin\SaasBillingDashboard::class)->name('admin.saas.billing');

    // ── Phase 27: Intelligent Academic Analytics ──────────────────────────
    Route::get('/admin/intelligence', App\Livewire\Admin\IntelligentAnalytics::class)->name('admin.intelligence');

    // ── CMS Public Routes ─────────────────────────────────────────────────
    Route::get('/sitemap.xml', function () {
        return response()->view('sitemap')->header('Content-Type', 'application/xml');
    })->name('sitemap');
    Route::get('/robots.txt', function () {
        return response("User-agent: *\nAllow: /\nSitemap: " . url('/sitemap.xml'), 200)
            ->header('Content-Type', 'text/plain');
    })->name('robots');

    // Attendance & Meetings
    Route::get('/attendance', AttendanceManager::class)->name('attendance.manage');
    Route::get('/attendance/checkin', AttendanceCheckIn::class)->name('attendance.checkin');
    Route::get('/meetings', VirtualClassroom::class)->name('meetings.index');
    Route::get('/meeting/{meetingId}/session', App\Livewire\Lms\MeetingRoom::class)->name('meeting.session');
    Route::get('/exam/{attemptId}/session', App\Livewire\Lms\ExamPlayer::class)->name('exam.session');

    // WebRTC Signaling Routes (no-external-API conferencing)
    Route::post('/meeting/{roomId}/signal', [MeetingSignalController::class, 'signal'])->name('meeting.signal');
    Route::get('/meeting/{roomId}/token', [MeetingSignalController::class, 'token'])->name('meeting.token');


    Route::get('/certificates', function () {
        return redirect()->route('dashboard');
    })->name('certificates');

    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

// Public Admission Form Route (Outside Auth group)
Route::get('/admissions/apply', App\Livewire\Admissions\AdmissionForm::class)->name('admissions.apply');

