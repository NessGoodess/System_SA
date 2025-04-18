<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,

                'new_password' => 'sometimes|string|min:8|confirmed',
                'current_password' => 'sometimes|required_with:new_password|string|min:8',

                'profile_photo' => 'sometimes|image|max:2048', // Max 2MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $user->fill($data);

            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->profile_photo = $path;
            }

            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->profile_photo = $path;
            }

            if ($request->has('new_password')) {
                if (!Hash::check($request->input('current_password'), $user->password)) {
                    return response()->json([
                        'error' => 'Current password is incorrect.'
                    ], 422);
                }
                $user->password = Hash::make($request->input('new_password'));
            }

            if ($request->has('name')) {
                $user->name = $request->input('name');
            }
            if ($request->has('username')) {
                $user->username = $request->input('username');
            }

            if ($user->isDirty()) {
                $user->save();
            }

            return response()->json([
                'status' => 'profile-updated',
                'user' => $user->only(['name', 'username', 'profile_photo']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update profile: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->tokens()->delete(); // Revoke all tokens

        $user->delete();

        return response()->json(['status' => 'account-deleted']);
    }
}
