<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * BlockAdminOnPublicDomain
 *
 * Ensures that admin portals (Staff + Super Admin) are NEVER accessible
 * from the main public-facing domain.
 *
 * Rules:
 *  - Super Admin login → ONLY accessible from ADMIN_DOMAIN env var
 *  - Staff login       → ONLY accessible from a tenant subdomain (not the public root)
 *  - If wrong domain → abort 404 (don't leak that the route even exists)
 */
class BlockAdminOnPublicDomain
{
    public function handle(Request $request, Closure $next, string $portalType = 'staff'): Response
    {
        $host       = $request->getHost();
        $baseDomain = str_replace(['http://', 'https://'], '', env('APP_BASE_DOMAIN', 'localhost'));
        $adminHost  = str_replace(['http://', 'https://'], '', env('ADMIN_DOMAIN', 'admin.' . $baseDomain));

        // ── Local dev bypass ───────────────────────────────────────────────
        // In local development (127.0.0.1 / localhost), allow access to all
        // admin portals without domain enforcement. Set APP_ENV=production to
        // enforce strict domain rules in production.
        if (app()->environment('local', 'testing') && in_array($host, ['127.0.0.1', 'localhost'])) {
            return $next($request);
        }

        // ── Super Admin Portal ─────────────────────────────────────────────
        // Must ONLY be reachable from ADMIN_DOMAIN (e.g. admin.nandskills.com)
        if ($portalType === 'superadmin') {
            if ($host !== $adminHost) {
                abort(404); // Silent 404 — don't expose the URL exists
            }
            return $next($request);
        }

        // ── Staff Portal ───────────────────────────────────────────────────
        // Must ONLY be reachable from a TENANT subdomain, never from the
        // public base domain itself (e.g. nandskills.com/login should 404).
        if ($portalType === 'staff') {
            $isPublicRoot = ($host === $baseDomain || $host === $adminHost);
            if ($isPublicRoot) {
                abort(404); // Staff portal hidden from public root domain
            }
            return $next($request);
        }

        return $next($request);
    }
}
