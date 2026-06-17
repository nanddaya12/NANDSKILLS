<div class="space-y-8 text-slate-800 font-sans">
    <!-- Stats Cards Header -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Tenants</span>
            <div class="flex items-baseline justify-between mt-4">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalTenants }}</span>
                <span class="text-xs font-bold text-emerald-600">All active</span>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Monthly Revenue</span>
            <div class="flex items-baseline justify-between mt-4">
                <span class="text-3xl font-extrabold text-slate-900">${{ number_format($monthlyRevenue, 2) }}</span>
                <span class="text-xs font-bold text-emerald-600">This Month</span>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Users</span>
            <div class="flex items-baseline justify-between mt-4">
                <span class="text-3xl font-extrabold text-slate-900">{{ number_format($activeUsers) }}</span>
                <span class="text-xs font-bold text-blue-600">Global count</span>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">System Health</span>
            <div class="flex items-baseline justify-between mt-4">
                <span class="text-3xl font-extrabold text-emerald-600">99.9%</span>
                <span class="text-xs font-bold text-slate-400">All systems OK</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Tenants Table Panel -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800">Registered Tenant Academies</h2>
                <a href="/register" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-xs font-semibold text-white transition-all shadow-md shadow-blue-500/10">Add Tenant</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs uppercase tracking-wider font-semibold text-slate-500">
                            <th class="py-3 px-4">Academy</th>
                            <th class="py-3 px-4">Subdomain</th>
                            <th class="py-3 px-4">Plan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tenants as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-4 font-semibold text-slate-900">{{ $item->name }}</td>
                            <td class="py-4 px-4">{{ $item->subdomain }}</td>
                            <td class="py-4 px-4">{{ $item->plan->name ?? 'None' }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 rounded-full text-xs border {{ $item->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <a href="/settings" class="text-xs text-blue-600 hover:underline font-semibold">Manage</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">No tenants registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Revenue Chart Visualizer -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6 flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Platform Revenue Growth</h2>
                
                <div class="h-64 flex items-end gap-3 pt-6 border-b border-slate-100">
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-blue-50 border border-blue-100 rounded-t-lg h-24 group hover:bg-blue-500 transition-all"></div>
                        <span class="text-[10px] text-slate-400">Mar</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-blue-50 border border-blue-100 rounded-t-lg h-36 group hover:bg-blue-500 transition-all"></div>
                        <span class="text-[10px] text-slate-400">Apr</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-blue-50 border border-blue-100 rounded-t-lg h-44 group hover:bg-blue-500 transition-all"></div>
                        <span class="text-[10px] text-slate-400">May</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-gradient-to-t from-blue-600 to-indigo-600 rounded-t-lg h-56 group hover:scale-105 transition-all"></div>
                        <span class="text-[10px] text-slate-700 font-bold">Jun</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center text-xs text-slate-500 mt-4">
                <span>Total growth since Q1</span>
                <span class="font-bold text-emerald-600">+24.5%</span>
            </div>
        </div>
    </div>
</div>
