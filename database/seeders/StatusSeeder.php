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
            ['name' => 'En proceso', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Firmado', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Entregado', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
