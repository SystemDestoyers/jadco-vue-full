<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Update user profile information
     */
    public function update(Request $request)
    {
        dd($request->all());
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string'
        ]);
        
        $user->update($validated);
        
        Log::info('User profile updated', ['user_id' => $user->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
    
    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        dd($request->all());
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            Log::warning('Failed password update attempt - incorrect current password', ['user_id' => $user->id]);
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }
        
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        Log::info('User password updated', ['user_id' => $user->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }
    
    /**
     * Update profile image
     */
    public function updateProfileImage(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'profile_image' => 'required|image|max:2048', // Max 2MB
        ]);
        
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store in public/images/profiles
            $path = $file->storeAs('profiles', $filename, 'public');
            
            // Update user's profile image path
            $user->profile_image = '/storage/' . $path;
            $user->save();
            
            Log::info('User profile image updated', ['user_id' => $user->id, 'path' => $path]);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile image updated successfully',
                'image_path' => $user->profile_image
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No image provided'
        ], 422);
    }
} 