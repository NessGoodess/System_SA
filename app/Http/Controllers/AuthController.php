<?php

namespace App\Http\Controllers;

use App\Jobs\RecordActivities;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{

    protected $tokenExpirationMinutes = 240; 

    /**
     * Create a login token for the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        try {
            $user = User::with('profile', 'roles')->where('username', $fields['username'])->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                //'message' => 'The User does not exist',
                'message' => 'El Usuario No existe',
            ], 401);
        }

        if (!Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'username' => $fields['username'],
                //'message' => 'The password is incorrect'
                'message' => 'La contraseña es incorrecta',
            ], 401);
        }

        // delete all previous tokens
        $user->tokens()->delete();

        //$token = $user->createToken('auth_token')->plainTextToken;

        $token = $user->createToken('auth_token', [
            'expires_at' => Carbon::now()->addMinutes($this->tokenExpirationMinutes),
        ])->plainTextToken;

        if (!$user->hasRole('admin')) {
            RecordActivities::dispatchSync($user, 'login', null, 'Inicio de sesión');
        }

        return response()->json([
            //'message' => 'Hi, ' . $user->name,
            'message' => 'Bienvenido, ' . $user->name,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->tokenExpirationMinutes * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'profile' => $user->profile ? [
                    'profile_photo' => $user->profile->profile_photo
                ] : null,
                'roles' => $user->roles->pluck('name')
            ]
        ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        if (!$user->hasRole('admin')) {
            RecordActivities::dispatchSync($user, 'Logout', null, 'Cerro sesión');
        }

        return response()->json([
            //'message' => 'successfully logged out',
            'message' => 'Cerro sesión con éxito',
        ], 200);
    }

    /**
     * Verifica el estado del token actual
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   public function checkToken(Request $request)
{
    // 1. Verificar si el token existe y es válido (pero puede estar expirado)
    if (!$request->bearerToken()) {
        return response()->json([
            'valid' => false,
            'message' => 'Token no proporcionado'
        ], 401);
    }

    try {
        // 2. Verificar el token manualmente
        $token = PersonalAccessToken::findToken($request->bearerToken());

        if (!$token) {
            return response()->json([
                'valid' => false,
                'message' => 'Token inválido'
            ], 401);
        }

        $user = $token->tokenable;

        // 3. Verificar expiración
        if ($token->expires_at && $token->expires_at->isPast()) {
            $token->delete();
            return response()->json([
                'valid' => false,
                'message' => 'Token expirado',
                'expired' => true // Bandera especial para el frontend
            ], 401);
        }

        return response()->json([
            'valid' => true,
            'expires_at' => $token->expires_a?->toDateTimeString(),
            'remaining_minutes' => $token->expires_at ? now()->diffInMinutes($token->expires_at) : null,
            'user' => $user->only(['id', 'name', 'username', 'email'])
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'valid' => false,
            'message' => 'Error al verificar token'
        ], 500);
    }
}

    /**
     * Log the user out of all devices, delete all tokens.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutAllDevices()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'You have been successfully logged out'
        ], 200);
    }
}
