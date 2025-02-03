<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('Documents')->insert([
            ['title' => 'Nombre de Documento 1', 'description' => 'Descripción de Documento 1', 'category_id' => 1, 'status_id' => 1, 'created_by' => 1, 'received_date' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Nombre de Documento 2', 'description' => 'Descripción de Documento 2', 'category_id' => 2, 'status_id' => 2, 'created_by' => 1, 'received_date' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Nombre de Documento 3', 'description' => 'Descripción de Documento 3', 'category_id' => 3, 'status_id' => 3, 'created_by' => 1, 'received_date' => $now, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
