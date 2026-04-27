<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class B2BTokenMiddleware
{
    /**
     * Handle an incoming request by validating a simple B2B token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-B2B-Token') ?? $request->query('token');
        $expected = config('services.b2b.token');

        if (!$expected || $token !== $expected) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

