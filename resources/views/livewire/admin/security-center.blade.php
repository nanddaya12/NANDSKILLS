<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">🛡️ Security & Access Audit Center</h2>
            <p class="text-sm text-slate-500 mt-1">Real-time session monitoring, failed login tracking, and system audit logs.</p>
        </div>
        <span class="px-3 py-1 bg-rose-50 border border-rose-100 text-rose-700 text-xs font-semibold rounded-full flex items-center gap-1.5 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span> SYSTEM MONITORED
        </span>
    </div>

    <!-- Status Alerts -->
    @if (session()->has('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center gap-3 shadow-sm transition-all">
            <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <!-- Security KPI Summary cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Active Sessions</span>
            <h4 class="text-3xl font-bold text-slate-900 mt-2">{{ count($activeSessions) }}</h4>
            <span class="text-[10px] text-emerald-600 font-semibold mt-1 inline-block">● Real-time active devices</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Failed Logins</span>
            <h4 class="text-3xl font-bold text-rose-600 mt-2">{{ count($failedLogins) }}</h4>
            <span class="text-[10px] text-slate-500 mt-1 inline-block">Failed attempts audited</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Successful Logins</span>
            <h4 class="text-3xl font-bold text-blue-600 mt-2">{{ count($loginHistories) }}</h4>
            <span class="text-[10px] text-slate-500 mt-1 inline-block">Audit history window</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Audit Trails</span>
            <h4 class="text-3xl font-bold text-indigo-600 mt-2">{{ count($auditLogs) }}</h4>
            <span class="text-[10px] text-slate-500 mt-1 inline-block">Database mutations logged</span>
        </div>
    </div>

    <!-- Active Sessions & Failed Login Audit -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Active Sessions Section -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide">👥 Active User Sessions</h3>
                <span class="text-xs font-bold text-slate-400">Database Sessions Table</span>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[400px]">
                @forelse($activeSessions as $session)
                    <div class="p-5 flex justify-between items-start gap-4">
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-950 text-sm truncate">{{ $session->first_name }} {{ $session->last_name }}</span>
                                <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 rounded">ACTIVE NOW</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">{{ $session->email }}</p>
                            <div class="flex items-center gap-3 text-[10px] text-slate-400 font-semibold pt-1">
                                <span class="flex items-center gap-1">🌐 IP: {{ $session->ip_address }}</span>
                                <span class="truncate max-w-[200px]" title="{{ $session->user_agent }}">💻 {{ $session->user_agent }}</span>
                                <span>🕒 Last active: {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</span>
                            </div>
                        </div>
                        <button wire:click="revokeSession('{{ $session->id }}')" class="bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold px-3 py-1.5 rounded-xl border border-rose-200 transition-all shrink-0">
                            Revoke
                        </button>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-400 text-sm">No active user sessions found.</div>
                @endforelse
            </div>
        </div>

        <!-- Failed Login Attempts Section -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide">⚠️ Failed Login Log</h3>
                <span class="text-xs font-bold text-rose-500">Security Failures</span>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[400px]">
                @forelse($failedLogins as $failed)
                    <div class="p-5 flex justify-between items-start gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-800">{{ $failed->email }}</span>
                                <span class="px-2 py-0.5 text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-100 rounded">{{ $failed->reason }}</span>
                            </div>
                            <p class="text-xs text-slate-500">Portal: <span class="font-bold text-slate-700">{{ $failed->portal }}</span></p>
                            <div class="flex items-center gap-3 text-[10px] text-slate-400 font-semibold pt-1">
                                <span>🌐 IP: {{ $failed->ip_address }}</span>
                                <span class="truncate max-w-[200px]" title="{{ $failed->user_agent }}">💻 {{ $failed->user_agent }}</span>
                                <span>🕒 {{ $failed->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-400 text-sm">No failed login attempts logged.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Audit Logs & Audit Trails -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide">📋 Detailed System Audit Trails</h3>
            <span class="text-xs font-bold text-slate-400">Track database state mutations</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                        <th class="px-6 py-3.5">User</th>
                        <th class="px-6 py-3.5">Action</th>
                        <th class="px-6 py-3.5">Entity</th>
                        <th class="px-6 py-3.5">IP Address</th>
                        <th class="px-6 py-3.5">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($auditLogs as $audit)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900 text-xs">{{ $audit->user ? ($audit->user->first_name . ' ' . $audit->user->last_name) : 'System / Guest' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $audit->user ? $audit->user->email : '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded {{ str_contains($audit->action, 'delete') || str_contains($audit->action, 'removed') ? 'bg-rose-50 text-rose-700' : (str_contains($audit->action, 'create') || str_contains($audit->action, 'add') ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700') }}">
                                    {{ $audit->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-xs font-semibold text-slate-700">{{ $audit->entity }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">ID: {{ substr($audit->entity_id, 0, 8) }}...</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $audit->ip_address }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400 font-medium">{{ $audit->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-sm">No system audit logs recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
