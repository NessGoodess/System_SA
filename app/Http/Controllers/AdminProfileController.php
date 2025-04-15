<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    /**
     * Display a listing of the existing users.
     */
    public function index()
    {
        return response()->json(User::all(), 200);
    }

    /**
     * Store a newly created user session, and create a token for the user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,user',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $role = $request->input('role');
        $permissions = $request->input('permissions', []);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        // Assign role and permissions to the user
        $user->assignRole($role);
        if ($role === 'user' || !empty($permissions)) {
            // Sync permissions to ensure the user has the correct permissions
            $user->syncPermissions($permissions);
        }

        return response()
            ->json([
                'message' => 'User created successfully',
                'data' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getPermissionNames(),
            ], 201);
    }

    /**
     * Display the specified user.
     */

    public function show(User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}
