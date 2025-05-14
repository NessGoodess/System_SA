<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $departments = [

            //Receivers
            ['name' => 'Direccion de Recursos Humanos', 'type' => 'receiver', 'description' => 'Gestiona todos los aspectos relacionados con los recursos humanos de la Universidad.', 'created_at' => $now],
            ['name' => 'Subdireccion de Recursos Humanos', 'type' => 'receiver', 'description' => 'Apoya en la gestion de los recursos humanos de la Universidad.', 'created_at' => $now],
            ['name' => 'Direccion de Nomina Academica', 'type' => 'receiver', 'description' => 'Gestiona la nomina del personal academico de la Universidad.', 'created_at' => $now],
            ['name' => 'Direccion de Control Presupuestal E Inventarios', 'type' => 'receiver', 'description' => 'Gestiona y controla los inventarios de la Universidad.', 'created_at' => $now],
            ['name' => 'Archivo', 'type' => 'receiver', 'description' => 'Gestiona y organiza los archivos y documentos de la Universidad.', 'created_at' => $now],

            //Senders
            ['name' => 'Secretaria De Finanzas', 'type' => 'sender', 'description' => 'Gestiona las finanzas de la Universidad.', 'created_at' => $now],
            ['name' => 'Facultad de Sistemas Biologicos E Inovacion Tegnologica', 'type' => 'sender', 'description' => 'Facultad que se enfoca en la innovación tecnológica y los sistemas biológicos.', 'created_at' => $now],
            ['name' => 'Secretaria General', 'type' => 'sender', 'description' => 'Coordina las actividades generales de la Universidad.', 'created_at' => $now],
        ];


        DB::table('departments')->insert($departments);
    }
}
