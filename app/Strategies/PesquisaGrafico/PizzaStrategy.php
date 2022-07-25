<?php 

namespace App\Strategies\PesquisaGrafico;

use App\Interfaces\PesquisaGraficoStrategy;
use App\Models\Pesquisa;
use App\Models\PesquisaConfiguracao;
use Illuminate\Support\Facades\Log;

class PizzaStrategy extends AbstractGrafico implements PesquisaGraficoStrategy
{

    public function handle(Pesquisa $pesquisa, string $campo, string $chave) : array
    {
        try {
            $dados = self::getDados($pesquisa, $campo, $chave);
            return [
                'html' => self::renderizarHtml($dados),
                'js' => self::script($dados)
            ];
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
        }
    }

    public static function getDados(Pesquisa $pesquisa, string $campo, string $chave) : array
    {
        try {
            $dados = array();
            $configuracao = $pesquisa->configuracao->configuracao[$chave];
            $dados['campo'] = $configuracao['label'];
            $referencia = data_get($pesquisa->referencias, '*.'.$chave);
            $valores = collect(data_get($referencia, '*.valores'))->whereNotNull()->first();
            if (!$valores) {
                $valores = collect($referencia)->whereNotNull()->first();
                $resultados = $pesquisa->respostas()->whereNotNull('resposta->'.$campo)->get()->pluck('resposta.'.$campo)->countBy()->toArray();
                foreach ($resultados as $resposta => $quantidade) {
                    $dados['dados'][] = [
                        'quantidade' => $quantidade,
                        'label' => $resposta
                    ];
                }                
            } else {
                foreach ($valores as $valor) {
                    $dados['dados'][] = [
                        'quantidade' => $pesquisa->respostas()->whereJsonContains('resposta->'.$campo, $valor['value'])->count(),
                        'label' => $valor['label']
                    ];
                }
            }
            $tipo_dado = $configuracao['tipo_dado'];
            return self::formatarDados($dados, $tipo_dado);
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
                "datasets" => [
                    [
                        "label" => $dados['campo'],
                        "data" => $quantidades,
                        "backgroundColor" => self::getPaleta($dados),
                    ]
                ]
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



    public static function script(array $dados)
    {
        return view('dashboard.pesquisas.graficos.pizza.js', [
            'dados' => $dados
        ])->render();
    }

    public static function renderizarHtml(array $dados) : string
    {
        return view('dashboard.pesquisas.graficos.pizza.html', [
            'dados' => $dados
        ])->render();
    }

}