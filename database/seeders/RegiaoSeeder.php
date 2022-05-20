<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegiaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('regioes')->insert([
            ['nome' => 'Norte'],
            ['nome' => 'Nordeste'],
            ['nome' => 'Centro-Oeste'],
            ['nome' => 'Sudeste'],
            ['nome' => 'Sul'],
        ]);
    }
}
