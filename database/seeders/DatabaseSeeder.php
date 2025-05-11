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
            'profile_photo' => 'profile_photos/default/default-profile-photo.svg',
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        User::factory(10)->withRole('user')->create();

        

        $this->call([
            CategorySeeder::class,
            StatusSeeder::class,
            DepartmentSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}
