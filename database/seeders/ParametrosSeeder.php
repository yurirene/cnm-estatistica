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
                'valor' => 'SIM',
                'area' => 'estatistica',
                'tipo' => 'switch'
            ],
            [
                'nome' => 'ano_referencia',
                'descricao' => 'Ano Referência',
                'valor' => '2022',
                'area' => 'estatistica',
                'tipo' => 'text'
            ],
            [
                'nome' => 'valor_aci',
                'descricao' => 'Valor ACI',
                'valor' => '24,00',
                'area' => 'tesouraria',
                'tipo' => 'text'
            ]
        ];
        DB::beginTransaction();
        try {
            foreach ($parametros as $parametro) {
                Parametro::updateOrCreate(['nome' => $parametro['nome']],$parametro);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }
}
