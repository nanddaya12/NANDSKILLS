<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        then: function () {
            // Admin portals in a completely separate route file
            \Illuminate\Support\Facades\Route::middleware(['web', \App\Http\Middleware\IdentifyTenant::class])
                ->group(base_path('routes/admin.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->prepend(\App\Http\Middleware\IdentifyTenant::class);
        $middleware->web(append: [
            \App\Http\Middleware\EnforceSecurityPolicies::class,
        ]);

        // Register admin domain isolation middleware alias
        $middleware->alias([
            'admin.portal' => \App\Http\Middleware\BlockAdminOnPublicDomain::class,
            'subscription.limit' => \App\Http\Middleware\SubscriptionLimitMiddleware::class,
            'security.policies' => \App\Http\Middleware\EnforceSecurityPolicies::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
