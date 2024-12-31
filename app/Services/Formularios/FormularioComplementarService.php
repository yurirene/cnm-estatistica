<?php

namespace App\Services\Formularios;

use App\Factories\PesquisaGraficoFactory;
use App\Helpers\FormHelper;
use App\Models\Federacao;
use App\Models\FormularioComplementarFederacao;
use App\Models\FormularioComplementarSinodal;
use App\Models\Local;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FormularioComplementarService
{
    public const TIPO_GRAFICO = [
        null => 'Sem Gráfico',
        'barras' => 'Barras',
        'linhas' => 'Linhas',
        'pizza' => 'Pizza',
        'polar' => 'Polar',
    ];

    public const QUANTIDADE = 1;
    public const PORCENTAGEM = 2;
    public const TIPO_DADO = [
        null => 'Não Aplicado',
        self::QUANTIDADE => 'Quantidade',
        self::PORCENTAGEM => 'Porcentagem'
    ];

    public const TAMANHO = [
        'barras' => '4',
        'linhas' => '5',
        'pizza' => '3',
        'polar' => '4',
    ];
    public const TIPO_FORMULARIO_FEDERACAO = "federacao";
    public const TIPO_FORMULARIO_SINODAL = "sinodal";
    public const TIPO_FORMULARIO_LOCAL = "local";
    public const CLASSES = [
        self::TIPO_FORMULARIO_SINODAL => FormularioComplementarSinodal::class,
        self::TIPO_FORMULARIO_FEDERACAO => FormularioComplementarFederacao::class
    ];

    public const CLASSES_INSTANCIAS = [
        self::TIPO_FORMULARIO_LOCAL => Local::class,
        self::TIPO_FORMULARIO_FEDERACAO => Federacao::class
    ];
    
    public const CLASSES_SERVICES_RELATORIO = [
        self::TIPO_FORMULARIO_LOCAL => [
            'classe' => FormularioLocalService::class,
            'metodo' => 'getFormularioLocal'
        ],
        self::TIPO_FORMULARIO_FEDERACAO => [
            'classe' => FormularioFederacaoService::class,
            'metodo' => 'getFormularioDaFederacao'
        ]
    ];

    public static function getFormularioComplementar(string $instanciaId, string $tipo): Model
    {
        $classe = self::CLASSES[$tipo];
        $campo = "{$tipo}_id";
        $formulario = $classe::where($campo, $instanciaId)->first();

        if ($formulario === null) {
            $formulario = $classe::create([
                $campo => $instanciaId
            ]);
        }

        return $formulario;
    }

    /**
     * Retorna os dados do formulário complementar sinodal
     * 
     * @param string $localId
     * 
     * @return FormularioComplementarFederacao|null
     */
    public static function getFormularioSinodal(
        string $instanciaId,
        string $tipo = FormularioComplementarService::TIPO_FORMULARIO_LOCAL
    ): ?Model {
        $classe = self::CLASSES_INSTANCIAS[$tipo];
        $instancia = $classe::find($instanciaId);
        $formulario = FormularioComplementarSinodal::where('sinodal_id', $instancia->sinodal_id)->first();

        $classeServiceFormulario = self::CLASSES_SERVICES_RELATORIO[$tipo]['classe'];
        $metodoServiceFormulario = self::CLASSES_SERVICES_RELATORIO[$tipo]['metodo'];
        $formularioInstancia = $classeServiceFormulario::$metodoServiceFormulario($instanciaId);

        if ($formularioInstancia != null) {
            $formulario->resposta = $formularioInstancia->campo_extra_sinodal;
        }

        return $formulario;
    }

    /**
     * Retorna os dados do formulário complementar da federação
     * 
     * @param string $localId
     * 
     * @return FormularioComplementarFederacao|null
     */
    public static function getFormularioFederacao(string $localId): ?Model
    {
        $local = Local::find($localId);
        $formulario = FormularioComplementarFederacao::where('federacao_id', $local->federacao_id)->first();
        $formularioLocal = FormularioLocalService::getFormularioLocal($localId);

        if ($formularioLocal != null) {
            $formulario->resposta = $formularioLocal->campo_extra_federacao;
        }

        return $formulario;
    }

    public static function update(string $id, array $request, string $tipo)
    {
        DB::beginTransaction();
        try {
            $referencias = self::referenciaCamposFormulario($request['formulario']);
            $request['formulario'] = self::padronizarFormulario($request['formulario']);
            $classe = self::CLASSES[$tipo];
            $formulario = $classe::find($id);
            $formulario->update([
                'formulario' => $request['formulario'],
                'referencias' => $referencias,
                'status' => $request['status'] == FormHelper::SWITCH_ON
            ]);
            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            Log::error('Erro ao atualizar o Formulário Complementar', [
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao salvar o formulário", 1);
        }
    }

    public static function referenciaCamposFormulario(string $formulario) : array
    {
        try {
            $json_formulario = json_decode($formulario, true);
            $array_formulario = json_decode($json_formulario, true);
            $referencias = [];

            foreach ($array_formulario as $key => $campo) {
                if (in_array($campo['type'], ['button', 'paragraph','header'])) {
                    continue;
                }

                $referencias[$key] = [
                    $campo['name'] => [
                        'label' => $campo['label'],
                        'campo' => isset($campo['label'])
                            ? Str::snake(FormHelper::removerAcentos($campo['label']))
                            : '',
                        'required' => $campo['required'] ?? false,
                        'description' => $campo['description'] ?? '',
                    ]
                ];
                
                if ($campo['type'] == 'number') {
                    $referencias[$key][$campo['name']]['min'] = $campo['min'] ?? 0;
                    $referencias[$key][$campo['name']]['max'] = $campo['max'] ?? null;
                }

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
        } catch (Throwable $th) {
            Log::error('Erro ao montar a referencia dos campos', [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function padronizarFormulario(string $formulario): string
    {
        $dados = json_decode(json_decode($formulario), true);
        
        foreach ($dados as $key => $campo) {
            if (isset($campo['type']) && $campo['type'] == 'header') {
                $dados[$key]['subtype'] = 'h3';
            }
        }
        
        return json_encode($dados);
    }

    public static function tratarResposta(array $request, array $referencias): array
    {        
        $resposta = [];

        try {
            foreach ($referencias as $parametros) {
                foreach ($parametros as $campo => $opcoes) {
                    if (!isset($request[$campo])) {
                        continue;
                    }

                    $resposta[$campo] = $request[$campo];
                }
            }   
        } catch (Throwable $th) {
            Log::error('Erro ao tratar a resposta', [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
        }

        return $resposta;
    }

    
    public static function tratarRespostasComplementaresSinodal(
        array $request,
        string $instanciaId,
        string $tipo = FormularioComplementarService::TIPO_FORMULARIO_LOCAL
    ): array {
        $formularios = self::getFormularioSinodal($instanciaId, $tipo);

        if ($formularios == null) {
            return [];
        }
        
        return self::tratarResposta($request, $formularios->referencias);
    }
    
    public static function tratarRespostasComplementaresFederacao(array $request, string $localId): array
    {
        $formularios = self::getFormularioFederacao($localId);

        if ($formularios == null) {
            return [];
        }
        
        return self::tratarResposta($request, $formularios->referencias);
    }


    public static function status(Model $formulario)
    {
        try {
            $formulario->update([
                'status' => !$formulario->status
            ]);
        } catch (Throwable $th) {
            Log::error('Erro ao atualizar status', [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception('Erro ao Responder');
        }
    }
    public static function templateConfiguracao(Model $formulario) : array
    {
        $configuracoes =[];
        foreach ($formulario->referencias as $parametros) {
            foreach ($parametros as $campo => $opcoes) {
                $configuracoes['configuracao'][$campo] = [
                    'label' => $opcoes['label'],
                    'campo' => $opcoes['campo'],
                    'tipo_grafico' => null,
                    'exportar' => false,
                    'tipo_dado' => self::QUANTIDADE
                ];
            }
        }
        return $configuracoes;
    }

    public static function setConfiguracoesPesquisa(Model $formulario, array $request)
    {
        try {
            $configuracoes = $formulario->configuracao->configuracao;
            $novas_configuracoes = [];
            foreach ($configuracoes as $campo => $configuracao) {
                $novas_configuracoes[$campo]['label'] = $configuracao['label'];
                $novas_configuracoes[$campo]['campo'] = $configuracao['campo'];
                $novas_configuracoes[$campo]['exportar'] = isset($request['configuracao'][$campo]['exportar']) ? true : false;
                $novas_configuracoes[$campo]['tipo_grafico'] = $request['configuracao'][$campo]['tipo_grafico'];
                $novas_configuracoes[$campo]['tipo_dado'] = $request['configuracao'][$campo]['tipo_dado'];
            }
            $formulario->configuracao->update([
                'configuracao' => $novas_configuracoes
            ]);
        } catch (Throwable $th) {
            Log::error('Erro ao setar a configuração da pesquisa', [
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao processar configurações pesquisa", 1);
        }
    }

    public static function getGraficos(Model $pesquisa)
    {
        try {
            $graficos = [];
            foreach ($pesquisa->configuracao->configuracao as  $chave => $configuracao) {
                if (is_null($configuracao['tipo_grafico'])) {
                    continue;
                }

                $graficos[] = [
                    'tamanho' => self::TAMANHO[$configuracao['tipo_grafico']],
                    'grafico' => PesquisaGraficoFactory::make($configuracao['tipo_grafico'])
                        ->handle($pesquisa, $configuracao['campo'], $chave)
                ];
            }
            return $graficos;
        } catch (Throwable $th) {
            Log::error('Erro ao gerar dados dos gráficos', [
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao processar get graficos", 1);
        }

    }

    public static function getTotalizadores(Model $pesquisa)
    {
        try {
            $retorno = array();
            $i = 0;
            foreach ($pesquisa->configuracao->configuracao as $configuracao) {
                if (is_null($configuracao['tipo_dado'])) {
                    continue;
                }
                $valores_respostas =  $pesquisa->respostas()
                    ->get()
                    ->pluck('resposta.'.$configuracao['campo']);
                if (count($valores_respostas->toArray()) == count($valores_respostas->toArray(), COUNT_RECURSIVE)) {
                    $valores = $pesquisa->respostas()
                        ->when(request()->has('filtro'), function ($query) {
                            return $query->whereHas('usuario', function ($sql) {
                                return $sql->whereHas(request()->filtro);
                            });
                        })
                        ->get()
                        ->pluck('resposta.'.$configuracao['campo'])->countBy();
                } else {
                    $valores = $pesquisa->respostas()
                        ->when(request()->has('filtro'), function ($query) {
                            return $query->whereHas('usuario', function ($sql) {
                                return $sql->whereHas(request()->filtro);
                            });
                        })
                        ->get()
                        ->pluck('resposta.'.$configuracao['campo'])->collapse()->countBy();
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
        } catch (Throwable $th) {
            Log::error('Erro ao carregar os totalizadores', [
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao processar get totalizadores", 1);
        }
    }

    public static function getLabelPeloCampo(Model $pesquisa, string $campo, string $opcao) : string
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
        } catch (Throwable $th) {
            Log::error('Erro ao buscar label do campo', [
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao processar get label pelo campo", 1);
        }
    }

}
