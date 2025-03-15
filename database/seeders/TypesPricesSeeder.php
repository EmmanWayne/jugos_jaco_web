<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypesPricesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('types_prices')->insert([
            [
                'name' => 'Precio Regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Precio Mayorista',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Precio VIP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
