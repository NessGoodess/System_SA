<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('statuses')->insert([
            ['name' => 'Recepcionado', 'key' => 'received', 'created_at' => $now],
            ['name' => 'En Tramite', 'key' => 'in_process', 'created_at' => $now],
            ['name' => 'En Firma', 'key' => 'in_signing', 'created_at' => $now],
            ['name' => 'Firmado', 'key' => 'in_signed', 'created_at' => $now],
            ['name' => 'Concluido', 'key' => 'completed', 'created_at' => $now],
            ['name' => 'Entregado', 'key' => 'delivered', 'created_at' => $now],
            ['name' => 'Archivado', 'key' => 'archived', 'created_at' => $now],
            ['name' => 'Cancelado', 'key' => 'cancelled', 'created_at' => $now],

        ]);
    }
}
