<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Branch;
use App\Models\User;
use App\Models\PermissionGroup;
use App\Models\Department;
use App\Models\Building;
use App\Models\Room;
use App\Models\ExamSession;
use App\Models\ExamType;
use App\Models\GradeScale;
use App\Models\CertificateTemplate;
use App\Models\SubscriptionUsage;
use App\Models\TenantInvoice;
use App\Models\AutomationWorkflow;
use App\Models\CustomForm;
use App\Models\Badge;
use App\Models\ForumCategory;
use App\Models\ContentLibrary;
use App\Models\Document;
use App\Models\NotificationTemplate;
use App\Models\ApiKey;
use App\Models\Employee;
use App\Models\Asset;
use App\Models\Book;
use App\Models\Accreditation;
use App\Models\Course;

class EnterpriseSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch Default Tenant
        $tenant = Tenant::where('subdomain', 'nandskills')->first();
        if (!$tenant) {
            return;
        }

        // Set context
        app()->instance('currentTenant', $tenant);

        // Fetch Trainer & Student
        $trainer = User::where('email', 'trainer@nandskills.com')->first();
        $student = User::where('email', 'student@nandskills.com')->first();
        $branch = Branch::where('tenant_id', $tenant->id)->first();
        $course = Course::where('tenant_id', $tenant->id)->first();

        // 1. RBAC groups
        PermissionGroup::create([
            'tenant_id' => $tenant->id,
            'name' => 'LMS Administration Group',
            'description' => 'Permissions relating to courses and curriculum.',
        ]);

        // 2. Campus departments, buildings, rooms
        if ($branch) {
            $dept = Department::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'name' => 'Computer Science Department',
                'description' => 'Coding and Software Engineering.',
            ]);

            $bld = Building::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'name' => 'Science Block A',
                'description' => 'Main engineering block.',
            ]);

            Room::create([
                'tenant_id' => $tenant->id,
                'building_id' => $bld->id,
                'name' => 'Lab Room 101',
                'capacity' => 45,
            ]);

            Room::create([
                'tenant_id' => $tenant->id,
                'building_id' => $bld->id,
                'name' => 'Auditorium Z',
                'capacity' => 120,
            ]);

            // 19. HR Employee
            if ($trainer && $dept) {
                Employee::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $trainer->id,
                    'department_id' => $dept->id,
                    'salary' => 4500.00,
                    'designation' => 'Senior Coding Instructor',
                    'hire_date' => now()->subYear(),
                ]);
            }
        }

        // 3. Exams
        $session = ExamSession::create([
            'tenant_id' => $tenant->id,
            'name' => 'Spring Semester 2026',
            'status' => 'ACTIVE',
        ]);

        ExamType::create([
            'tenant_id' => $tenant->id,
            'name' => 'Written Theory Exam',
            'description' => 'Standard written testing.',
        ]);

        // 3a. Grade scales
        $grades = [
            ['grade' => 'A', 'min' => 90, 'max' => 100, 'gpa' => 4.00],
            ['grade' => 'B', 'min' => 80, 'max' => 89, 'gpa' => 3.00],
            ['grade' => 'C', 'min' => 70, 'max' => 79, 'gpa' => 2.00],
            ['grade' => 'D', 'min' => 60, 'max' => 69, 'gpa' => 1.00],
            ['grade' => 'F', 'min' => 0, 'max' => 59, 'gpa' => 0.00],
        ];
        foreach ($grades as $g) {
            GradeScale::create([
                'tenant_id' => $tenant->id,
                'grade' => $g['grade'],
                'min_score' => $g['min'],
                'max_score' => $g['max'],
                'gpa' => $g['gpa'],
            ]);
        }

        // 5. Certificates templates
        CertificateTemplate::create([
            'tenant_id' => $tenant->id,
            'name' => 'Official Academy Graduation Template',
            'content_html' => '<h1>Certificate of Graduation</h1><p>Awarded to {{student_name}}</p>',
            'is_active' => true,
        ]);

        // 6. Usages and invoices
        SubscriptionUsage::create([
            'tenant_id' => $tenant->id,
            'metric' => 'users_count',
            'current_value' => 5,
            'max_limit' => 1000,
        ]);
        SubscriptionUsage::create([
            'tenant_id' => $tenant->id,
            'metric' => 'courses_count',
            'current_value' => 1,
            'max_limit' => 100,
        ]);
        SubscriptionUsage::create([
            'tenant_id' => $tenant->id,
            'metric' => 'storage_bytes',
            'current_value' => 2450000,
            'max_limit' => 53687091200,
        ]);

        TenantInvoice::create([
            'tenant_id' => $tenant->id,
            'invoice_number' => 'INV-SAAS-2026-001',
            'amount' => 299.00,
            'status' => 'PAID',
            'due_date' => now()->subMonth(),
            'paid_at' => now()->subMonth(),
        ]);

        // 7. Workflows
        AutomationWorkflow::create([
            'tenant_id' => $tenant->id,
            'name' => 'New Student Orientation Alert',
            'trigger_event' => 'STUDENT_REGISTERED',
            'is_active' => true,
        ]);

        // 8. Custom Forms
        CustomForm::create([
            'tenant_id' => $tenant->id,
            'title' => 'Academy Feedback Survey',
            'description' => 'Submit details regarding course content.',
            'is_public' => true,
        ]);

        // 11. Gamification
        Badge::create([
            'tenant_id' => $tenant->id,
            'name' => '100% Attendance Streak',
            'description' => 'Awarded for complete branch attendance.',
            'icon_url' => 'attendance_badge.png',
            'xp_required' => 200,
        ]);

        // 12. Forum category
        ForumCategory::create([
            'tenant_id' => $tenant->id,
            'name' => 'General Class Discussions',
            'description' => 'Post non-course specific questions here.',
        ]);

        // 13. Content Library
        ContentLibrary::create([
            'tenant_id' => $tenant->id,
            'title' => 'Dynamic Routing Video Guide',
            'type' => 'VIDEO',
            'file_url' => 'https://example.com/routing_tutorial.mp4',
            'storage_key' => 'media/routing_tutorial.mp4',
        ]);

        // 15. Blogs / Articles
        Document::create([
            'tenant_id' => $tenant->id,
            'name' => 'Academy Operational Guidelines SOP',
            'category' => 'Operations',
            'status' => 'APPROVED',
        ]);

        NotificationTemplate::create([
            'tenant_id' => $tenant->id,
            'name' => 'SMS Verification Alert',
            'channel' => 'SMS',
            'body' => 'Your nandskills verification code is: {{code}}',
        ]);

        ApiKey::create([
            'tenant_id' => $tenant->id,
            'name' => 'Analytics Webhook Key',
            'key_hash' => hash('sha256', 'nandskills_secret_api_key_2026'),
            'is_active' => true,
            'rate_limit' => 60,
        ]);

        // 20. Inventory Assets
        Asset::create([
            'tenant_id' => $tenant->id,
            'name' => 'Interactive Smart Whiteboard',
            'serial_number' => 'SW-4455-8899',
            'status' => 'AVAILABLE',
        ]);

        // 21. Books
        Book::create([
            'tenant_id' => $tenant->id,
            'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
            'author' => 'Robert C. Martin',
            'isbn' => '978-0132350884',
            'total_copies' => 10,
            'available_copies' => 10,
        ]);

        // 22. Accreditation
        Accreditation::create([
            'tenant_id' => $tenant->id,
            'framework_name' => 'ISO 9001 Academic Quality Certificate',
            'agency' => 'Standard Audit Committee',
            'status' => 'PENDING',
        ]);
    }
}
