<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🗄️ Audit & Activity Monitor</h1>
            <p class="text-sm text-gray-500 mt-1">Track every action across your institution in real time.</p>
        </div>
        <div class="flex gap-3">
            <button wire:click="clearFilters" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition">
                ↺ Clear Filters
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-600 text-white rounded-xl p-4 shadow">
            <div class="text-3xl font-bold">{{ $todayCount }}</div>
            <div class="text-xs mt-1 opacity-80">Actions Today</div>
        </div>
        <div class="bg-indigo-600 text-white rounded-xl p-4 shadow">
            <div class="text-3xl font-bold">{{ $weekCount }}</div>
            <div class="text-xs mt-1 opacity-80">This Week</div>
        </div>
        <div class="bg-emerald-600 text-white rounded-xl p-4 shadow">
            <div class="text-3xl font-bold">{{ count($actionStats) }}</div>
            <div class="text-xs mt-1 opacity-80">Action Types</div>
        </div>
        <div class="bg-violet-600 text-white rounded-xl p-4 shadow">
            <div class="text-3xl font-bold">{{ count($topUsers) }}</div>
            <div class="text-xs mt-1 opacity-80">Active Users</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
        @foreach(['logs' => '📋 Activity Logs', 'stats' => '📊 Statistics', 'security' => '🔐 Security'] as $tab => $label)
            <button wire:click="setTab('{{ $tab }}')"
                class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $activeTab === $tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- LOGS TAB --}}
    @if($activeTab === 'logs')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        {{-- Filters --}}
        <div class="p-4 grid grid-cols-2 md:grid-cols-5 gap-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <input wire:model.live.debounce.400ms="searchUser" type="text" placeholder="🔍 Search user…"
                class="col-span-2 md:col-span-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <select wire:model.live="filterAction"
                class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">All Actions</option>
                @foreach($actions as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
            <input wire:model.live="filterModel" type="text" placeholder="Model type…"
                class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <input wire:model.live="dateFrom" type="date"
                class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <input wire:model.live="dateTo" type="date"
                class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Timestamp</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Action</th>
                        <th class="px-4 py-3 text-left">Model</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-left">IP / Device</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400 text-xs">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $log->user?->name ?? 'System' }}</div>
                            <div class="text-xs text-gray-400">{{ $log->role_name ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $colors = [
                                    'CREATE'   => 'bg-green-100 text-green-800',
                                    'UPDATE'   => 'bg-blue-100 text-blue-800',
                                    'DELETE'   => 'bg-red-100 text-red-800',
                                    'LOGIN'    => 'bg-indigo-100 text-indigo-800',
                                    'LOGOUT'   => 'bg-gray-100 text-gray-800',
                                    'APPROVE'  => 'bg-emerald-100 text-emerald-800',
                                    'FINANCIAL'=> 'bg-yellow-100 text-yellow-800',
                                    'EXAM'     => 'bg-purple-100 text-purple-800',
                                ];
                                $color = $colors[$log->action] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">{{ $log->action }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ $log->model_type ? class_basename($log->model_type) : '—' }}
                            <span class="text-gray-400">{{ $log->model_id ? '#'.substr($log->model_id, 0, 8) : '' }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300 max-w-xs truncate">
                            {{ $log->description ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                            <div>{{ $log->ip_address ?? '—' }}</div>
                            <div>{{ $log->device }} / {{ $log->browser }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            No activity logs found for the selected filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ $logs->links() }}
        </div>
    </div>
    @endif

    {{-- STATS TAB --}}
    @if($activeTab === 'stats')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Action Breakdown --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Action Breakdown</h3>
            <div class="space-y-3">
                @foreach($actionStats as $action => $count)
                @php $pct = $actionStats ? round(($count / max($actionStats)) * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $action }}</span>
                        <span class="text-gray-500">{{ number_format($count) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top Users --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Most Active Users</h3>
            <div class="space-y-3">
                @foreach($topUsers as $i => $user)
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold">{{ $i + 1 }}</span>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 dark:text-white text-sm">{{ $user['user'] }}</div>
                    </div>
                    <span class="text-gray-500 font-semibold text-sm">{{ number_format($user['total']) }}</span>
                </div>
                @endforeach
                @if(empty($topUsers))
                    <p class="text-gray-400 text-sm text-center py-4">No data yet.</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- SECURITY TAB --}}
    @if($activeTab === 'security')
    <livewire:audit.security-monitor />
    @endif

</div>
