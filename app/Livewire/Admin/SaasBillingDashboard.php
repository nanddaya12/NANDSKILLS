<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\TenantPlan;
use App\Models\TenantSubscription;
use App\Models\TenantInvoice;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class SaasBillingDashboard extends Component
{
    public string $activeTab     = 'overview';   // overview | subscriptions | coupons | invoices
    public string $searchQuery   = '';

    // Coupon form
    public bool   $isCouponFormOpen  = false;
    public string $coupon_code       = '';
    public string $coupon_name       = '';
    public string $coupon_type       = 'PERCENTAGE';
    public float  $coupon_value      = 10.0;
    public int    $coupon_max_uses   = 100;
    public string $coupon_valid_from = '';
    public string $coupon_expires_at = '';

    // Stats
    public float $mrr     = 0;
    public float $arr     = 0;
    public int   $active  = 0;
    public int   $churned = 0;

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'SaaS Billing is restricted to Super Admin.');
        }
        $this->computeMetrics();
    }

    public function computeMetrics(): void
    {
        $this->active  = TenantSubscription::where('status', 'ACTIVE')->count();
        $this->churned = TenantSubscription::where('status', 'CANCELLED')->count();
        $this->mrr     = TenantSubscription::where('status', 'ACTIVE')
            ->join('tenant_plans', 'tenant_subscriptions.plan_id', '=', 'tenant_plans.id')
            ->sum('tenant_plans.monthly_price');
        $this->arr = $this->mrr * 12;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function saveCoupon(): void
    {
        $this->validate([
            'coupon_code'  => 'required|string|max:30',
            'coupon_name'  => 'required|string|max:100',
            'coupon_value' => 'required|numeric|min:0',
        ]);

        DB::table('coupon_codes')->insert([
            'id'             => \Illuminate\Support\Str::uuid(),
            'code'           => strtoupper(trim($this->coupon_code)),
            'name'           => $this->coupon_name,
            'discount_type'  => $this->coupon_type,
            'discount_value' => $this->coupon_value,
            'max_uses'       => $this->coupon_max_uses ?: null,
            'used_count'     => 0,
            'valid_from'     => $this->coupon_valid_from ?: null,
            'expires_at'     => $this->coupon_expires_at ?: null,
            'is_active'      => true,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $this->isCouponFormOpen = false;
        $this->reset(['coupon_code', 'coupon_name', 'coupon_value']);
    }

    public function deleteCoupon(string $id): void
    {
        DB::table('coupon_codes')->where('id', $id)->delete();
    }

    public function toggleCoupon(string $id): void
    {
        $coupon = DB::table('coupon_codes')->where('id', $id)->first();
        DB::table('coupon_codes')->where('id', $id)->update([
            'is_active'  => !$coupon->is_active,
            'updated_at' => now(),
        ]);
    }

    public function cancelSubscription(string $id): void
    {
        TenantSubscription::findOrFail($id)->update(['status' => 'CANCELLED', 'ends_at' => now()]);
        $this->computeMetrics();
    }

    public function render()
    {
        $subscriptions = TenantSubscription::with('tenant:id,name', 'plan:id,name,monthly_price')
            ->when($this->searchQuery, fn($q) => $q->whereHas('tenant', fn($t) => $t->where('name', 'like', '%' . $this->searchQuery . '%')))
            ->orderByDesc('created_at')
            ->paginate(15);

        $coupons = DB::table('coupon_codes')
            ->orderByDesc('created_at')
            ->get();

        $plans = TenantPlan::orderBy('monthly_price')->get();

        $recentInvoices = TenantInvoice::with('tenant:id,name')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('livewire.admin.saas-billing-dashboard', compact('subscriptions', 'coupons', 'plans', 'recentInvoices'))
            ->layout('layouts.app');
    }
}
