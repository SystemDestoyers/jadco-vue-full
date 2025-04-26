<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        Log::info('Admin login attempt', ['email' => $request->email]);
        
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            Log::info('Admin login successful', ['user' => Auth::user()->email]);
            
            return response()->json([
                'success' => true,
                'user' => Auth::user()
            ]);
        }

        Log::warning('Admin login failed', ['email' => $request->email]);
        
        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Log::info('Admin logout', ['user' => $user ? $user->email : 'unknown']);
        
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'user' => Auth::user()
            ]);
        }
        
        return response()->json([
            'user' => null
        ]);
    }
    
    /**
     * Check if the user is authenticated
     */
    public function checkAuth(Request $request)
    {
        return response()->json([
            'authenticated' => Auth::check()
        ]);
    }
} 