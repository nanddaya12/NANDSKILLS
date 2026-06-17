<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">🔐 Security Monitor</h2>
            <p class="text-sm text-gray-500 mt-0.5">Real-time threat detection & account security.</p>
        </div>
        <div class="flex gap-2">
            @foreach(['24h' => '24h', '7d' => '7 Days', '30d' => '30 Days'] as $key => $label)
            <button wire:click="setPeriod('{{ $key }}')"
                class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $selectedPeriod === $key ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <div class="text-3xl font-bold text-red-700 dark:text-red-400">{{ $failedLoginsToday }}</div>
            <div class="text-xs text-red-600 dark:text-red-500 mt-1">Failed Logins Today</div>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-4">
            <div class="text-3xl font-bold text-orange-700 dark:text-orange-400">{{ count($suspiciousUsers) }}</div>
            <div class="text-xs text-orange-600 mt-1">Suspicious Accounts</div>
        </div>
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <div class="text-3xl font-bold text-amber-700 dark:text-amber-400">{{ $lockedAccounts }}</div>
            <div class="text-xs text-amber-600 mt-1">Locked Accounts</div>
        </div>
    </div>

    {{-- Suspicious Users --}}
    @if(count($suspiciousUsers) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
            <span class="text-red-500 text-lg">⚠️</span>
            <h3 class="font-semibold text-gray-800 dark:text-white">Suspicious Login Attempts</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Email / IP</th>
                        <th class="px-4 py-3 text-left">Attempts</th>
                        <th class="px-4 py-3 text-left">Last Attempt</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($suspiciousUsers as $u)
                    <tr class="hover:bg-red-50 dark:hover:bg-red-900/10">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $u['email'] ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold">{{ $u['attempts'] ?? 0 }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $u['last_attempt'] ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <button class="text-xs px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                Block IP
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-6 text-center">
        <div class="text-4xl mb-2">✅</div>
        <p class="text-green-700 dark:text-green-400 font-medium">No suspicious activity detected in this period.</p>
    </div>
    @endif

    {{-- Failed Login Chart (bar representation) --}}
    @if(count($failedLoginsByHour) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Failed Logins by Hour</h3>
        <div class="flex items-end gap-1 h-32">
            @php $maxVal = max($failedLoginsByHour) ?: 1; @endphp
            @for($h = 0; $h < 24; $h++)
            @php $val = $failedLoginsByHour[$h] ?? 0; $pct = round(($val / $maxVal) * 100); @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full rounded-t transition-all {{ $val > 0 ? 'bg-red-400' : 'bg-gray-200 dark:bg-gray-700' }}"
                     style="height: {{ max($pct, 4) }}%"
                     title="{{ $h }}:00 — {{ $val }} failed"></div>
                <span class="text-xs text-gray-400" style="font-size:9px">{{ str_pad($h,2,'0',STR_PAD_LEFT) }}</span>
            </div>
            @endfor
        </div>
    </div>
    @endif

</div>
