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
        $this->call([
            RolesAndPermissionsSeeder::class,
            CategorySeeder::class,
            StatusSeeder::class,
            DepartmentSeeder::class,
        ]);

        $admin = User::factory()->withRole('admin')->create([
            'name' => 'Administrador',
            'username' => 'Ness0024',
        ]);

        $directorRH = User::factory()->withRole('user')->create([
            'name' => 'Director de Recursos Humanos',
            'username' => 'DirectorRH',
            'department_id' => 1, // Direccion de Recursos Humanos
        ]);

        $subdirectorRH = User::factory()->withRole('user')->create([
            'name' => 'Subdirector de Recursos Humanos',
            'username' => 'SubdirectorRH',
            'department_id' => 2, // Subdireccion de Recursos Humanos
        ]);

        $directorNomina = User::factory()->withRole('user')->create([
            'name' => 'Director de Nomina Academica',
            'username' => 'DirectorNA',
            'department_id' => 3, // Direccion de Nomina Academica
        ]);
        $directorControlPresupuestal = User::factory()->withRole('user')->create([
            'name' => 'Director de Control Presupuestal e Inventarios',
            'username' => 'DirectorCPeI',
            'department_id' => 4, // Direccion de Control Presupuestal e Inventarios
        ]);
        $directorArchivo = User::factory()->withRole('user')->create([
            'name' => 'Director de Archivo',
            'username' => 'DirectorArchivo',
            'department_id' => 5, // Direccion de Archivo
        ]);

        // Ensure each user exists and the profile relationship is defined in the User model
        foreach ([$admin, $directorRH, $subdirectorRH, $directorNomina, $directorControlPresupuestal, $directorArchivo] as $user) {
            if ($user) {
                $user->profile()->create([
                    'profile_photo' => 'profile_photos/default/default-profile-photo.svg',
                ]);
            }
        }

        $this->call([

            DocumentSeeder::class,
        ]);
    }
}
