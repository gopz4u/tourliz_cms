<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Test endpoint to verify token authentication is working
     * 
     * This endpoint requires authentication and returns information
     * about the authenticated user and token validity
     */
    public function testAuth(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'message' => 'Authentication successful!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token_info' => [
                'authenticated' => true,
                'token_name' => $request->user()->currentAccessToken()->name ?? 'Unknown',
                'token_abilities' => $request->user()->currentAccessToken()->abilities ?? [],
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Public test endpoint (no authentication required)
     */
    public function testPublic()
    {
        return response()->json([
            'success' => true,
            'message' => 'Public endpoint is accessible',
            'timestamp' => now()->toISOString(),
        ]);
    }
}

