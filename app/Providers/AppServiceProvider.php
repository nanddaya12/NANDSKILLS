<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Observers\AuditObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind NotificationDispatcher as singleton
        $this->app->singleton(\App\Services\NotificationDispatcher::class);
        $this->app->singleton(\App\Services\ActivityLogger::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasPermission($ability)) {
                return true;
            }
        });

        // ── Enterprise Audit Observers ────────────────────────────────────
        // Attach AuditObserver to key models so all CRUD is automatically logged
        $modelsToAudit = [
            \App\Models\User::class,
            \App\Models\Admission::class,
            \App\Models\StudentProfile::class,
            \App\Models\Invoice::class,
            \App\Models\StudentFee::class,
            \App\Models\Course::class,
            \App\Models\Enrollment::class,
            \App\Models\ManagedDocument::class,
            \App\Models\ComplianceFramework::class,
            \App\Models\TenantSubscription::class,
        ];

        foreach ($modelsToAudit as $model) {
            if (class_exists($model)) {
                $model::observe(AuditObserver::class);
            }
        }
    }
}
