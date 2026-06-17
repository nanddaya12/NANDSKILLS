<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\TenantPlan;
use App\Models\TenantSubscription;
use App\Models\TenantInvoice;
use App\Models\User;
use App\Models\Course;
use App\Models\MediaFile;
use Illuminate\Support\Str;

class SubscriptionDashboard extends Component
{
    public $plans = [];
    public $invoices = [];
    public $currentPlan;
    public $subscription;
    public $stats = [];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized action. Subscription control is restricted to Tenant Administrators.');
        }

        $this->loadSubscriptionData();
    }

    public function loadSubscriptionData()
    {
        $tenant = app('currentTenant');
        
        $this->plans = TenantPlan::all();
        $this->currentPlan = $tenant->plan;
        
        // Load active subscription or create a dummy one if none exists
        $this->subscription = TenantSubscription::where('tenant_id', $tenant->id)
            ->where('status', 'ACTIVE')
            ->latest()
            ->first();

        if (!$this->subscription) {
            $this->subscription = TenantSubscription::firstOrCreate(
                ['tenant_id' => $tenant->id, 'status' => 'ACTIVE'],
                [
                    'plan_id' => $tenant->plan_id,
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'stripe_subscription_id' => 'sub_mock_' . Str::random(10),
                ]
            );
        }

        // Generate mock invoice if none exist
        $this->invoices = TenantInvoice::where('tenant_id', $tenant->id)->orderBy('created_at', 'desc')->get();
        if ($this->invoices->isEmpty()) {
            TenantInvoice::create([
                'tenant_id' => $tenant->id,
                'invoice_number' => 'TS-' . date('Y') . '-' . strtoupper(Str::random(5)),
                'amount' => $this->currentPlan->price,
                'status' => 'PAID',
                'due_date' => now()->subDays(5),
                'paid_at' => now()->subDays(5),
                'stripe_payment_id' => 'ch_' . Str::random(10),
            ]);
            $this->invoices = TenantInvoice::where('tenant_id', $tenant->id)->orderBy('created_at', 'desc')->get();
        }

        // Calculate usage statistics
        $usersCount = User::count();
        $coursesCount = Course::count();
        $storageBytes = MediaFile::sum('file_size');

        $this->stats = [
            'users' => [
                'current' => $usersCount,
                'max' => $this->currentPlan->max_users,
                'percentage' => min(100, round(($usersCount / max(1, $this->currentPlan->max_users)) * 100)),
            ],
            'courses' => [
                'current' => $coursesCount,
                'max' => $this->currentPlan->max_courses,
                'percentage' => min(100, round(($coursesCount / max(1, $this->currentPlan->max_courses)) * 100)),
            ],
            'storage' => [
                'current' => round($storageBytes / (1024 * 1024), 2), // in MB
                'max' => round($this->currentPlan->max_storage_bytes / (1024 * 1024), 2), // in MB
                'percentage' => min(100, round(($storageBytes / max(1, $this->currentPlan->max_storage_bytes)) * 100)),
            ]
        ];
    }

    public function changePlan($planId)
    {
        $tenant = app('currentTenant');
        $plan = TenantPlan::find($planId);

        if ($plan) {
            $tenant->update(['plan_id' => $plan->id]);

            // Update active subscription
            if ($this->subscription) {
                $this->subscription->update([
                    'plan_id' => $plan->id,
                    'end_date' => now()->addMonth(),
                ]);
            }

            // Create new invoice for the upgrade
            TenantInvoice::create([
                'tenant_id' => $tenant->id,
                'invoice_number' => 'TS-' . date('Y') . '-' . strtoupper(Str::random(5)),
                'amount' => $plan->price,
                'status' => 'PAID',
                'due_date' => now(),
                'paid_at' => now(),
                'stripe_payment_id' => 'ch_' . Str::random(10),
            ]);

            session()->flash('success', "Successfully switched to the {$plan->name}!");
            $this->loadSubscriptionData();
        }
    }

    public function render()
    {
        return view('livewire.finance.subscription-dashboard')
            ->layout('layouts.app');
    }
}
