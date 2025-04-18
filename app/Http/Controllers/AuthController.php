<?php

namespace App\Http\Controllers;

use App\Jobs\RecordActivities;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a login token for the user.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        try {
            $user = User::where('username', $fields['username'])->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'The User does not exist',
            ], 401);
        }

        if (!Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'username' => $fields['username'],
                'message' => 'The password is incorrect'
            ], 401);
        }

        $profileImage = $user->profile;

        if (!$user->hasRole('admin')) {
            RecordActivities::dispatchSync($user, 'login', null, 'Inicio de sesión');
        }
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
        $user = $request->user();

        $user->currentAccessToken()->delete();

        if (!$user->hasRole('admin')) {
            RecordActivities::dispatchSync($user, 'Logout', null, 'Cerro sesión');
        }

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
