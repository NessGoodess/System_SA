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
        $user = $request->user();

        $userData = [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'profile_photo' => $user->profile_photo_url,
        ];

        return response()->json($userData, 200);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
                'new_password' => 'sometimes|string|min:8|confirmed',
                'current_password' => 'required_with:new_password',
                'profile_photo' => 'sometimes|image|max:2048',
            ], [
                'name.max' => 'El nombre no puede tener más de 255 caracteres.',
                'username.max' => 'El nombre de usuario no puede tener más de 255 caracteres.',
                'username.unique' => 'El nombre de usuario ya está en uso.',
                'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
                'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
                'current_password.required_with' => 'Debes ingresar la contraseña actual para cambiar la contraseña.',
                'profile_photo.image' => 'La foto de perfil debe ser una imagen.',
                'profile_photo.max' => 'La foto de perfil no debe superar los 2MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $changesMade = false;

            if ($request->has('name')) {
                $user->name = $data['name'];
                $changesMade = true;
            }

            if ($request->has('username')) {
                $user->username = $data['username'];
                $changesMade = true;
            }

            if ($request->has('new_password')) {
                if (!Hash::check($data['current_password'], $user->password)) {
                    return response()->json([
                        'error' => 'La contraseña actual no es correcta.'
                    ], 422);
                }
                $user->password = Hash::make($data['new_password']);
                $changesMade = true;
            }

            if ($request->hasFile('profile_photo')) {
                $profile = $user->profile ?? $user->profile()->create();
                $profile->updatePhoto($request->file('profile_photo'));
                $changesMade = true;
            }

            if ($changesMade) {
                $user->save();
                return response()->json([
                    'status' => 'profile-updated',
                    'user' => [
                        'name' => $user->name,
                        'username' => $user->username,
                        'profile_photo' => $user->profile_photo_url
                    ],
                ], 200);
            } else {
                return response()->json([
                    'status' => 'no-changes',
                    'message' => 'No hay cambios para actualizar.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
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
