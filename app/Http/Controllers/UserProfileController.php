<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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
        try {
            // Manually validate the received data
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'username' => 'sometimes|string|unique:users,username,' . $request->user()->id,
                'profile_photo' => 'sometimes|image|max:2048', // Max 2MB
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = $request->user();

            // Manually assign the validated data
            $data = $validator->validated();
            $user->fill($data);

            // If an image is uploaded
            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->profile_photo = $path;
            }

            // If the username is changed, reset verification
            //if (isset($data['username']) && $user->isDirty('username')) {
            //    $user->email_verified_at = null;
            //}

            $user->save();

            return response()->json(['status' => 'profile-updated']);
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
