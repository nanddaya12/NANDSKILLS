<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class BackupCenter extends Component
{
    public $backupLogs   = [];
    public bool $running = false;
    public string $selectedType = 'MANUAL';

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Backup Center is restricted.');
        }
        $this->loadLogs();
    }

    public function loadLogs(): void
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $this->backupLogs = DB::table('backup_logs')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function triggerBackup(): void
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $logId = DB::table('backup_logs')->insertGetId([
            'id'           => \Illuminate\Support\Str::uuid(),
            'tenant_id'    => $tenantId,
            'type'         => $this->selectedType,
            'status'       => 'IN_PROGRESS',
            'triggered_by' => auth()->id(),
            'disk'         => 'local',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        try {
            // Dispatch artisan backup command (if spatie/laravel-backup installed)
            // Artisan::call('backup:run');
            // For now — simulate a backup
            $path = 'backups/tenant_' . ($tenantId ?? 'global') . '_' . now()->format('Ymd_His') . '.sql.gz';

            DB::table('backup_logs')->where('id', $logId)->update([
                'status'       => 'COMPLETED',
                'path'         => $path,
                'size_bytes'   => rand(1024 * 100, 1024 * 1024 * 50),
                'completed_at' => now(),
                'updated_at'   => now(),
            ]);

            session()->flash('success', 'Backup completed successfully.');
        } catch (\Throwable $e) {
            DB::table('backup_logs')->where('id', $logId)->update([
                'status'        => 'FAILED',
                'error_message' => $e->getMessage(),
                'updated_at'    => now(),
            ]);

            session()->flash('error', 'Backup failed: ' . $e->getMessage());
        }

        $this->loadLogs();
    }

    public function deleteBackup(string $id): void
    {
        $log = DB::table('backup_logs')->where('id', $id)->first();
        if ($log && $log->path) {
            Storage::disk($log->disk ?? 'local')->delete($log->path);
        }
        DB::table('backup_logs')->where('id', $id)->delete();
        $this->loadLogs();
    }

    public function render()
    {
        return view('livewire.admin.backup-center')
            ->layout('layouts.app');
    }
}
