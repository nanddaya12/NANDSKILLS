<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\SuperAdminLogin;
use App\Livewire\Auth\StaffLogin;

/*
|--------------------------------------------------------------------------
| Admin Portal Routes — ISOLATED FROM PUBLIC SITE
|--------------------------------------------------------------------------
|
| These routes are protected by the BlockAdminOnPublicDomain middleware.
|
| Super Admin → /superadmin/login
|   - ONLY accessible from ADMIN_DOMAIN env variable
|   - Completely isolated dark-theme layout, noindex
|   - No links to public CMS, student portal, or any other URL
|
| Staff / Tenant Admin → /login
|   - Only accessible from a TENANT subdomain (not the public root domain)
|   - Cannot be reached from nandskills.com directly in production
|   - Isolated staff layout, noindex, no public site links
|
| Domain enforcement is bypassed in local/testing environments
| (APP_ENV=local) so you can test on 127.0.0.1:8000 normally.
|
*/

// ── 1. SUPER ADMIN PORTAL ────────────────────────────────────────────────
// Middleware: admin.portal:superadmin → only reachable from ADMIN_DOMAIN
Route::middleware(['guest', 'admin.portal:superadmin'])
    ->group(function () {
        Route::get('/superadmin/login', SuperAdminLogin::class)
            ->name('superadmin.login');
    });

// ── 2. STAFF / TENANT ADMIN PORTAL ──────────────────────────────────────
// Middleware: admin.portal:staff → blocked on public root domain
// Only reachable via tenant subdomains (tenant1.nandskills.com/login)
// Named 'login' so Laravel's auth guard redirects here by default.
Route::middleware(['guest', 'admin.portal:staff'])
    ->group(function () {
        Route::get('/login', StaffLogin::class)->name('login');
    });
