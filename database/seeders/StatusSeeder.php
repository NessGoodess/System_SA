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
            ['name' => 'Recepcionado', 'created_at' => $now],
            ['name' => 'En Tramite', 'created_at' => $now],
            ['name' => 'En Firma', 'created_at' => $now],
            ['name' => 'Concluido', 'created_at' => $now],
            ['name' => 'Entregado', 'created_at' => $now],
            ['name' => 'Cancelado', 'created_at' => $now],

        ]);
    }
}
