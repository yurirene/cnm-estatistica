<?php

namespace App\Strategies\PesquisaGrafico;

use App\Models\Pesquisas\Pesquisa;
use Exception;
use Illuminate\Support\Facades\Log;

abstract class AbstractGrafico
{
    protected const PALETA = [
        "#001219",
        "#03071E",
        "#370617",
        "#6A040F",
        "#9D0208",
        "#D00000",
        "#DC2F02",
        "#E85D04",
        "#F48C06",
        "#FAA307",
        "#FFBA08",
        "E9D8A6",
        "#E9D8A6",
        "#94D2BD",
        "#0A9396",
        "#005F73"
    ];



    protected static function getPaleta(array $dados) : array
    {
        try {
            $retorno = array();
            $i = 0;
            $cores = array();
            while ($i < count($dados['dados'])) {
                $selecionado = array_rand(self::PALETA);
                if (in_array(self::PALETA[$selecionado], $cores)) {
                    continue;
                } else {
                    $cores[] = self::PALETA[$selecionado];
                    $i++;
                }
            }
            return $cores;
        } catch (\Throwable $th) {
            throw new Exception("Erro no getPaleta", 1);

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
                $resultados = $pesquisa->respostas()
                    ->when(request()->has('filtro'), function($query) {
                        return $query->whereHas('usuario', function($sql) {
                            return $sql->whereHas(request()->filtro);
                        });
                    })
                    ->whereNotNull('resposta->'.$campo)
                    ->get()
                    ->pluck('resposta.'.$campo)
                    ->countBy()
                    ->toArray();
                foreach ($resultados as $resposta => $quantidade) {
                    $dados['dados'][] = [
                        'quantidade' => $quantidade,
                        'label' => $resposta
                    ];
                }
            } else {
                foreach ($valores as $valor) {
                    $dados['dados'][] = [
                        'quantidade' => $pesquisa->respostas()
                            ->when(request()->has('filtro'), function($query) {
                                return $query->whereHas('usuario', function($sql) {
                                    return $sql->whereHas(request()->filtro);
                                });
                            })
                            ->whereJsonContains('resposta->'.$campo, $valor['value'])
                            ->count(),
                        'label' => $valor['label']
                    ];
                }
            }

            $tipo_dado = $configuracao['tipo_dado'];
            return [
                'dados' => $dados,
                'tipo_dados' => $tipo_dado
            ];
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
        }
    }

    protected static function gerarPorcentagem(array $quantidades) : array
    {
        try {
            $total = array_sum($quantidades);
            $retorno = array();
            if ($total == 0) {
                return [0];
            }
            foreach ($quantidades as $quantidade) {
                $divisao = ($quantidade * 100) / $total;
                $retorno[] = round($divisao, 2);
            }
            return $retorno;
        } catch (\Throwable $th) {
            throw new Exception("Erro no gerarPorcentagem", 1);
        }
    }

    protected static function gerarPorcentagemComTotal(array $quantidades, $valor) : array
    {
        try {

            $total = array_sum($quantidades);
            if ($total == 0) {
                return [0];
            }
            return ($valor * 100) / $total;
        } catch (\Throwable $th) {
            throw new Exception("Erro no gerarPorcentagem", 1);
        }
    }

    public static function script(array $dados, string $tipo) : string
    {
        return view('dashboard.pesquisas.graficos.' . $tipo . '.js', [
            'dados' => $dados
        ])->render();
    }

    public static function renderizarHtml(array $dados, string $tipo) : string
    {
        return view('dashboard.pesquisas.graficos.' . $tipo . '.html', [
            'dados' => $dados
        ])->render();
    }

}
