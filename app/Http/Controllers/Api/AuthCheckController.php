<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthCheckController extends Controller
{
    /**
     * Check authentication status and provide debug information
     */
    public function checkAuth(Request $request)
    {
        $user = $request->user();
        $token = $request->bearerToken();
        
        // Get all headers for debugging
        $allHeaders = [];
        foreach ($request->headers->all() as $key => $value) {
            $allHeaders[$key] = $value;
        }
        
        // Log the request for debugging
        Log::info('Auth check debug info', [
            'authenticated' => !is_null($user),
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'token_exists' => !is_null($token),
            'token_length' => $token ? strlen($token) : 0,
            'token_preview' => $token ? substr($token, 0, 10) . '...' : null,
            'all_headers' => $allHeaders,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        return response()->json([
            'authenticated' => !is_null($user),
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'token_exists' => !is_null($token),
            'token_length' => $token ? strlen($token) : 0,
            'token_preview' => $token ? substr($token, 0, 10) . '...' : null,
            'headers' => [
                'authorization' => $request->header('Authorization'),
                'accept' => $request->header('Accept'),
                'content_type' => $request->header('Content-Type'),
            ],
            'all_headers' => $allHeaders,
        ]);
    }
}
