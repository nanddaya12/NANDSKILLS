<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🗄️ Backup & Disaster Recovery</h1>
            <p class="text-sm text-gray-500 mt-1">Manage database backups and recovery points for your institution.</p>
        </div>
        <div class="flex gap-3">
            <select wire:model="selectedType"
                class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="MANUAL">Manual Backup</option>
                <option value="SCHEDULED_DAILY">Daily Scheduled</option>
                <option value="SCHEDULED_WEEKLY">Weekly Scheduled</option>
            </select>
            <button wire:click="triggerBackup"
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition flex items-center gap-2">
                <svg wire:loading wire:target="triggerBackup" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span wire:loading.remove wire:target="triggerBackup">🚀 Run Backup Now</span>
                <span wire:loading wire:target="triggerBackup">Running…</span>
            </button>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl px-4 py-3 text-green-700 dark:text-green-400 text-sm">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl px-4 py-3 text-red-700 dark:text-red-400 text-sm">
        ❌ {{ session('error') }}
    </div>
    @endif

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-600 text-white rounded-xl p-5 shadow">
            <div class="text-3xl font-bold">{{ count(array_filter((array)$backupLogs, fn($l) => $l->status === 'COMPLETED')) }}</div>
            <div class="text-xs mt-1 opacity-80">Successful Backups</div>
        </div>
        <div class="bg-red-500 text-white rounded-xl p-5 shadow">
            <div class="text-3xl font-bold">{{ count(array_filter((array)$backupLogs, fn($l) => $l->status === 'FAILED')) }}</div>
            <div class="text-xs mt-1 opacity-80">Failed Backups</div>
        </div>
        <div class="bg-emerald-600 text-white rounded-xl p-5 shadow">
            <div class="text-3xl font-bold">
                {{ collect($backupLogs)->where('status', 'COMPLETED')->sortByDesc('created_at')->first()?->created_at ?? 'N/A' }}
            </div>
            <div class="text-xs mt-1 opacity-80">Last Backup</div>
        </div>
    </div>

    {{-- Backup Log Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Backup History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Date & Time</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Size</th>
                        <th class="px-4 py-3 text-left">Path</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($backupLogs as $log)
                    @php
                        $statusColor = match($log->status) {
                            'COMPLETED'  => 'bg-green-100 text-green-700',
                            'FAILED'     => 'bg-red-100 text-red-700',
                            'IN_PROGRESS'=> 'bg-blue-100 text-blue-700',
                            default      => 'bg-gray-100 text-gray-700',
                        };
                        $sizeMB = $log->size_bytes ? round($log->size_bytes / (1024*1024), 2) . ' MB' : '—';
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">{{ $log->created_at }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', $log->type) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">{{ $log->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $sizeMB }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400 font-mono truncate max-w-xs">{{ $log->path ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="deleteBackup('{{ $log->id }}')" onclick="return confirm('Delete this backup record?')"
                                class="text-xs px-2 py-1 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition">
                                🗑️ Remove
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            No backup history yet. Run your first backup above.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Backup Schedule Info --}}
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-5">
        <h3 class="font-semibold text-amber-800 dark:text-amber-400 mb-2">📌 Backup Recommendations</h3>
        <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1">
            <li>• Schedule daily automated backups via <code class="bg-amber-100 px-1 rounded">php artisan schedule:run</code></li>
            <li>• Keep at least 30 days of backup history on remote storage (S3 / MinIO)</li>
            <li>• Test disaster recovery quarterly by restoring to a staging environment</li>
            <li>• Store encryption keys separately from backup files</li>
        </ul>
    </div>

</div>
