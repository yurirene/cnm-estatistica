<?php

namespace App\Strategies\PesquisaGrafico;

use App\Interfaces\PesquisaGraficoStrategy;
use App\Models\Pesquisas\Pesquisa;
use App\Models\PesquisaConfiguracao;
use Illuminate\Support\Facades\Log;

class BarrasStrategy extends AbstractGrafico implements PesquisaGraficoStrategy
{

    public function handle(Pesquisa $pesquisa, string $campo, string $chave) : array
    {
        try {
            $dados = self::getDados($pesquisa, $campo, $chave);
            $dados_formatados = self::formatarDados($dados['dados'], $dados['tipo_dados']);
            return [
                'html' => self::renderizarHtml($dados_formatados, 'barra'),
                'js' => self::script($dados_formatados, 'barra')
            ];
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
        }
    }


    public static function formatarDados(array $dados, string $tipo_dado) : array
    {

        try {
            $quantidades = data_get($dados['dados'], '*.quantidade');
            if ($tipo_dado == PesquisaConfiguracao::PORCENTAGEM) {
                $quantidades = self::gerarPorcentagem($quantidades);
            }

            $retorno = array();
            $retorno = [
                "labels" => data_get($dados['dados'], '*.label'),
                "datasets" => []
            ];
            $retorno['datasets'][] = [
                'borderRadius' => 4,
                "label" => $dados['campo'],
                "data" => $quantidades,
                "backgroundColor" => self::getPaleta($dados),
            ];
            return $retorno;
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
        }
    }
}
