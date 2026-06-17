<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TenantPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\StudentProfile;
use App\Models\ParentProfile;
use App\Models\PipelineStage;
use App\Models\Lead;
use App\Models\Deal;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\Invoice;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Tenant Plans
        $planStandard = TenantPlan::create([
            'name' => 'Standard Plan',
            'price' => 99.00,
            'billing_interval' => 'monthly',
            'max_users' => 100,
            'max_courses' => 10,
            'max_storage_bytes' => 5368709120, // 5 GB
            'features' => ['lms', 'cms', 'crm', 'helpdesk']
        ]);

        $planEnterprise = TenantPlan::create([
            'name' => 'Enterprise Plan',
            'price' => 299.00,
            'billing_interval' => 'monthly',
            'max_users' => 1000,
            'max_courses' => 100,
            'max_storage_bytes' => 53687091200, // 50 GB
            'features' => ['lms', 'cms', 'crm', 'sis', 'finance', 'helpdesk']
        ]);

        // 2. Create Default Tenant: NANDSKILLS
        $tenant = Tenant::create([
            'name' => 'NANDSKILLS Education',
            'subdomain' => 'nandskills',
            'custom_domain' => 'nandskills.local',
            'status' => 'ACTIVE',
            'primary_color' => '#2563EB',
            'secondary_color' => '#0F172A',
            'typography' => 'Inter',
            'plan_id' => $planEnterprise->id,
        ]);

        TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $planEnterprise->id,
            'status' => 'ACTIVE',
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);

        // Bind tenant context for seeder operations
        app()->instance('currentTenant', $tenant);

        // 3. Create Permissions
        $permissions = [
            ['name' => 'tenant.manage', 'action' => 'manage', 'subject' => 'tenant', 'description' => 'Manage tenant configurations'],
            ['name' => 'users.create', 'action' => 'create', 'subject' => 'users', 'description' => 'Create users'],
            ['name' => 'users.read', 'action' => 'read', 'subject' => 'users', 'description' => 'Read users list'],
            ['name' => 'users.update', 'action' => 'update', 'subject' => 'users', 'description' => 'Update users'],
            ['name' => 'users.delete', 'action' => 'delete', 'subject' => 'users', 'description' => 'Delete users'],
            ['name' => 'course.create', 'action' => 'create', 'subject' => 'course', 'description' => 'Create LMS courses'],
            ['name' => 'course.read', 'action' => 'read', 'subject' => 'course', 'description' => 'Read LMS courses'],
            ['name' => 'course.update', 'action' => 'update', 'subject' => 'course', 'description' => 'Update LMS courses'],
            ['name' => 'course.delete', 'action' => 'delete', 'subject' => 'course', 'description' => 'Delete LMS courses'],
            ['name' => 'leads.manage', 'action' => 'manage', 'subject' => 'leads', 'description' => 'Manage CRM leads'],
            ['name' => 'finance.manage', 'action' => 'manage', 'subject' => 'finance', 'description' => 'Manage invoices and payments'],
            ['name' => 'tickets.manage', 'action' => 'manage', 'subject' => 'tickets', 'description' => 'Manage support tickets'],
        ];

        $permissionIds = [];
        foreach ($permissions as $p) {
            $perm = Permission::create($p);
            $permissionIds[] = $perm->id;
        }

        // 4. Create Tenant Admin Role & Bind to User
        $adminRole = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenant Admin',
            'description' => 'Full administrative access.',
            'is_system' => true,
        ]);
        $adminRole->permissions()->sync($permissionIds);

        $adminUser = User::create([
            'tenant_id' => $tenant->id,
            'email' => 'admin@nandskills.com',
            'password_hash' => Hash::make('admin123'),
            'first_name' => 'Nand',
            'last_name' => 'Admin',
            'is_email_verified' => true,
            'status' => 'ACTIVE',
        ]);
        $adminUser->roles()->attach($adminRole->id);

        // Create Super Admin Role & Bind to User
        $superAdminRole = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Super Admin',
            'description' => 'SaaS Platform Administrator.',
            'is_system' => true,
        ]);
        $superAdminRole->permissions()->sync($permissionIds);

        $superAdminUser = User::create([
            'tenant_id' => $tenant->id,
            'email' => 'superadmin@nandskills.com',
            'password_hash' => Hash::make('superadmin123'),
            'first_name' => 'Nand',
            'last_name' => 'SuperAdmin',
            'is_email_verified' => true,
            'status' => 'ACTIVE',
        ]);
        $superAdminUser->roles()->attach($superAdminRole->id);

        // 5. Create System-Wide / Other Default Tenant Roles
        $trainerRole = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Trainer',
            'description' => 'Trainer access.',
            'is_system' => true,
        ]);

        $studentRole = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Student',
            'description' => 'Student access.',
            'is_system' => true,
        ]);

        $parentRole = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Parent',
            'description' => 'Parent access.',
            'is_system' => true,
        ]);

        // 6. Create Users for other roles
        $trainerUser = User::create([
            'tenant_id' => $tenant->id,
            'email' => 'trainer@nandskills.com',
            'password_hash' => Hash::make('trainer123'),
            'first_name' => 'Zahid',
            'last_name' => 'Trainer',
            'is_email_verified' => true,
            'status' => 'ACTIVE',
        ]);
        $trainerUser->roles()->attach($trainerRole->id);

        $studentUser = User::create([
            'tenant_id' => $tenant->id,
            'email' => 'student@nandskills.com',
            'password_hash' => Hash::make('student123'),
            'first_name' => 'Ali',
            'last_name' => 'Khan',
            'is_email_verified' => true,
            'status' => 'ACTIVE',
        ]);
        $studentUser->roles()->attach($studentRole->id);

        $parentUser = User::create([
            'tenant_id' => $tenant->id,
            'email' => 'parent@nandskills.com',
            'password_hash' => Hash::make('parent123'),
            'first_name' => 'Tariq',
            'last_name' => 'Khan',
            'is_email_verified' => true,
            'status' => 'ACTIVE',
        ]);
        $parentUser->roles()->attach($parentRole->id);

        // 7. Create default Branch
        $branch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Jamshoro Branch',
            'address' => 'Phase 1, Jamshoro, Sindh',
            'contact_email' => 'jamshoro@nandskills.com',
            'contact_phone' => '+92-22-1234567',
        ]);

        // 8. Create Course catalog (LMS)
        $course = Course::create([
            'tenant_id' => $tenant->id,
            'title' => 'Advanced Web Engineering',
            'slug' => 'advanced-web-engineering',
            'description' => 'Master PHP, Laravel, Livewire, and database architectures.',
            'short_description' => 'Hands-on enterprise application development program.',
            'cover_image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500',
            'status' => 'PUBLISHED',
            'price' => 199.00,
        ]);

        $chapter = Chapter::create([
            'course_id' => $course->id,
            'title' => 'Routing & Controller Architectures',
            'description' => 'Learn how to define endpoints and manage multi-tenant HTTP requests.',
            'order_index' => 1,
        ]);

        Lesson::create([
            'chapter_id' => $chapter->id,
            'title' => 'Introduction to Single-Database Tenancy',
            'description' => 'How to write custom Middlewares to identify hosts and load context.',
            'content' => '<p>In this lesson, we cover standard host filters and database isolation queries.</p>',
            'type' => 'VIDEO',
            'url' => 'https://example.com/videos/lesson1.mp4',
            'order_index' => 1,
            'duration_minutes' => 20,
        ]);

        Lesson::create([
            'chapter_id' => $chapter->id,
            'title' => 'Query Scopes & Observer Logging',
            'description' => 'Applying global scopes to automate where filters in database entities.',
            'content' => '<p>Learn to secure database queries with Global Eloquent Scopes.</p>',
            'type' => 'PDF',
            'url' => 'https://example.com/docs/lesson2.pdf',
            'order_index' => 2,
            'duration_minutes' => 15,
        ]);

        // Enroll Student
        Enrollment::create([
            'tenant_id' => $tenant->id,
            'user_id' => $studentUser->id,
            'course_id' => $course->id,
            'progress_percent' => 0,
            'status' => 'ACTIVE',
            'enrolled_at' => now(),
        ]);

        // 9. Create SIS Profiles
        $studentProfile = StudentProfile::create([
            'user_id' => $studentUser->id,
            'tenant_id' => $tenant->id,
            'roll_number' => 'NS-2026-001',
            'admission_date' => now()->subMonths(2),
            'status' => 'ACTIVE',
        ]);

        $parentProfile = ParentProfile::create([
            'user_id' => $parentUser->id,
            'tenant_id' => $tenant->id,
            'relationship' => 'Father',
            'occupation' => 'Business Owner',
            'address' => 'Flat 402, Al-Latif residency, Jamshoro',
        ]);

        $studentProfile->parents()->attach($parentProfile->id);

        // 10. Create CRM Pipeline Stages & Leads
        $leadStageNew = PipelineStage::create([
            'tenant_id' => $tenant->id,
            'name' => 'New Leads',
            'order_index' => 1,
            'color' => '#3B82F6',
        ]);

        $leadStageWorking = PipelineStage::create([
            'tenant_id' => $tenant->id,
            'name' => 'Working Leads',
            'order_index' => 2,
            'color' => '#F59E0B',
        ]);

        $lead = Lead::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Asad',
            'last_name' => 'Memon',
            'email' => 'asad@gmail.com',
            'phone' => '+92-300-1112223',
            'value' => 299.00,
            'source' => 'Organic',
            'status' => 'NEW',
        ]);

        Deal::create([
            'tenant_id' => $tenant->id,
            'lead_id' => $lead->id,
            'stage_id' => $leadStageNew->id,
            'amount' => 299.00,
            'status' => 'OPEN',
        ]);

        // 11. Create Finance Billing structures
        $fee = FeeStructure::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admission & Registration Fee',
            'description' => 'One-time admission charge.',
            'amount' => 150.00,
            'due_date' => now()->addDays(5),
            'frequency' => 'ONCE',
        ]);

        $studentFee = StudentFee::create([
            'student_id' => $studentProfile->id,
            'fee_structure_id' => $fee->id,
            'discount_amount' => 0.00,
            'scholarship_amount' => 50.00,
            'net_amount' => 100.00,
            'status' => 'UNPAID',
        ]);

        Invoice::create([
            'tenant_id' => $tenant->id,
            'student_fee_id' => $studentFee->id,
            'invoice_number' => 'INV-2026-001',
            'amount' => 150.00,
            'tax' => 0.00,
            'total' => 100.00,
            'status' => 'UNPAID',
            'due_date' => $fee->due_date,
        ]);

        // 12. Create Support Ticket category
        TicketCategory::create([
            'tenant_id' => $tenant->id,
            'name' => 'LMS Classroom Support',
            'description' => 'Course Player issues.',
        ]);

        $this->call(EnterpriseSeeder::class);
        $this->call(FinanceSeeder::class);
    }
}
