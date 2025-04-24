<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        Log::info('Login attempt', ['email' => $request->email]);
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Login validation failed', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            Log::warning('User not found', ['email' => $request->email]);
            return response()->json(['error' => 'User not found with this email'], 401);
        }

        // Check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Password mismatch', ['email' => $request->email]);
            return response()->json(['error' => 'Password is incorrect'], 401);
        }

        // If we get here, credentials are valid
        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::error('Auth attempt failed despite valid credentials', ['email' => $request->email]);
            return response()->json(['error' => 'Authentication failed. Please try again.'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        Log::info('Login successful', ['user_id' => $user->id]);

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function profile(Request $request)
    {
        return response()->json(['data' => $request->user()]);
    }
} 