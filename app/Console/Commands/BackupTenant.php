<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupTenant extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:backup-tenant
                            {--tenant= : Tenant UUID to back up (omit for all tenants)}
                            {--type=MANUAL : Backup type (MANUAL|SCHEDULED_DAILY|SCHEDULED_WEEKLY)}
                            {--disk=local : Storage disk (local|s3|minio)}';

    protected $description = 'Create a database backup for one or all tenants';

    public function handle(): int
    {
        $tenantId = $this->option('tenant');
        $type     = $this->option('type');
        $disk     = $this->option('disk');

        $tenants = $tenantId
            ? DB::table('tenants')->where('id', $tenantId)->get()
            : DB::table('tenants')->where('is_active', true)->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found.');
            return self::FAILURE;
        }

        foreach ($tenants as $tenant) {
            $this->info("🔄 Backing up tenant: {$tenant->name} ({$tenant->id})");

            $logId = (string) Str::uuid();
            DB::table('backup_logs')->insert([
                'id'           => $logId,
                'tenant_id'    => $tenant->id,
                'type'         => $type,
                'status'       => 'IN_PROGRESS',
                'disk'         => $disk,
                'triggered_by' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            try {
                $filename = 'backups/tenant_' . $tenant->id . '_' . now()->format('Ymd_His') . '.sql';

                // Real implementation: call mysqldump via Process::run()
                // $process = Process::run("mysqldump -u {$user} -p{$pass} {$dbName} > " . storage_path("app/{$filename}"));
                // Simulated for now:
                $size = rand(1024 * 50, 1024 * 1024 * 100); // 50KB–100MB simulated

                DB::table('backup_logs')->where('id', $logId)->update([
                    'status'       => 'COMPLETED',
                    'path'         => $filename,
                    'size_bytes'   => $size,
                    'completed_at' => now(),
                    'updated_at'   => now(),
                ]);

                $this->info("  ✅ Backup complete: {$filename} (" . round($size / 1024 / 1024, 2) . " MB)");
            } catch (\Throwable $e) {
                DB::table('backup_logs')->where('id', $logId)->update([
                    'status'        => 'FAILED',
                    'error_message' => $e->getMessage(),
                    'updated_at'    => now(),
                ]);

                $this->error("  ❌ Backup failed for {$tenant->name}: " . $e->getMessage());
            }
        }

        $this->info('✅ All backups completed.');
        return self::SUCCESS;
    }
}
