<?php

namespace App\Services;

use App\Helpers\FormHelper;
use App\Factories\PesquisaGraficoFactory;
use App\Models\Pesquisa;
use App\Models\PesquisaConfiguracao;
use App\Models\PesquisaResposta;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PesquisaService
{
    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $referencias = self::referenciaCamposFormulario($request->formulario);
            $pesquisa = Pesquisa::create([
                'nome' => $request->nome,
                'formulario' => $request->formulario,
                'referencias' => $referencias,
                'user_id' => Auth::id()
            ]);
            $pesquisa->usuarios()->sync($request->secretarios);
            PesquisaConfiguracao::create(self::templateConfiguracao($pesquisa));
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao salvar o formulário", 1);
        }
    }

    public static function update(Pesquisa $pesquisa, Request $request)
    {
        try {
            $referencias = self::referenciaCamposFormulario($request->formulario);
            $pesquisa->update([
                'nome' => $request->nome,
                'formulario' => $request->formulario,
                'referencias' => $referencias,
            ]);
            $pesquisa->usuarios()->sync($request->secretarios);
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao salvar o formulário", 1);
        }
    }

    public static function referenciaCamposFormulario(string $formulario)
    {
        $json_formulario = json_decode($formulario, true);
        $array_formulario = json_decode($json_formulario, true);
        $referencias = array();
        foreach ($array_formulario as $key => $campo) {
            if (in_array($campo['type'], ['button', 'paragraph','header'])) {
                continue;
            }
            $referencias[$key] = [
                $campo['name'] => [
                    'label' => $campo['label'],
                    'campo' => isset($campo['label']) ? Str::snake(FormHelper::removerAcentos($campo['label'])) : '',
                    'required' => $campo['required'] ?? false,
                ]
            ]; 
            if (!isset($campo['values'])) {
                continue;
            }
            foreach ($campo['values'] as $opcao) {
                $referencias[$key][$campo['name']]['valores'][] = [
                    'value' => $opcao['value'],
                    'label' => $opcao['label']
                ];       
            }
        }
        return $referencias;
    }

    public static function responder(Request $request)
    {
        try {
            $pesquisa = Pesquisa::findOrFail($request->pesquisa_id);
            $resposta = [];
            foreach ($pesquisa->referencias as $parametros) {
                foreach ($parametros as $campo => $opcoes) {
                    if ($request->has($campo)) {
                        $resposta[$opcoes['campo']] = $request->$campo;
                    } else {
                        $resposta[$opcoes['campo']] = null;
                    }
                }
            }
            PesquisaResposta::updateOrCreate(
                [
                    'pesquisa_id' => $pesquisa->id,
                    'user_id' => Auth::id(),    
                ],
                [
                    'pesquisa_id' => $pesquisa->id,
                    'user_id' => Auth::id(),
                    'resposta' => $resposta
                ]
            );

        } catch (Throwable $th) {
            throw new Exception('Erro ao Responder');
        }
    }

    public static function templateConfiguracao(Pesquisa $pesquisa) : array
    {        
        $configuracoes = array();
        $configuracoes['pesquisa_id'] = $pesquisa->id;
        foreach ($pesquisa->referencias as $parametros) {
            foreach ($parametros as $campo => $opcoes) {
                $configuracoes['configuracao'][$campo] = [
                    'label' => $opcoes['label'],
                    'campo' => $opcoes['campo'],
                    'tipo_grafico' => null,
                    'exportar' => false,
                    'tipo_dado' => PesquisaConfiguracao::QUANTIDADE
                ];
            }
        }
        return $configuracoes;
    }

    public static function setConfiguracoesPesquisa(Pesquisa $pesquisa, Request $request) 
    {
        try {
            $configuracoes = $pesquisa->configuracao->configuracao;
            $novas_configuracoes = [];
            foreach ($configuracoes as $campo => $configuracao) {
                $novas_configuracoes[$campo]['label'] = $configuracao['label'];
                $novas_configuracoes[$campo]['campo'] = $configuracao['campo'];
                $novas_configuracoes[$campo]['exportar'] = isset($request->configuracao[$campo]['exportar']) ? true : false;
                $novas_configuracoes[$campo]['tipo_grafico'] = $request->configuracao[$campo]['tipo_grafico'];
                $novas_configuracoes[$campo]['tipo_dado'] = $request->configuracao[$campo]['tipo_dado'];
            }
            $pesquisa->configuracao->update([
                'configuracao' => $novas_configuracoes
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao processar configurações", 1);
        }
    }

    public static function getGraficos(Pesquisa $pesquisa)
    {
        try {
            $graficos = array(); 
            foreach ($pesquisa->configuracao->configuracao as  $chave => $configuracao) {
                if (is_null($configuracao['tipo_grafico'])) {
                    continue;
                }
                $graficos[] = [
                    'tamanho' => PesquisaConfiguracao::TAMANHO[$configuracao['tipo_grafico']],
                    'grafico' => PesquisaGraficoFactory::make($configuracao['tipo_grafico'])->handle($pesquisa, $configuracao['campo'], $chave)
                ];
            }
            return $graficos;
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao processar configurações", 1);
        }

    }

    public static function getTotalizadores(Pesquisa $pesquisa)
    {
        try {
            $retorno = array(); 
            $i = 0;
            foreach ($pesquisa->configuracao->configuracao as $configuracao) {
                if (is_null($configuracao['tipo_dado'])) {
                    continue;
                }
                $valores_respostas =  $pesquisa->respostas->pluck('resposta.'.$configuracao['campo']);
                if (count($valores_respostas->toArray()) == count($valores_respostas->toArray(), COUNT_RECURSIVE)) {
                    $valores = $pesquisa->respostas->pluck('resposta.'.$configuracao['campo'])->countBy();
                } else {
                    $valores = $pesquisa->respostas->pluck('resposta.'.$configuracao['campo'])->collapse()->countBy();
                }
                $retorno[$i]['campo'] = $configuracao['label'];
                foreach ($valores as $opcao => $valor) {
                    $retorno[$i]['valores'][] = [
                        'label' => self::getLabelPeloCampo($pesquisa, $configuracao['campo'], $opcao),
                        'valor' => $valor
                    ];
                }
                $i++;
            }
            return $retorno;
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao processar configurações", 1);
        }
    }

    public static function getLabelPeloCampo(Pesquisa $pesquisa, string $campo, string $opcao) : string
    {

        try {
            
            $referencias = $pesquisa->referencias;
            foreach ($referencias as $referencia) {
                foreach ($referencia as $informacoes) {
                    if ($informacoes['campo'] != $campo) {
                        continue;
                    }
                    if (!isset($informacoes['valores'])) {
                        return $informacoes['label'];
                    }
                    foreach ($informacoes['valores'] as $valor) {
                        if ($valor['value'] == $opcao) {
                            return $valor['label'];
                        }
                    }
                }
            }
            return '';
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao processar configurações", 1);
        }
    }

}