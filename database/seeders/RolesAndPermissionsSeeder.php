<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create permissions
        Permission::create(['name' => 'create']);
        Permission::create(['name' => 'read']);
        Permission::create(['name' => 'update']);
        Permission::create(['name' => 'delete']);

        // Assign permissions to roles
        $adminRole->givePermissionTo([Permission::all()]);
        $userRole->givePermissionTo(['read']);

        // Assign roles to users
        //$user = User::find(1); // Assuming the first user is the admin
        //$user = User::where('email', 'admin@example.com')->first();
        //$user->assignRole($adminRole);
    }
}
