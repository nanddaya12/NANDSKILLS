<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected $commands = [
        Commands\BackupTenant::class,
        Commands\CacheWarm::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ── Daily automated backup for all tenants ────────────────────────
        $schedule->command('app:backup-tenant --type=SCHEDULED_DAILY')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/backup.log'));

        // ── Weekly full backup ────────────────────────────────────────────
        $schedule->command('app:backup-tenant --type=SCHEDULED_WEEKLY')
            ->weeklyOn(0, '03:00') // Sundays at 3 AM
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/backup.log'));

        // ── Cache warm-up every 30 minutes ────────────────────────────────
        $schedule->command('app:cache-warm')
            ->everyThirtyMinutes()
            ->withoutOverlapping();

        // ── Route & config cache (every day) ──────────────────────────────
        // Uncomment these in production:
        // $schedule->command('route:cache')->daily();
        // $schedule->command('config:cache')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
