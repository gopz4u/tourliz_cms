<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request by validating API key OR Bearer token.
     * Allows API access with:
     * 1. Static API key (no login required) - Set API_KEY in .env
     * 2. Bearer token from login - Use /api/auth/login
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        $apiKeyHeader = $request->header('X-API-Key');
        $apiKeyQuery = $request->query('api_key');
        
        // Check if Bearer token is provided (from login)
        if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
            // Let Sanctum handle Bearer token authentication
            // This middleware will pass through and Sanctum will validate
            return $next($request);
        }
        
        // Check for API key
        $apiKey = $apiKeyHeader ?? $apiKeyQuery;
        $expectedKey = config('services.api_key');

        // If no API key is configured, allow access (for development)
        // Or if API key matches, allow access
        if (!$expectedKey || ($apiKey && $apiKey === $expectedKey)) {
            return $next($request);
        }

        // If API key is required but not provided or invalid
        if ($expectedKey && (!$apiKey || $apiKey !== $expectedKey)) {
            return response()->json([
                'message' => 'API key required. Provide X-API-Key header or api_key query parameter.',
                'error' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}

