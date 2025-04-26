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
            $user = Auth::user();
            
            // First delete any existing tokens for this user to prevent duplicates
            $user->tokens()->delete();
            
            // Create a new token with more details for debugging
            $token = $user->createToken('admin-token');
            $plainTextToken = $token->plainTextToken;
            
            // Log token creation for debugging
            Log::info('Admin token created', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'token_id' => $token->accessToken->id,
                'token_name' => $token->accessToken->name,
                'token_length' => strlen($plainTextToken),
                'token_preview' => substr($plainTextToken, 0, 10) . '...',
            ]);
            
            Log::info('Admin login successful', ['user' => $user->email]);
            
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $plainTextToken,
                'debug_info' => [
                    'token_id' => $token->accessToken->id,
                    'token_preview' => substr($plainTextToken, 0, 10) . '...'
                ]
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
        try {
            $user = $request->user();
            
            // Check if user is authenticated
            if ($user) {
                // Delete the current access token if it's not a TransientToken
                try {
                    $token = $request->user()->currentAccessToken();
                    if ($token && !($token instanceof \Laravel\Sanctum\TransientToken)) {
                        $token->delete();
                    }
                } catch (\Exception $e) {
                    Log::error('Error deleting token', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id ?? 'unknown'
                    ]);
                    // Continue with logout even if token deletion fails
                }
                
                Log::info('Admin logout', ['user' => $user->email]);
            } else {
                Log::warning('Logout attempt with no authenticated user');
            }
            
            // Always return success to client
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Exception during logout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return success anyway to ensure client can complete logout flow
            return response()->json([
                'success' => true
            ]);
        }
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        if ($request->user()) {
            return response()->json([
                'user' => $request->user()
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
            'authenticated' => !is_null($request->user())
        ]);
    }
} 