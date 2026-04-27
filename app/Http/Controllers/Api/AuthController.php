<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Get device information from request
     */
    private function getDeviceInfo(Request $request)
    {
        $deviceName = $request->input('device_name', $request->header('X-Device-Name', 'Unknown Device'));
        $deviceId = $request->input('device_id', $request->header('X-Device-ID', Str::uuid()->toString()));
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        return [
            'device_name' => $deviceName,
            'device_id' => $deviceId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ];
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'device_name' => 'nullable|string|max:255',
            'device_id' => 'nullable|string|max:255',
        ]);

        $user = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $deviceInfo = $this->getDeviceInfo($request);
        $tokenName = $deviceInfo['device_name'] . ' - ' . ($deviceInfo['device_id'] ?? 'Device');
        
        $token = $user->createToken($tokenName)->plainTextToken;
        
        // Update token with device information
        $accessToken = $user->tokens()->latest()->first();
        if ($accessToken) {
            $accessToken->update([
                'device_name' => $deviceInfo['device_name'],
                'device_id' => $deviceInfo['device_id'],
                'ip_address' => $deviceInfo['ip_address'],
                'user_agent' => $deviceInfo['user_agent'],
            ]);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'device' => [
                'name' => $deviceInfo['device_name'],
                'id' => $deviceInfo['device_id'],
            ],
        ], 201);
    }

    /**
     * Log in user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string|max:255',
            'device_id' => 'nullable|string|max:255',
        ]);

        $user = Admin::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Multiple device login is enabled - do not revoke existing tokens
        // Users can be logged in from multiple devices simultaneously

        $deviceInfo = $this->getDeviceInfo($request);
        $tokenName = $deviceInfo['device_name'] . ' - ' . ($deviceInfo['device_id'] ?? 'Device');
        
        $token = $user->createToken($tokenName)->plainTextToken;
        
        // Update token with device information
        $accessToken = $user->tokens()->latest()->first();
        if ($accessToken) {
            $accessToken->update([
                'device_name' => $deviceInfo['device_name'],
                'device_id' => $deviceInfo['device_id'],
                'ip_address' => $deviceInfo['ip_address'],
                'user_agent' => $deviceInfo['user_agent'],
            ]);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'device' => [
                'name' => $deviceInfo['device_name'],
                'id' => $deviceInfo['device_id'],
            ],
        ]);
    }

    /**
     * Log out user
     */
    public function logout(Request $request)
    {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Refresh token (create new token and revoke old one)
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        $currentToken = $user->currentAccessToken();
        
        // Preserve device info from current token
        $deviceInfo = [
            'device_name' => $currentToken->device_name ?? 'Unknown Device',
            'device_id' => $currentToken->device_id ?? Str::uuid()->toString(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
        
        // Revoke current token
        $currentToken->delete();

        // Create new token with same device info
        $tokenName = $deviceInfo['device_name'] . ' - ' . ($deviceInfo['device_id'] ?? 'Device');
        $token = $user->createToken($tokenName)->plainTextToken;
        
        // Update token with device information
        $accessToken = $user->tokens()->latest()->first();
        if ($accessToken) {
            $accessToken->update([
                'device_name' => $deviceInfo['device_name'],
                'device_id' => $deviceInfo['device_id'],
                'ip_address' => $deviceInfo['ip_address'],
                'user_agent' => $deviceInfo['user_agent'],
            ]);
        }

        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $token,
            'device' => [
                'name' => $deviceInfo['device_name'],
                'id' => $deviceInfo['device_id'],
            ],
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function user(Request $request)
    {
        $currentToken = $request->user()->currentAccessToken();
        
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'created_at' => $request->user()->created_at,
            ],
            'current_device' => $currentToken ? [
                'name' => $currentToken->device_name,
                'id' => $currentToken->device_id,
                'ip_address' => $currentToken->ip_address,
                'last_used_at' => $currentToken->last_used_at,
            ] : null,
        ]);
    }

    /**
     * Get all active devices/sessions for the authenticated user
     */
    public function devices(Request $request)
    {
        $user = $request->user();
        $currentTokenId = $request->user()->currentAccessToken()->id;
        
        $devices = $user->tokens()->get()->map(function ($token) use ($currentTokenId) {
            return [
                'id' => $token->id,
                'name' => $token->device_name ?? $token->name,
                'device_id' => $token->device_id,
                'ip_address' => $token->ip_address,
                'user_agent' => $token->user_agent,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
                'is_current_device' => $token->id === $currentTokenId,
            ];
        });

        return response()->json([
            'devices' => $devices,
            'total_devices' => $devices->count(),
        ]);
    }

    /**
     * Revoke a specific device token
     */
    public function revokeDevice(Request $request, $tokenId)
    {
        $user = $request->user();
        
        $token = $user->tokens()->where('id', $tokenId)->first();
        
        if (!$token) {
            return response()->json([
                'message' => 'Token not found',
            ], 404);
        }

        $token->delete();

        return response()->json([
            'message' => 'Device logged out successfully',
        ]);
    }

    /**
     * Revoke all other devices (keep current device)
     */
    public function revokeOtherDevices(Request $request)
    {
        $user = $request->user();
        $currentTokenId = $user->currentAccessToken()->id;
        
        $revokedCount = $user->tokens()
            ->where('id', '!=', $currentTokenId)
            ->delete();

        return response()->json([
            'message' => 'All other devices logged out successfully',
            'revoked_count' => $revokedCount,
        ]);
    }
}

