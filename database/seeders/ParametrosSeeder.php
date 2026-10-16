<?php

namespace Database\Seeders;

use App\Models\Parametro;
use App\Models\ValorAciAno;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
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
                'descricao' => 'Valor ACI (ano corrente)',
                'valor' => '24,00',
                'area' => 'tesouraria',
                'tipo' => 'text'
            ],
            [
                'nome' => 'min_federacao',
                'descricao' => 'Porcentagem Mínima de Entrega (Federação)',
                'valor' => '60',
                'area' => 'estatistica',
                'tipo' => 'text'
            ],
            [
                'nome' => 'min_sinodal',
                'descricao' => 'Porcentagem Mínima de Entrega (Sinodal)',
                'valor' => '70',
                'area' => 'estatistica',
                'tipo' => 'text'
            ],
            [
                'nome' => 'min_aci',
                'descricao' => 'Porcentagem de ACI mínima(por sócio das federações ativas)',
                'valor' => '35',
                'area' => 'tesouraria',
                'tipo' => 'text'
            ]
        ];

        app(GamificacaoConfiguracaoService::class)->semearParametros();

        DB::beginTransaction();
        try {
            foreach ($parametros as $parametro) {
                Parametro::firstOrCreate(['nome' => $parametro['nome']],$parametro);
            }

            $anoReferencia = (int) (Parametro::where('nome', 'ano_referencia')->first()?->valor ?? 0);
            $valorAci = Parametro::where('nome', 'valor_aci')->first()?->valor;
            if ($anoReferencia > 0 && $valorAci !== null) {
                ValorAciAno::firstOrCreate(
                    ['ano' => $anoReferencia],
                    ['valor' => ValorAciAno::parseValor($valorAci)]
                );
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }
}
