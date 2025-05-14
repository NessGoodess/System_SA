<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('categories')->insert([
            ['name' => 'Oficio', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Acta', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Expediente', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Informe', 'created_at' => $now, 'updated_at' => $now],
        ]);

    }
}
