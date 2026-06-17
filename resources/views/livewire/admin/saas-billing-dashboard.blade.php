<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">💳 SaaS Billing & Subscriptions</h1>
            <p class="text-sm text-gray-500 mt-1">Monitor revenue, manage subscriptions and coupon codes.</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl p-5 shadow">
            <div class="text-2xl font-bold">₨{{ number_format($mrr) }}</div>
            <div class="text-xs mt-1 opacity-80">Monthly Recurring Revenue</div>
        </div>
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded-xl p-5 shadow">
            <div class="text-2xl font-bold">₨{{ number_format($arr) }}</div>
            <div class="text-xs mt-1 opacity-80">Annual Recurring Revenue</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-xl p-5 shadow">
            <div class="text-2xl font-bold">{{ $active }}</div>
            <div class="text-xs mt-1 opacity-80">Active Tenants</div>
        </div>
        <div class="bg-gradient-to-br from-red-400 to-red-600 text-white rounded-xl p-5 shadow">
            <div class="text-2xl font-bold">{{ $churned }}</div>
            <div class="text-xs mt-1 opacity-80">Churned Tenants</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
        @foreach(['overview' => '📊 Overview', 'subscriptions' => '📋 Subscriptions', 'coupons' => '🏷️ Coupons', 'invoices' => '🧾 Invoices'] as $tab => $label)
        <button wire:click="setTab('{{ $tab }}')"
            class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $activeTab === $tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- SUBSCRIPTIONS --}}
    @if($activeTab === 'subscriptions' || $activeTab === 'overview')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 dark:text-white">Tenant Subscriptions</h3>
            <input wire:model.live.debounce.300ms="searchQuery" type="text" placeholder="🔍 Search tenant…"
                class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white w-48">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Tenant</th>
                        <th class="px-4 py-3 text-left">Plan</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Monthly</th>
                        <th class="px-4 py-3 text-left">Renews</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($subscriptions as $sub)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $sub->tenant?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $sub->plan?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sub->status === 'ACTIVE' ? 'bg-green-100 text-green-700' : ($sub->status === 'CANCELLED' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $sub->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">₨{{ number_format($sub->plan?->monthly_price ?? 0) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400">{{ optional($sub->ends_at)->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($sub->status === 'ACTIVE')
                            <button wire:click="cancelSubscription('{{ $sub->id }}')" onclick="return confirm('Cancel this subscription?')"
                                class="text-xs px-3 py-1 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                Cancel
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No subscriptions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">{{ $subscriptions->links() }}</div>
    </div>
    @endif

    {{-- COUPONS --}}
    @if($activeTab === 'coupons')
    <div class="flex justify-end">
        <button wire:click="$set('isCouponFormOpen', true)"
            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">
            + Create Coupon
        </button>
    </div>

    @if($isCouponFormOpen)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-blue-200 dark:border-blue-800">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Create Discount Coupon</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Coupon Code *</label>
                <input wire:model="coupon_code" type="text" placeholder="SUMMER25"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono uppercase">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Name *</label>
                <input wire:model="coupon_name" type="text" placeholder="Summer 2025 Discount"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Discount Type</label>
                <select wire:model="coupon_type"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="PERCENTAGE">Percentage (%)</option>
                    <option value="FIXED">Fixed Amount (₨)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Value *</label>
                <input wire:model="coupon_value" type="number" step="0.01" min="0" placeholder="10"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Max Uses</label>
                <input wire:model="coupon_max_uses" type="number" min="1" placeholder="100"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Expires At</label>
                <input wire:model="coupon_expires_at" type="date"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button wire:click="saveCoupon" class="px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition">Create Coupon</button>
            <button wire:click="$set('isCouponFormOpen', false)" class="px-5 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm rounded-lg font-medium transition">Cancel</button>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($coupons as $coupon)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border {{ $coupon->is_active ? 'border-green-200 dark:border-green-800' : 'border-gray-200 dark:border-gray-700 opacity-60' }}">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <span class="font-mono font-bold text-lg text-blue-600 dark:text-blue-400">{{ $coupon->code }}</span>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $coupon->name }}</p>
                </div>
                <span class="text-2xl font-bold {{ $coupon->discount_type === 'PERCENTAGE' ? 'text-green-600' : 'text-indigo-600' }}">
                    {{ $coupon->discount_type === 'PERCENTAGE' ? $coupon->discount_value . '%' : '₨' . number_format($coupon->discount_value) }}
                </span>
            </div>
            <div class="text-xs text-gray-400 mb-3">
                Used: {{ $coupon->used_count }}/{{ $coupon->max_uses ?? '∞' }} &nbsp;|&nbsp;
                Expires: {{ $coupon->expires_at ?? 'Never' }}
            </div>
            <div class="flex gap-2">
                <button wire:click="toggleCoupon('{{ $coupon->id }}')"
                    class="flex-1 text-xs py-1.5 {{ $coupon->is_active ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }} rounded-lg font-medium transition">
                    {{ $coupon->is_active ? '⏸️ Disable' : '▶️ Enable' }}
                </button>
                <button wire:click="deleteCoupon('{{ $coupon->id }}')" onclick="return confirm('Delete this coupon?')"
                    class="text-xs py-1.5 px-3 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition">🗑️</button>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center text-gray-400 py-10">No coupons created yet.</div>
        @endforelse
    </div>
    @endif

    {{-- INVOICES --}}
    @if($activeTab === 'invoices')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Recent SaaS Invoices</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Invoice #</th>
                        <th class="px-4 py-3 text-left">Tenant</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentInvoices as $inv)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ substr($inv->id, 0, 8) }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $inv->tenant?->name ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">₨{{ number_format($inv->amount ?? 0) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $inv->status === 'PAID' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $inv->status ?? 'PENDING' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">{{ optional($inv->created_at)->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
