<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            // Check for general admin access (Super Admin or Employee)
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }

            // Check for Super Admin specifically
            if ($role === 'super_admin' && $user->isSuperAdmin()) {
                return $next($request);
            }

            // Check for Employee specifically
            if ($role === 'employee' && $user->isEmployee()) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        abort(403, 'You do not have permission to access this module.');
    }
}
