<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     * Force API routes to always expect JSON responses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Force API routes to accept JSON
        if ($request->is('api/*')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}

