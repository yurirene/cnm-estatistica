<?php

namespace Database\Seeders;

use App\Models\Parametro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parametros = [
            [
                'nome' => 'coleta_dados',
                'descricao' => 'Coleta de Dados',
                'valor' => 'SIM'
            ],
            [
                'nome' => 'valor_aci',
                'descricao' => 'Valor ACI',
                'valor' => '24,00'
            ]
        ];
        DB::beginTransaction();
        try {
            foreach ($parametros as $parametro) {
                Parametro::updateOrCreate($parametro);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }
}
