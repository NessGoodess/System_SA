<?php

namespace App\Http\Controllers;

use App\Models\User;
use DragonCode\PrettyArray\Services\Formatters\Json;
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
        $users = User::role('user')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'profile_photo' => $user->profile_photo_url,
            ];
        });

        return response()->json($users, 200);
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
            'department_id' => 'nullable|exists:departments,id',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'username.required' => 'El nombre de usuario es obligatorio',
            'username.max' => 'El nombre de usuario no puede tener más de 255 caracteres',
            'username.unique' => 'El usuario ya existe',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'role.required' => 'El rol es obligatorio',
            'role.in' => 'El rol debe ser admin o user',
            'permissions.required' => 'Los permisos son obligatorios',
            'permissions.array' => 'Los permisos deben ser un array',
            'permissions.*.exists' => 'Uno de los permisos seleccionados no existe',
            'department_id.exists' => 'El departamento seleccionado no existe',
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
        // Assign department to the user
        if ($request->has('department_id')) {
            $user->department()->associate($request->input('department_id'));
            $user->save();
        }

        return response()
            ->json([
                'message' => 'User created successfully',
                'data' => $user->only(['id', 'name', 'username']),
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getPermissionNames(),
                'department' => $user->department ? [
                    'id' => $user->department->id,
                    'name' => $user->department->name,
                ] : null,
            ], 201);
    }

    /**
     * Display the specified user.
     */

    public function show(User $user)
    {
        return response()->json(
            [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'profile_photo' => $user->profile_photo_url,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getPermissionNames(),
                'department' => $user->department ? [
                    'id' => $user->department->id,
                    'name' => $user->department->name,
                ] : null,
            ],
            200
        );
    }

    public function edit(User $user)
    {
        $admin = auth()->user();
        if (!$admin->hasRole('admin')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getPermissionNames(),

        ], 200);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $admin = auth()->user();
        if (!$admin->hasRole('admin')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,

            'new_password' => 'sometimes|required|string|min:8|confirmed',
            'admin_password' => 'required_with:new_password|string',

            'role' => 'sometimes|required|string|exists:roles,name',
            'permissions' => 'sometimes|required|array',
            'permissions.*' => 'string|exists:permissions,name',
            'department_id' => 'sometimes|nullable|exists:departments,id',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'username.required' => 'El nombre de usuario es obligatorio',
            'username.max' => 'El nombre de usuario no puede tener más de 255 caracteres',
            'username.unique' => 'El usuario ya existe',
            'new_password.required' => 'La nueva contraseña es obligatoria',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide',
            'admin_password.required_with' => 'La contraseña de administrador es obligatoria cuando se cambia la contraseña',
            'role.required' => 'El rol es obligatorio',
            'role.exists' => 'El rol seleccionado no existe',
            'permissions.required' => 'Los permisos son obligatorios',
            'permissions.array' => 'Los permisos deben ser un array',
            'permissions.*.exists' => 'Uno de los permisos seleccionados no existe',
            'department_id.exists' => 'El departamento seleccionado no existe',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $updated = false;

        if ($request->has('new_password')) {

            if (!Hash::check($request->input('admin_password'), $admin->password)) {
                return response()->json([
                    'message' => 'Admin password is incorrect',
                    'errors' => [
                        'admin_password' => ['La contraseña de administrador es incorrecta']
                    ]
                ], 422);
            }
            $user->password = Hash::make($request->input('new_password'));
            $updated = true;
        }

        if ($request->has('name')) {
            $user->name = $request->input('name');
            $updated = true;
        }

        if ($request->has('username')) {
            $user->username = $request->input('username');
            $updated = true;
        }

        if ($request->has('department_id')) {
            $user->department_id = $request->input('department_id');
            $updated = true;
        }

        if ($updated) {
            $user->save();
        }

        if ($request->has('role')) {
            $user->syncRoles($request->input('role'));
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->input('permissions'));
        }

        // Cargar explícitamente la relación department
        $user->load('department');

        return response()->json([
            'message' => 'User updated successfully by admin',
            'data' => $user->only(['id', 'name', 'username']),
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getPermissionNames(),
            'department' => $user->department ? [
                'id' => $user->department->id,
                'name' => $user->department->name,
            ] : null,
        ], 200);
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

    /**
     * Display the authenticated user's notifications.
     */
    public function sendNotification()
    {
        $user = auth()->user();

        return response()->json([
            'notifications' => $user->notifications,
            'unread_notifications' => $user->unreadNotifications,
        ]);
    }
}
