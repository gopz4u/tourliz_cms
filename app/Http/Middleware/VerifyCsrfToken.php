<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add API routes that don't need CSRF protection
        // 'api/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // Ensure session is started before CSRF verification
        if (!$request->hasSession()) {
            try {
                $request->session()->start();
            } catch (\Exception $e) {
                Log::error('Failed to start session in CSRF middleware: ' . $e->getMessage());
            }
        }

        return parent::handle($request, $next);
    }

    protected function tokensMatch($request)
    {
        // Ensure session is available
        if (!$request->hasSession()) {
            try {
                $request->session()->start();
            } catch (\Exception $e) {
                Log::error('Failed to start session: ' . $e->getMessage());
                return false;
            }
        }

        // Get token from various possible sources
        $token = $request->input('_token') 
            ?: $request->header('X-CSRF-TOKEN')
            ?: $request->header('X-XSRF-TOKEN');

        if (!$token) {
            Log::warning('CSRF token missing in request', [
                'url' => $request->url(),
                'method' => $request->method(),
                'has_session' => $request->hasSession(),
            ]);
            return false;
        }

        return parent::tokensMatch($request);
    }
}
