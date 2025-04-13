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
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'test@example.com',
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
