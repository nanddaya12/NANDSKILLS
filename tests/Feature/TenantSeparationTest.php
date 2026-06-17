<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TenantPlan;
use App\Models\Tenant;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\StudentFee;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Branch;
use App\Models\FeeStructure;

class TenantSeparationTest extends TestCase
{
    use RefreshDatabase;

    private TenantPlan $plan;
    private Tenant $tenant1;
    private Tenant $tenant2;

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
    }

    public function test_tenant_scope_isolates_records_successfully()
    {
        // 1. Set context to Tenant 1
        app()->instance('currentTenant', $this->tenant1);

        // Create course under Tenant 1 (should automatically assign tenant_id)
        $course1 = Course::create([
            'title' => 'Web Design for Beginners',
            'slug' => 'web-design-beginners',
            'description' => 'Intro course.',
            'status' => 'PUBLISHED',
            'price' => 49.00,
        ]);

        $this->assertEquals($this->tenant1->id, $course1->tenant_id);

        // 2. Set context to Tenant 2
        app()->instance('currentTenant', $this->tenant2);

        // Create course under Tenant 2
        $course2 = Course::create([
            'title' => 'Advanced Laravel 12 Architecture',
            'slug' => 'advanced-laravel-12',
            'description' => 'Deep dive.',
            'status' => 'PUBLISHED',
            'price' => 149.00,
        ]);

        $this->assertEquals($this->tenant2->id, $course2->tenant_id);

        // 3. Query courses under Tenant 1 context
        app()->instance('currentTenant', $this->tenant1);
        $coursesTenant1 = Course::all();

        $this->assertCount(1, $coursesTenant1);
        $this->assertEquals($course1->id, $coursesTenant1->first()->id);

        // 4. Query courses under Tenant 2 context
        app()->instance('currentTenant', $this->tenant2);
        $coursesTenant2 = Course::all();

        $this->assertCount(1, $coursesTenant2);
        $this->assertEquals($course2->id, $coursesTenant2->first()->id);
    }

    public function test_unique_subdomain_registration_validation()
    {
        // Assert that subdomain must be unique
        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        Tenant::create([
            'name' => 'Duplicate Academy',
            'subdomain' => 'academy1', // academy1 already exists from setUp
            'status' => 'ACTIVE',
            'plan_id' => $this->plan->id,
        ]);
    }
}
