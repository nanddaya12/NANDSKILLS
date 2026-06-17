<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Models\TenantSubscription;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Register extends Component
{
    public string $tenantName = '';
    public string $subdomain = '';
    public string $email = '';
    public string $password = '';
    public string $firstName = '';
    public string $lastName = '';
    public string $errorMessage = '';

    protected array $rules = [
        'tenantName' => 'required|string|max:100',
        'subdomain' => 'required|alpha_dash|unique:tenants,subdomain|max:50',
        'email' => 'required|email|max:255',
        'password' => 'required|min:6',
        'firstName' => 'required|string|max:50',
        'lastName' => 'required|string|max:50',
    ];

    public function register()
    {
        $this->validate();

        // 1. Get or Create Default Plan
        $plan = TenantPlan::firstOrCreate(
            ['name' => 'Standard Enterprise Plan'],
            [
                'price' => 199.00,
                'billing_interval' => 'monthly',
                'max_users' => 500,
                'max_courses' => 50,
                'max_storage_bytes' => 10737418240, // 10 GB
                'features' => ['lms', 'cms', 'crm', 'sis', 'finance', 'helpdesk']
            ]
        );

        // 2. Create Tenant
        $tenant = Tenant::create([
            'name' => $this->tenantName,
            'subdomain' => Str::slug($this->subdomain),
            'status' => 'ACTIVE',
            'plan_id' => $plan->id,
            'primary_color' => '#2563EB',
            'secondary_color' => '#0F172A',
            'typography' => 'Inter',
        ]);

        // 3. Create Subscription
        TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'TRIAL',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        // 4. Create Tenant Admin User
        $user = User::create([
            'tenant_id' => $tenant->id,
            'email' => $this->email,
            'password_hash' => Hash::make($this->password),
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'is_email_verified' => true,
            'status' => 'ACTIVE',
        ]);

        // 5. Provision Tenant Default Roles & Permissions
        $this->provisionTenant($tenant, $user);

        // Log in user and redirect to dashboard
        Auth::login($user);

        // Build redirect URL using APP_URL so port is preserved in local dev.
        // In production this will be http://subdomain.yourdomain.com/dashboard
        $appUrl    = rtrim(env('APP_URL', 'http://127.0.0.1:8000'), '/');
        $parsedUrl = parse_url($appUrl);
        $scheme    = $parsedUrl['scheme'] ?? 'http';
        $host      = $parsedUrl['host']   ?? '127.0.0.1';
        $port      = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';

        $baseDomain = env('APP_BASE_DOMAIN', 'localhost');
        $baseDomainClean = str_replace(['http://', 'https://'], '', $baseDomain);

        // In local dev (127.0.0.1 / localhost), don't switch subdomain —
        // just stay on the same host and go to dashboard.
        if (in_array($host, ['127.0.0.1', 'localhost'])) {
            return redirect()->route('dashboard');
        }

        // In production: redirect to the new subdomain
        $redirectUrl = "{$scheme}://{$tenant->subdomain}.{$baseDomainClean}{$port}/dashboard";
        return redirect()->away($redirectUrl);

    }

    protected function provisionTenant(Tenant $tenant, User $adminUser)
    {
        // Define default system permissions
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
            $perm = Permission::firstOrCreate(['name' => $p['name']], $p);
            $permissionIds[] = $perm->id;
        }

        // Create Tenant Admin role (specific to this tenant)
        $adminRole = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenant Admin',
            'description' => 'Full control over the tenant resources',
            'is_system' => true,
        ]);

        // Link all permissions to Tenant Admin Role
        $adminRole->permissions()->sync($permissionIds);

        // Bind user to Tenant Admin role
        $adminUser->roles()->attach($adminRole->id);

        // Create other default tenant roles
        $defaultRoles = [
            ['name' => 'Trainer', 'desc' => 'Manages assigned course contents and student grades'],
            ['name' => 'Student', 'desc' => 'Enrolls in courses, takes quizzes, and views grades'],
            ['name' => 'Parent', 'desc' => 'Monitors student progress and pays bills'],
            ['name' => 'Branch Admin', 'desc' => 'Controls branch settings and class schedules'],
            ['name' => 'CRM Manager', 'desc' => 'Handles pipeline leads and campaigns'],
            ['name' => 'Content Manager', 'desc' => 'Edits CMS pages and blog lists'],
            ['name' => 'Finance Manager', 'desc' => 'Issues student fee structures and reconciles payments'],
        ];

        foreach ($defaultRoles as $r) {
            Role::create([
                'tenant_id' => $tenant->id,
                'name' => $r['name'],
                'description' => $r['desc'],
                'is_system' => false,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.guest');
    }
}
