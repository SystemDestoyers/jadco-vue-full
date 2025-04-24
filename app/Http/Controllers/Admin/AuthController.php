<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            return response()->json([
                'success' => true,
                'user' => Auth::user()
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
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