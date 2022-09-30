<?php

namespace Database\Seeders;

use App\Models\TipoReuniao;
use Illuminate\Database\Seeder;

class TipoReuniaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $tipos = [
                1 => 'Congresso Nacional',
                2 => 'Comissão Executiva'
            ];

            foreach($tipos as $id => $tipo) {
                TipoReuniao::updateOrCreate(['id' => $id], [
                    'id' => $id,
                    'nome' => $tipo
                ]);
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
