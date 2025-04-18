<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrador',
            'username' => 'Ness0024',
        ]);

        $admin->profile()->create([
            'profile_photo' => 'default-profile-photo.svg',
        ]);

        User::factory(10)->create();
        $this->call([
            CategorySeeder::class,
            StatusSeeder::class,
            DepartmentSeeder::class,
            DocumentSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);

    }
}
