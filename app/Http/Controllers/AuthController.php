<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
    * Create a login token for the user.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'The User does not exist',
            ], 401);
        }

        if (!Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'email' => $fields['email'],
                'message' => 'The password is incorrect'
            ], 401);
        }

        $profileImage = $user->profile;

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json([
                'message' => 'Hi, ' . $user->name,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'profile_image' => $profileImage
            ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out'
        ], 200);
    }

    /**
     * Log the user out of all devices, delete all tokens.
     */
    public function logoutAllDevices()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'You have been successfully logged out'
        ], 200);
    }

}
