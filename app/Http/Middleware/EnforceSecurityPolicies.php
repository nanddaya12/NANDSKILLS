<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnforceSecurityPolicies
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // 1. Concurrent Sessions Limit (Max 3 active sessions)
            // Querying sessions table from Laravel database session driver if active
            try {
                $sessions = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->orderBy('last_activity', 'desc')
                    ->get();

                $maxSessions = 3;
                if ($sessions->count() > $maxSessions) {
                    $keepIds = $sessions->take($maxSessions)->pluck('id')->toArray();
                    DB::table('sessions')
                        ->where('user_id', $user->id)
                        ->whereNotIn('id', $keepIds)
                        ->delete();
                }
            } catch (\Exception $e) {
                // Fail silently if sessions table is not using database driver in the current environment
            }

            // 2. Password Age Expiry Warning (90 days)
            // Check based on user created_at as fallback since password_updated_at is not standard
            if ($user->created_at && now()->diffInDays($user->created_at) > 90) {
                $routeName = $request->route() ? $request->route()->getName() : null;
                $allowedRoutes = ['settings', 'logout', 'auth.reset-password', 'auth.forgot-password'];
                
                if ($routeName && !in_array($routeName, $allowedRoutes) && !$request->is('settings*') && !$request->is('logout*')) {
                    session()->flash('warning', 'Security Policy Alert: Your password is older than 90 days. Please update your credentials in settings.');
                }
            }
        }

        return $next($request);
    }
}
