<div class="space-y-8">
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-900/60 p-6 rounded-2xl border border-white/5 gap-4 backdrop-blur-md">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Tenant Subscription Control</h2>
            <p class="text-slate-400 text-sm mt-1">Monitor resource utilization limits, manage package subscriptions, and download billing invoices.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs uppercase tracking-widest text-slate-400 font-bold bg-white/5 px-3 py-1.5 rounded-lg border border-white/10">Active Plan</span>
            <span class="px-4 py-2 rounded-xl bg-blue-600/20 text-blue-400 font-black text-sm border border-blue-500/30">
                {{ $currentPlan->name }}
            </span>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Usage Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Users Metric -->
        <div class="bg-slate-900/40 p-6 rounded-2xl border border-white/5 backdrop-blur-md flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">User Seats</h3>
                    <div class="flex items-baseline mt-2 gap-2">
                        <span class="text-3xl font-black text-white">{{ $stats['users']['current'] }}</span>
                        <span class="text-sm text-slate-500">/ {{ $stats['users']['max'] }}</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-600/10 rounded-xl text-blue-400 border border-blue-500/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="mt-6">
                <div class="flex justify-between text-xs text-slate-400 font-medium mb-2">
                    <span>Usage Percentage</span>
                    <span>{{ $stats['users']['percentage'] }}%</span>
                </div>
                <div class="w-full bg-slate-850 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['users']['percentage'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Courses Metric -->
        <div class="bg-slate-900/40 p-6 rounded-2xl border border-white/5 backdrop-blur-md flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Course Directories</h3>
                    <div class="flex items-baseline mt-2 gap-2">
                        <span class="text-3xl font-black text-white">{{ $stats['courses']['current'] }}</span>
                        <span class="text-sm text-slate-500">/ {{ $stats['courses']['max'] }}</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-600/10 rounded-xl text-purple-400 border border-purple-500/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>
            <div class="mt-6">
                <div class="flex justify-between text-xs text-slate-400 font-medium mb-2">
                    <span>Usage Percentage</span>
                    <span>{{ $stats['courses']['percentage'] }}%</span>
                </div>
                <div class="w-full bg-slate-850 rounded-full h-2">
                    <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['courses']['percentage'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Storage Metric -->
        <div class="bg-slate-900/40 p-6 rounded-2xl border border-white/5 backdrop-blur-md flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Cloud Storage</h3>
                    <div class="flex items-baseline mt-2 gap-2">
                        <span class="text-3xl font-black text-white">{{ $stats['storage']['current'] }} MB</span>
                        <span class="text-sm text-slate-500">/ {{ $stats['storage']['max'] }} MB</span>
                    </div>
                </div>
                <div class="p-3 bg-amber-600/10 rounded-xl text-amber-400 border border-amber-500/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                </div>
            </div>
            <div class="mt-6">
                <div class="flex justify-between text-xs text-slate-400 font-medium mb-2">
                    <span>Usage Percentage</span>
                    <span>{{ $stats['storage']['percentage'] }}%</span>
                </div>
                <div class="w-full bg-slate-850 rounded-full h-2">
                    <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['storage']['percentage'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Packages -->
    <div class="space-y-6">
        <h3 class="text-lg font-bold text-white tracking-wide">Available Subscription Tiers</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($plans as $plan)
                <div class="relative p-8 rounded-3xl border {{ $currentPlan->id === $plan->id ? 'border-blue-500 bg-blue-500/[0.02]' : 'border-white/5 bg-slate-900/40' }} backdrop-blur-md flex flex-col justify-between overflow-hidden">
                    @if($currentPlan->id === $plan->id)
                        <div class="absolute top-0 right-0 bg-blue-600 text-white text-[10px] font-black uppercase px-4 py-1 rounded-bl-2xl tracking-widest">
                            Current Plan
                        </div>
                    @endif

                    <div>
                        <h4 class="text-xl font-extrabold text-white">{{ $plan->name }}</h4>
                        <div class="mt-4 flex items-baseline gap-1">
                            <span class="text-4xl font-black text-white">${{ number_format($plan->price, 2) }}</span>
                            <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">/ {{ $plan->billing_interval }}</span>
                        </div>

                        <!-- Limits List -->
                        <ul class="mt-8 space-y-3.5">
                            <li class="flex items-center text-sm text-slate-300 gap-3">
                                <span class="text-emerald-400">✔</span>
                                <span>Up to <strong>{{ $plan->max_users }}</strong> active users</span>
                            </li>
                            <li class="flex items-center text-sm text-slate-300 gap-3">
                                <span class="text-emerald-400">✔</span>
                                <span>Up to <strong>{{ $plan->max_courses }}</strong> courses directories</span>
                            </li>
                            <li class="flex items-center text-sm text-slate-300 gap-3">
                                <span class="text-emerald-400">✔</span>
                                <span>Cloud storage allocation of <strong>{{ round($plan->max_storage_bytes / (1024 * 1024 * 1024), 2) }} GB</strong></span>
                            </li>
                            <li class="flex items-center text-sm text-slate-300 gap-3">
                                <span class="text-emerald-400">✔</span>
                                <span>Included Modules: <span class="text-slate-400 font-semibold">{{ implode(', ', array_map('strtoupper', $plan->features ?? [])) }}</span></span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/5">
                        @if($currentPlan->id === $plan->id)
                            <button disabled class="w-full py-3 bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded-xl font-bold text-sm tracking-wide cursor-not-allowed">
                                Active Subscription
                            </button>
                        @else
                            <button wire:click="changePlan('{{ $plan->id }}')" class="w-full py-3 bg-white hover:bg-slate-100 text-slate-900 rounded-xl font-bold text-sm tracking-wide transition-all shadow-md shadow-white/5">
                                Switch to {{ $plan->name }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tenant Billing History -->
    <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md">
        <h3 class="text-lg font-bold text-white mb-6 tracking-wide">Subscription Invoice Logs</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead>
                    <tr class="border-b border-white/5 text-xs uppercase tracking-wider font-bold text-slate-400">
                        <th class="py-3 px-4">Invoice #</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Stripe Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-4 px-4 font-bold text-white">{{ $invoice->invoice_number }}</td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-400">{{ $invoice->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-4 text-blue-400 font-extrabold">${{ number_format($invoice->amount, 2) }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">PAID</span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-500 font-mono">{{ $invoice->stripe_payment_id }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">No invoices logged.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
