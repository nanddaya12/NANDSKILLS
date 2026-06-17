<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionLimitMiddleware
{
    public function handle(Request $request, Closure $next, string $limitType): Response
    {
        $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        if (!$tenant) {
            return $next($request);
        }

        $plan = $tenant->plan;
        if (!$plan) {
            return $next($request);
        }

        $exceeded = false;
        $message = '';

        switch ($limitType) {
            case 'users':
                $currentUsers = $tenant->users()->count();
                if ($currentUsers >= $plan->max_users) {
                    $exceeded = true;
                    $message = "You have reached the maximum limit of users ({$plan->max_users}) allowed by your current plan.";
                }
                break;

            case 'courses':
                $currentCourses = $tenant->courses()->count();
                if ($currentCourses >= $plan->max_courses) {
                    $exceeded = true;
                    $message = "You have reached the maximum limit of courses ({$plan->max_courses}) allowed by your current plan.";
                }
                break;

            case 'storage':
                $currentStorage = \App\Models\MediaFile::where('tenant_id', $tenant->id)->sum('file_size');
                if ($currentStorage >= $plan->max_storage_bytes) {
                    $exceeded = true;
                    $message = "You have reached the storage limit of " . number_format($plan->max_storage_bytes / (1024 * 1024), 2) . " MB allowed by your current plan.";
                }
                break;

            default:
                // Check feature activation in plan's features list
                $features = $plan->features ?? [];
                if (!in_array($limitType, $features)) {
                    $exceeded = true;
                    $message = "The requested module or feature '{$limitType}' is not enabled in your current subscription plan.";
                }
                break;
        }

        if ($exceeded) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Subscription Limit Exceeded',
                    'message' => $message,
                    'limit_type' => $limitType,
                ], 403);
            }

            return redirect()->route('billing')->with('error', $message);
        }

        return $next($request);
    }
}
