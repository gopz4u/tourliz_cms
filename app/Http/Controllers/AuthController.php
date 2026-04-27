<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Get device information from request
     */
    private function getDeviceInfo(Request $request)
    {
        // Ensure session is available before accessing it
        $deviceId = null;
        if ($request->hasSession()) {
            $deviceId = $request->session()->get('device_id');
        }
        
        if (!$deviceId) {
            $deviceId = Str::uuid()->toString();
        }
        
        return [
            'device_name' => $request->header('User-Agent', 'Unknown Device'),
            'device_id' => $deviceId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now()->toDateTimeString(),
        ];
    }
    /**
     * Show the login form
     */
    public function showLoginForm(Request $request)
    {
        // Ensure session is started before rendering the view
        // This ensures CSRF token is properly generated
        if (!$request->hasSession()) {
            $request->session()->start();
        }
        
        return view('landing');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Ensure session is available before validation
        if (!$request->hasSession()) {
            $request->session()->start();
        }
        // Diagnostic logging: record login attempt (without password)
        try {
            Log::info('Login attempt', ['email' => $request->input('email'), 'ip' => $request->ip(), 'ua' => $request->userAgent()]);
        } catch (\Exception $e) {
            // swallow logging errors to avoid breaking auth flow
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Explicitly use the 'web' guard
        $attempt = Auth::guard('web')->attempt($credentials, $remember);
        if ($attempt) {
            try {
                Log::info('Login success', ['email' => $request->input('email'), 'id' => Auth::guard('web')->id()]);
            } catch (\Exception $e) {}
            // Get intended URL before any session operations
            $intended = $request->session()->pull('url.intended');
            
            // Get the authenticated user before regenerating session
            $user = Auth::guard('web')->user();
            
            // Regenerate session ID for security (prevents session fixation attacks)
            // This maintains the authenticated user state
            $request->session()->regenerate();
            
            // Verify and re-authenticate user after regenerate to ensure persistence
            if ($user && !Auth::guard('web')->check()) {
                Auth::guard('web')->login($user, $remember);
            }
            
            // Store device information in session for multiple device tracking
            // Multiple devices can be logged in simultaneously
            $deviceInfo = $this->getDeviceInfo($request);
            $request->session()->put('device_id', $deviceInfo['device_id']);
            $request->session()->put('device_info', $deviceInfo);
            
            // Redirect to intended URL or dashboard
            // The StartSession middleware will handle saving the session
            return redirect()->to($intended ?: route('admin.dashboard'));
        }

        try {
            Log::warning('Login failed', ['email' => $request->input('email')]);
        } catch (\Exception $e) {}

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Explicitly use the 'web' guard
        Auth::guard('web')->login($admin);
        
        // Regenerate session ID for security
        $request->session()->regenerate();
        
        // Ensure the authenticated user is properly stored in session
        $request->session()->put('auth.password_confirmed_at', time());
        
        // Store device information in session for multiple device tracking
        // Multiple devices can be logged in simultaneously
        $deviceInfo = $this->getDeviceInfo($request);
        $request->session()->put('device_id', $deviceInfo['device_id']);
        $request->session()->put('device_info', $deviceInfo);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}

