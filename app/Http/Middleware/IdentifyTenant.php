<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = env('APP_BASE_DOMAIN', 'localhost');
        $tenant = null;

        // Clean up base domain for matching
        $baseDomainClean = str_replace(['http://', 'https://'], '', $baseDomain);

        if ($host !== $baseDomainClean && str_ends_with($host, '.' . $baseDomainClean)) {
            // Subdomain routing (e.g. tenant1.localhost)
            $subdomain = str_replace('.' . $baseDomainClean, '', $host);
            $tenant = Tenant::where('subdomain', $subdomain)->first();
        } else if ($host !== $baseDomainClean && $host !== '127.0.0.1' && $host !== 'localhost') {
            // Custom domain routing (e.g. customdomain.com)
            $tenant = Tenant::where('custom_domain', $host)->first();
        }

        // Local dev fallback: 127.0.0.1 or localhost → use the first active tenant
        if (!$tenant && in_array($host, ['127.0.0.1', 'localhost', $baseDomainClean])) {
            $tenant = Tenant::where('status', 'ACTIVE')->first();
        }

        if ($tenant) {
            if ($tenant->status === 'SUSPENDED') {
                abort(403, 'This portal has been suspended. Please contact support.');
            }
            app()->instance('currentTenant', $tenant);
            
            // Share currentTenant with all Blade views
            view()->share('currentTenant', $tenant);
        }

        return $next($request);
    }
}
